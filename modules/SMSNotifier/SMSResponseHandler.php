<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * @author: jgriffin@igcsoftware.com
 * @package: SMSResponseHandler
 * @subpackage: SMSNotifier
 * @copyright: above vtig and @TODO corp policy.
 ************************************************************************************/
include_once dirname(__FILE__) . '/SMSNotifier.php';
include_once 'include/Zend/Json.php';

class SMSResponseHandler extends SMSNotifier
{
    private $commentMessagePrefix = "SMS Message From:\n";

    /**
     * primary entry point that processes a notification sent ot us by an external SMS program
     *
     * @param <SMSRequest object> $request
     * @param <user object> $user
     * @return json status message
     */
    public function processResponder($request, $user)
    {
        //$user is useless to me because it's admin
        //request is useful but I'm not immediately seeing where it eats the element

        $postedValues = $request->getAll();
        /*
        file_put_contents('logs/devLog.log', "in process fucntion responese! : \n" .
        "##################################\n"
        //. "POST\n"
        //. "##################################\n"
        //. print_r($_POST, 1)
        //. "##################################\n"
        //. "postedValues\n"
        //. "##################################\n"
        //. print_r($postedValues, 1)
        . "##################################\n"
        . print_r($request, 1)
        . "##################################\n"
        . 'Body:' . $request->getRaw('Body')
        . 'From:' . $request->getRaw('From')
        . "##################################\n"
        . print_r($request->getRawKeys(), 1)
        . "\n##################################\n"
        . print_r($request->getKeys(), 1)
        . "\n##################################\n"
        , FILE_APPEND);
        //*/

        if ($this->checkServer()) {
            if (is_array($postedValues) && $postedValues > 0) {
                //@TODO: This is lacking what if they change providers?
                //	They could still get post backs from a prior provider.
                $provider = SMSNotifierManager::getActiveProviderInstance();

                /*
                 * @NOTE: This is an undesireable method
                $provider->setParameter('postedValues', json_encode($postedValues));

                foreach ($postedValues as $k => $v) {
                    $provider->setParameter(strtolower($k), $v);
                }
                */

                $provider->setParameter('postedValues', json_encode($postedValues));
                foreach ($request->getAll() as $k=>$v) {
                    $provider->setParameter(strtolower($k), $v);
                }

                if ($provider->sentToUs()) {

                    //store response for posterity;
                    $this->addResponse($provider);
                    //Good now half the battle is done!

                    //Next we have to add this to the comments thread.
                    $this->addToComments($this->commentMessagePrefix, $provider);
                } else {
                    // Sent from us all we need to do is update status record
                    $this->statusUpdate($provider->getParameter('messagesid'), $provider->getStatus());
                }
                return json_encode(array('success' => '1'));
            } else {
                // no posted data something broke?
                return json_encode(array('a' => '1', 'b'=>'2'));
                return false;
            }
        } else {
            //no provider is assigned.  We should handle the data next time.
            return json_encode(array('c' => '3', 'd'=>'4'));
            return false;
        }
    }

    /**
     * Function to update the status of the sms record we sent out
     *
     * @param <string> smsmessageid$
     * @param <Array> $response
     */
    public static function statusUpdate($smsmessageid, $record)
    {
        $db = PearDatabase::getInstance();

        //$provider = SMSNotifierManager::getActiveProviderInstance();
        //$smsmessageid = $provider->getParameter('smsmessageid');
        //$smsresponderid = $provider->getParameter('smsresponderid');

        //@TODO: add isset checks to make this strict compliant
        //if (is_array($record) && ($smsmessageid || $smsresponderid)) {
        if (is_array($record) && $smsmessageid) {
            $responseStatus = $record['status'];
            $responseStatusMessage = $record['statusmessage'];
            $needlookup = $record['needlookup'];
            $messageid = $smsmessageid;

            /* @TODO allow this switch maybe...
            if (!isset($smsmessageid)) {
                $messageid = $smsresponderid;
            }
             */

            if ($record['error']) {
                $responseStatus = 'Failed';//ISMSProvider::MSG_STATUS_FAILED;
                $needlookup = $record['needlookup'];
            } else {
                $responseStatus = $record['status'];
                $needlookup = $record['needlookup'];
            }

            $db->pquery("UPDATE vtiger_smsnotifier_status SET status=?, statusmessage=?, needlookup=? WHERE smsmessageid = ?",
                array($responseStatus, $responseStatusMessage, $needlookup, $messageid));
        }
    }

    /**
     * Function to create a respondant message
     * so the sms was sent to our number
     *
     * @param <provider object> $provider
     */
    private function addResponse($provider)
    {
        $db = PearDatabase::getInstance();

        //Make sure we have a crmid so we can tie it all together!
        if (!is_int($this->id)) {
            $this->id = $this->__getCRMId();
        }

        //retrieve the LAST user who sent this number a text and assign it to them.
        list($creatorid, $ownerid, $modifiedby) = $this->__findOwner($provider->getParameter('from'));
        $seed_user = new Users();
        $GLOBALS['current_user'] = $seed_user->retrieveCurrentUserInfoFromFile($ownerid);

        //@TODO: remove this when we make the SMSResponseHandler build a user object set $current_user to it;
        $this->comment_assigned_user_id = $ownerid;
        $this->comment_userid = $ownerid;

        // get the response status array
        $responseStatus = $provider->getStatus();

        // save the return message to a database for responses ONLY
        $stmt = 'INSERT INTO `vtiger_smsresponder` (smsresponderid, message, status) VALUES (?,?,?)';
        $db->pquery($stmt, array($this->id, $provider->getParameter('body'), $provider->getParameter('status')));

        // save the to the cf table.
        $db->pquery('INSERT INTO `vtiger_smsrespondercf` (smsresponderid) VALUES (?)', array($this->id));

        // save to the responder status table so it's close enough to the responder to reuse responder stuffs
        $db->pquery("INSERT INTO `vtiger_smsresponder_status` (smsresponderid,tonumber,fromnumber,status,statusmessage,smsmessageid,returnpost) VALUES(?,?,?,?,?,?,?)",
            array(
                $this->id,
                $provider->getParameter('to'),
                $provider->getParameter('from'),
                $responseStatus['status'],
                $responseStatus['statusmessage'],
                $provider->getParameter('messagesid'),
                $provider->getParameter('postedValues')
            )
        );

        // save the corresponding action to crmentity
        $db->pquery('INSERT INTO `vtiger_crmentity`
					(crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
					VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)',
                        array(
                            $this->id,                            //crmid
                            $creatorid,                            //smcreatorid
                            $ownerid,                            //smownerid
                            $modifiedby,                        //modifiedby
                            'SMSResponder',                        //setype
                            //'NOW()',							//createdtime
                            //'NOW()',							//modifiedtime
                            $provider->getParameter('body')        //label => body of the comment/message
                        )
                    );
    }

    /**
     * Function to add this message to the comments thread for the "record"
     *
     * @param <Vtiger_Request object> $request
     */
    public function addToComments($messagePreface, $provider, $linktoids = false)
    {
        /*
         * we can't thread it like i had hoped because there's just no threading in SMS there's no subject or header or whatever.
         * we need to get a ModComments object and pass it a vtiger_request to saveRecord and let it process like normal

            ##################################
            Vtiger_Request Object
            (
                [valuemap:Vtiger_Request:private] => Array
                    (
                        [__vtrftk] => sid:3c99ccaa7e1e4f0ca0f6a767009640d861750399,1450285464
                        [commentcontent] => Third comment added.
                        [related_to] => 431
                        [module] => ModComments
                        [action] => SaveAjax
                        [assigned_user_id] => 1
                        [userid] => 1
                    )

                [rawvaluemap:Vtiger_Request:private] => Array
                    (
                        [__vtrftk] => sid:3c99ccaa7e1e4f0ca0f6a767009640d861750399,1450285464
                        [commentcontent] => Third comment added.
                        [related_to] => 431
                        [module] => ModComments
                        [action] => SaveAjax
                    )

                [defaultmap:Vtiger_Request:private] => Array
                    (
                    )

            )
            ##################################
         *
         * OK so to find the related_to we need to search the tables holding phone numbers for a match and get the crmid from it.
         *
         * These have send SMS:
         * account -- lead -- contact
         *
         * @TODO: These do NOT have SMS send widgets:
         * agent -- opportunity --  vendors --  employees

         * @TODO: probably need to add sms send to these
         *	Agent, but not in list widget <-- handled by Contacts actually.

         *	opportunity, but not in list widget  this is actually linked to contacts so the comment would go through contacts for now.
         *		-> vtiger_potential has: origin_phone1_type | origin_phone2_type | destination_phone1_type | destination_phone2_type | origin_phone1_ext | origin_phone2_ext | destination_phone1_ext | destination_phone2_ext | days_to_move | enabled | contact_name | contact_email  | company_name | contact_phone

         *	vendors? I think this is being removed
         *		-> vtiger_vendor has phone

         *	Employees
         *		-> vtiger_employees has: employee_mphone | employee_hphone | employee_emphone | employee_ehphone

         *
         * OH if nobody is found we relate it to admin
        */

        $messageContent = $provider->getParameter('body');
        $toNumbers = [$provider->getParameter('from')];
        $serviceSid = $provider->getParameter('messagingservicesid');
        if($serviceSid == getenv('SMS_SENDER')) {
            //Incoming SMS should be discarded if sent to the default messaging service since we have no way to determine proper linkage
            //Send a reply SMS to inform the customer that their incoming SMS was not processed and to contact their sales rep directly
            $this->__logDiscardedIncomingSMS($messageContent, $provider->getParameter('from'));
            return false;
        }

        // so make a string/int into an array of string/int
        if($linktoids === false) {
            $linktoids = [];
        } elseif (!is_array($linktoids)) {
            $linktoids = array($linktoids);
        }

        //if we don't have linktoids then we have to generate them from the phoneNumbers input
        if (count($linktoids) < 1) {
            // so make a string/int into an array of string/int
            if (!is_array($toNumbers)) {
                $toNumbers = array($toNumbers);
            }

            foreach ($toNumbers as $to) {
                //ensure the $to is all digits,
                // @NOTE: I am unsure if I want to add this I mean the numbers should be checked in $this->sendsms() but they aren't, and the parts inputing to sendsms are checking for non-empty not valid number
                // @TODO: perhaps check for valid number before asking to send the sms
                $to = preg_replace('/[^0-9]/', '', $to);
                if ($to) {
                    //Lookup all records matching phone number that belong to agent to which messaging service SID is tied
                    $ownerAgent = $this->__getOwnerByServiceSid($serviceSid);
                    if(!$ownerAgent) {
                        $this->__logDiscardedIncomingSMS($messageContent, $provider->getParameter('from'));
                        return false;
                    }

                    $linktoids = $this->__lookupRecordsToLinkByOwner($provider->getParameter('from'), $ownerAgent);
                }
            }
        }
        $linkIDsToRelate = array();
        foreach ($linktoids as $tempToid) {
            if (!$tempToid) {
                continue;
            }
            $linkIDsToRelate[$tempToid] = 1;
        }


        foreach ($linkIDsToRelate as $linkID => $junk) {
            if ($linkID) {
                // Well what's the worst that can happen?
                $vt_request = new Vtiger_Request([]);
                $vt_request->set('commentcontent', $messagePreface . $messageContent);
                $vt_request->set('module', 'ModComments');
                $vt_request->set('action', 'SaveAjax');
                $vt_request->set('assigned_user_id', $this->comment_assigned_user_id);
                $vt_request->set('userid', $this->comment_userid);
                $vt_request->set('related_to', $linkID);

                // Well what's the worst that can happen?
                try {
                    //@NOTE: This doesn't catch fatal errors.
                    $commentObject = new ModComments_SaveAjax_Action();
                    $recordModel   = $commentObject->saveRecord($vt_request);
                } catch (Exception $e){
                    //@TODO: log an error maybe
                }
            }
        }

        return false;
    }

    private function __getOwnerByServiceSid($serviceSid) {
        global $adb;

        if($serviceSid == getenv('SMS_SENDER')) {
            return false;
        }

        $sql = "SELECT agentmanagerid FROM `twilio_servicemap` WHERE service_sid=?";
        $result = $adb->pquery($sql, [$serviceSid]);

        if(!$result || $adb->num_rows($result) == 0) {
            return false;
        }

        return $result->fields['agentmanagerid'];
    }

    private function __lookupRecordsToLinkByOwner($fromNumber, $ownerId) {
        global $adb;

        $crmids = [];
        //Query to check Contacts, Accounts, and Leads
        $sql = "(SELECT DISTINCT(crmid) 
                FROM `vtiger_crmentity` 
                JOIN `vtiger_contactdetails` ON `vtiger_crmentity`.crmid=`vtiger_contactdetails`.contactid
                WHERE ? IN (`vtiger_contactdetails`.phone, `vtiger_contactdetails`.mobile) AND agentid=?)
            UNION
                (SELECT DISTINCT(crmid)
                FROM `vtiger_crmentity`
                JOIN `vtiger_account` ON `vtiger_crmentity`.crmid=`vtiger_account`.accountid
                WHERE ? IN (`vtiger_account`.phone, `vtiger_account`.otherphone) AND agentid=?)
            UNION
                (SELECT DISTINCT(crmid)
                FROM `vtiger_crmentity`
                JOIN `vtiger_leadaddress` ON `vtiger_crmentity`.crmid=`vtiger_leadaddress`.leadaddressid
                JOIN `vtiger_leadscf` ON `vtiger_crmentity`.crmid=`vtiger_leadscf`.leadid
                WHERE ? IN (`vtiger_leadaddress`.phone, `vtiger_leadaddress`.mobile, `vtiger_leadscf`.origin_phone1, `vtiger_leadscf`.origin_phone2, `vtiger_leadscf`.destination_phone1, `vtiger_leadscf`.destination_phone2) AND agentid=?)";
        $result = $adb->pquery($sql, [$fromNumber, $ownerId, $fromNumber, $ownerId, $fromNumber, $ownerId]);

        while($row =& $result->fetchRow()) {
            $crmids[] = $row['crmid'];
        }

        return $crmids;
    }

    private function __logDiscardedIncomingSMS($messageContent, $fromNumber) {
        if(!Vtiger_Utils::CheckTable('twilio_unlinkedincoming')) {
            Vtiger_Utils::CreateTable('twilio_unlinkedincoming',
                                      '(
                                          `from_number` int(10) NOT NULL,
                                          `message` TEXT,
                                          `received_time` DATETIME', true);
        }

        global $adb;
        $sql = "INSERT INTO `twilio_unlinkedincoming` (`from_number`, `message`, `received_time`) VALUES (?,?,NOW())";
        $adb->pquery($sql, [$fromNumber, $messageContent]);

        $provider = SMSNotifierManager::getActiveProviderInstance();
        if ($provider) {
            $message = "Unable to process incoming SMS request. Please contact your move representative directly.";
            $provider->send($message, [$fromNumber]);
        }
    }

    /**
     * Function to find the owner of an sms sent ot us
     *
     * @NOTE this will have limitations,
     * If more than one person sent them an SMS then only the last person will be assigned as owner.
     * iff a message was sent we can find it in the vtiger_smsnotifier_status table
     * Otherwise we have to search in the lead, accounts, opportunity...
     * We're just going to add it to an "unowned" message queue perhaps the agent admin or a higher up admin who can assign it out?
     *
     * @return <Array> ($creatorid, $ownerid, $modifiedby)
     */
    private function __findOwner($phoneNumber)
    {
        $db = PearDatabase::getInstance();
        $ownerid = false;

        $result = $db->pquery('SELECT * FROM `vtiger_smsnotifier_status` WHERE `tonumber` = ? ORDER by `smsnotifierid` DESC LIMIT 1', array('1'.$phoneNumber));
        if ($result && $db->num_rows($result)) {
            $resultrow = $db->fetch_array($result);
            $crmResult = $db->pquery('SELECT * FROM `vtiger_crmentity` WHERE `crmid` = ? LIMIT 1', array($resultrow['smsnotifierid']));
            if ($crmResult && $db->num_rows($crmResult)) {
                $crmResultrow = $db->fetch_array($crmResult);
                $creatorid = $crmResultrow['smcreatorid'];
                $ownerid = $crmResultrow['smownerid'];
                $modifiedby = $crmResultrow['modifiedby'];
            }
        }

        if (!$ownerid) {
            //@TODO: FIX this because it's stupid hack.
            //assign the ownerid to something we can pull from teh table...
            //for now it's admin

            $creatorid = 1;
            $ownerid = 1;
            $modifiedby = 1;
        }

        return array($creatorid, $ownerid, $modifiedby);
    }

    /**
     * Function to increment and return the CRM ID
     *
     * @NOTE: this is apparently the practice of vtiger...
     *
     * @return <int> $crmid
     */
    private function __getCRMId()
    {
        $db = PearDatabase::getInstance();

        $sql = "UPDATE `vtiger_crmentity_seq` SET `id`=LAST_INSERT_ID(`id`+1) LIMIT 1";
        $db->pquery($sql);

        //$sql = "SELECT id FROM `vtiger_crmentity_seq` LIMIT 1";
        $sql = "SELECT LAST_INSERT_ID() AS `id`";
        $result = $db->pquery($sql);

        return $db->query_result($result, 0, 'id');
    }
}
