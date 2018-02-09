<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../../includes/http/Request.php';
include_once dirname(__FILE__) . '/../ModComments/actions/SaveAjax.php';
include_once dirname(__FILE__) . '/SMSNotifierBase.php';

// @TODO: fix.. in order to get it to work i'm hacking this include list in.
include_once dirname(__FILE__) . '/../../include/Webservices/Relation.php';
include_once dirname(__FILE__) . '/../../vtlib/Vtiger/Module.php';
include_once dirname(__FILE__) . '/../../includes/main/WebUI.php';
include_once dirname(__FILE__) . '/../../libraries/htmlpurifier/library/HTMLPurifier.auto.php';
include_once 'include/Zend/Json.php';
require_once('vendor/twilio/sdk/Services/Twilio.php');

class SMSNotifier extends SMSNotifierBase
{
    //The admin id to dump the unknown receive messages to.
    private $adminID = 1;

    //I think we'll want a message in the content that says "SMS Message TO:" or "SMS Message From:"
    private $commentMessagePrefix = "SMS Message To:\n";
    private $recordAgentMap = [];

    //I need to know what the user is when we build a request to send to the comments.
    //@TODO: remove this when we make the SMSResponseHandler build a user object set $current_user to it;
    public $comment_assigned_user_id = 1;
    public $comment_userid = 1;

    /**
     * Check if there is active server configured.
     *
     * @return true if activer server is found, false otherwise.
     */
    public function checkServer()
    {
        $provider = SMSNotifierManager::getActiveProviderInstance();
        return ($provider !== false);
    }

    /**
     * Send SMS (Creates SMS Entity record, links it with related CRM record and triggers provider to send sms)
     *
     * @param String $message
     * @param Array $tonumbers
     * @param Integer $ownerid User id to assign the SMS record
     * @param mixed $linktoids List of CRM record id to link SMS record
     * @param String $linktoModule Modulename of CRM record to link with (if not provided lookup it will be calculated)
     *
     */
    public function sendsms($message, $tonumbers, $ownerid = false, $linktoids = false, $linktoModule = false)
    {
        global $current_user, $adb;

        if ($ownerid === false) {
            if (isset($current_user) && !empty($current_user)) {
                $ownerid = $current_user->id;
            } else {
                $ownerid = 1;
            }
        }
        $this->comment_assigned_user_id = $ownerid;
        $this->comment_userid = $ownerid;

        $moduleName = 'SMSNotifier';
        $focus = CRMEntity::getInstance($moduleName);

        $focus->column_fields['message'] = $message;
        $focus->column_fields['assigned_user_id'] = $ownerid;
        $focus->save($moduleName);

        /*
         * this is because it wasn't called as a module, i changed it to call as a module...
        $this->column_fields['message'] = $message;
        $this->column_fields['assigned_user_id'] = $ownerid;
        $this->save($moduleName);
         */

        if ($linktoids !== false) {
            if ($linktoModule !== false) {
                relateEntities($focus, $moduleName, $focus->id, $linktoModule, $linktoids);
                if($linktoModule == 'Cubesheets') {
                    //Lookup linked Order - if none exists, Opportunity
                    $recordModel = Vtiger_Record_Model::getInstanceById($linktoids, $linktoModule);
                    $orderId = $recordModel->get('cubesheets_orderid');
                    $oppId = $recordModel->get('potential_id');
                    $linkOwnerAgents = $adb->pquery("SELECT agentid FROM `vtiger_crmentity` WHERE crmid=?", [!empty($orderId) ? $orderId : $oppId]);
                    if($linkOwnerAgents) {
                        $this->recordAgentMap[$linktoids] = $linkOwnerAgents->fields['agentid'];
                    }
                } else {
                    $linkOwnerAgents = $adb->pquery("SELECT crmid,agentid FROM `vtiger_crmentity` WHERE crmid IN (".generateQuestionMarks($linktoids).")", [$linktoids]);
                    while ($linkrow =& $linkOwnerAgents->fetchRow()) {
                        $this->recordAgentMap[$linkrow['crmid']] = $linkrow['agentid'];
                    }
                }
            } else {
                // Link modulename not provided (linktoids can belong to mix of module so determine proper modulename)
                $linkidsetypes = $adb->pquery("SELECT setype,crmid,agentid FROM vtiger_crmentity WHERE crmid IN (".generateQuestionMarks($linktoids) . ")", array($linktoids));
                if ($linkidsetypes && $adb->num_rows($linkidsetypes)) {
                    while ($linkidsetypesrow = $adb->fetch_array($linkidsetypes)) {
                        relateEntities($focus, $moduleName, $focus->id, $linkidsetypesrow['setype'], $linkidsetypesrow['crmid']);
                        $this->recordAgentMap[$linkidsetypesrow['crmid']] = $linkidsetypesrow['agentid'];
                    }
                }
            }
        }
        $responses = $this->fireSendSMS($message, $tonumbers);
        $this->addToComments($this->commentMessagePrefix, $message, $tonumbers, $linktoids);
        $focus->processFireSendSMSResponse($responses);
    }

    /**
     * Detect the related modules based on the entity relation information for this instance.
     */
    public function detectRelatedModules()
    {
        global $adb, $current_user;

        // Pick the distinct modulenames based on related records.
        $result = $adb->pquery("SELECT distinct setype FROM vtiger_crmentity WHERE crmid in (
			SELECT relcrmid FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_crmentityrel.crmid
			WHERE vtiger_crmentity.crmid = ? AND vtiger_crmentity.deleted=0)", array($this->id));

        $relatedModules = array();

        // Calculate the related module access (similar to getRelatedList API in DetailViewUtils.php)
        if ($result && $adb->num_rows($result)) {
            require ('include/utils/LoadUserPrivileges.php');
            while ($resultrow = $adb->fetch_array($result)) {
                $accessCheck = false;
                $relatedTabId = getTabid($resultrow['setype']);
                if ($relatedTabId == 0) {
                    $accessCheck = true;
                } else {
                    if ($profileTabsPermission[$relatedTabId] == 0) {
                        if ($profileActionPermission[$relatedTabId][3] == 0) {
                            $accessCheck = true;
                        }
                    }
                }

                if ($accessCheck) {
                    $relatedModules[$relatedTabId] = $resultrow['setype'];
                }
            }
        }

        return $relatedModules;
    }

    protected function isUserOrGroup($id)
    {
        global $adb;
        $result = $adb->pquery("SELECT 1 FROM vtiger_users WHERE id=?", array($id));
        if ($result && $adb->num_rows($result)) {
            return 'U';
        } else {
            return 'T';
        }
    }

    protected function smsAssignedTo()
    {
        global $adb;

        // Determine the number based on Assign To
        $assignedtoid = $this->column_fields['assigned_user_id'];
        $type = $this->isUserOrGroup($assignedtoid);

        if ($type == 'U') {
            $userIds = array($assignedtoid);
        } else {
            require_once('include/utils/GetGroupUsers.php');
            $getGroupObj=new GetGroupUsers();
            $getGroupObj->getAllUsersInGroup($assignedtoid);
            $userIds = $getGroupObj->group_users;
        }

        $tonumbers = array();

        if (count($userIds) > 0) {
            $phoneSqlQuery = "select phone_mobile, id from vtiger_users WHERE status='Active' AND id in(". generateQuestionMarks($userIds) .")";
            $phoneSqlResult = $adb->pquery($phoneSqlQuery, array($userIds));
            while ($phoneSqlResultRow = $adb->fetch_array($phoneSqlResult)) {
                $number = $phoneSqlResultRow['phone_mobile'];
                if (!empty($number)) {
                    $tonumbers[] = $number;
                }
            }
        }

        if (!empty($tonumbers)) {
            $responses = $this->fireSendSMS($this->column_fields['message'], $tonumbers);
            $this->addToComments($this->commentMessagePrefix, $this->column_fields['message'], $tonumbers);
            $this->processFireSendSMSResponse($responses);
        }
    }

    private function processFireSendSMSResponse($responses)
    {
        if (empty($responses)) {
            return;
        }

        global $adb;

        foreach ($responses as $response) {
            $responseID = '';
            $responseStatus = '';
            $responseStatusMessage = '';

            $needlookup = 1;
            if ($response['error']) {
                $responseStatus = 'Failed';//ISMSProvider::MSG_STATUS_FAILED;
                $needlookup = 0;
            } else {
                $responseID = $response['id'];
                $responseStatus = $response['status'];
            }

            if (isset($response['statusmessage'])) {
                $responseStatusMessage = $response['statusmessage'];
            }
            $adb->pquery("INSERT INTO vtiger_smsnotifier_status(smsnotifierid,tonumber,status,statusmessage,smsmessageid,needlookup) VALUES(?,?,?,?,?,?)",
                array($this->id, $response['to'], $responseStatus, $responseStatusMessage, $responseID, $needlookup)
            );
        }
    }

    private function smsquery($record)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_smsnotifier_status WHERE smsnotifierid = ? AND needlookup = 1", array($record));
        if ($result && $adb->num_rows($result)) {
            $provider = SMSNotifierManager::getActiveProviderInstance();

            while ($resultrow = $adb->fetch_array($result)) {
                $messageid = $resultrow['smsmessageid'];

                $response = $provider->query($messageid);

                if ($response['error']) {
                    $responseStatus = 'Failed';//ISMSProvider::MSG_STATUS_FAILED;
                    $needlookup = $response['needlookup'];
                } else {
                    $responseStatus = $response['status'];
                    $needlookup = $response['needlookup'];
                }

                $responseStatusMessage = '';
                if (isset($response['statusmessage'])) {
                    $responseStatusMessage = $response['statusmessage'];
                }

                $adb->pquery("UPDATE vtiger_smsnotifier_status SET status=?, statusmessage=?, needlookup=? WHERE smsmessageid = ?",
                    array($responseStatus, $responseStatusMessage, $needlookup, $messageid));
            }
        }
    }

    private function fireSendSMS($message, $tonumbers)
    {
        global $log;
        $provider = SMSNotifierManager::getActiveProviderInstance();
        if ($provider) {
            return $provider->send($message, $tonumbers, $this->recordAgentMap);
        }
    }

    /**
     * Function to pull the sms status information for display to the user
     *
     * jgriffin: Modified to do an smsquery IF the record needs looked up to update the status.
     *
     * @param <Array> $record
     */
    public function getSMSStatusInfo($record)
    {
        global $adb;
        $results = array();
        $qresult = $adb->pquery("SELECT * FROM vtiger_smsnotifier_status WHERE smsnotifierid=?", array($record));
        if ($qresult && $adb->num_rows($qresult)) {
            while ($resultrow = $adb->fetch_array($qresult)) {
                if ($resultrow['needlookup'] == 1) {
                    $this->smsquery($resultrow['smsnotifierid']);
                    $tempResult = $adb->pquery("SELECT * FROM vtiger_smsnotifier_status WHERE smsnotifierid=?", array($resultrow['smsnotifierid']));
                    if ($tempResult && $adb->num_rows($tempResult)) {
                        $results[] = $adb->fetch_array($tempResult);
                    } else {
                        $results[] = $resultrow;
                    }
                } else {
                    $results[] = $resultrow;
                }
            }
        }
        return $results;
    }

    /**
     * Function to add this message to the comments thread for the "record"
     *
     * @param <Vtiger_Request object> $request
     */
    public function addToComments($messagePreface, $messageContent, $toNumbers, $linktoids = false)
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

        // so make a string/int into an array of string/int
        if (!is_array($linktoids)) {
            $linktoids = array($linktoids);
        }

        //if we don't have linktoids thne we have to generate them from the phoneNumbers input
        if (count($linktoids) <= 1) {
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
                    array_push($linktoids, $this->__getSenderID($to));
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

    /**
     * Function to get the sender's ID
     *
     * @param <Int> $phonenumber
     * @return <Int> $crmid
     */
    private function __getSenderID($phonenumber)
    {
        $crmid = false;
        /*
         * Search three tables for now.
         *
         *	account
         *		-> vtiger_account has | accountid | phone | otherphone

         *	lead
         *		-> vtiger_leadaddress has | leadaddressid | phone | mobile

         *	contact
         *		-> vtiger_contactdetails has: contactid | phone | mobile | donotcall
         *		-> vtiger_contactsubdetails has:  contactsubscriptionid | homephone | otherphone | assistant | assistantphone
         */

        //@TODO: probably want to move this to a foreach and break when found.
        //		that way we can reorder/add tables easier?  or pass it in...
        if ($result = $this->__searchTheTable($phonenumber, 'vtiger_leadaddress', array('phone', 'mobile'), 'leadaddressid')) {
            //$checkLeads = 'SELECT `leadaddressid` FROM `vtiger_leadaddress` WHERE (`phone` = ? OR `mobile` = ?) LIMIT 1';
            $crmid = $result;
        } elseif ($result = $this->__searchTheTable($phonenumber, 'vtiger_contactdetails', array('phone', 'mobile'), 'contactid')) {
            //$checkContactsOne = 'SELECT `contactid` FROM `vtiger_contactdetails` WHERE (`phone` = ? OR `mobile` = ?) LIMIT 1';
            $crmid = $result;
        } elseif ($result = $this->__searchTheTable($phonenumber, 'vtiger_contactsubdetails', array('homephone', 'otherphone', 'assistantphone'), 'contactsubscriptionid')) {
            //$checkContactsTwo = 'SELECT `contactsubscriptionid` FROM `vtiger_contactsubdetails` WHERE (`homephone` = ? OR `otherphone` = ? OR `assistantphone` = ?) LIMIT 1';
            $crmid = $result;
        } elseif ($result = $this->__searchTheTable($phonenumber, 'vtiger_account', array('phone', 'otherphone'), 'accountid')) {
            //$checkAccounts = 'SELECT `accountid` FROM `vtiger_accounts` WHERE (`phone` = ? OR `otherphone` = ?) LIMIT 1';
            $crmid = $result;
        } else {
            //unable to find the number so set CRMID to the admin for now
            //@TODO: This doesn't work, because the adminID is a user record and those are special.
            //$crmid = $this->adminID;
        }

        return $crmid;
    }

    /**
     * Function to look in a table for a phone number field to match a value and return an id
     *
     * @param <Int> $searchNumber
     * @param <String> $searchTable
     * @param <Array> $searchFields
     * @param <String> $idKey
     * @return $crmid
     */
    private function __searchTheTable($searchNumber, $searchTable, $searchFields, $idKey)
    {
        //using this instead of getInstance to keep sameness
        global $adb;
        $rv = false;

        $searchArray = array();
        $searchString = '';

        //build a search string to do the stuff here!
        foreach ($searchFields as $sf) {
            $searchString .= '`' . $adb->sql_escape_string($sf) . '` = ? OR ';
            array_push($searchArray, $searchNumber);
        }

        //remove the trailing ' OR '
        $searchString = preg_replace('/ OR $/', '', $searchString);

        $result = $adb->pquery('SELECT * FROM ' . $adb->escapeDbName($searchTable) . ' WHERE (' . $searchString . ') LIMIT 1', $searchArray);
        if ($result && $adb->num_rows($result)) {
            $resultrow = $adb->fetch_array($result);
            $rv = $resultrow[$idKey];
        }
        return $rv;
    }
}

class SMSNotifierManager
{

    /** Server configuration management */
    public static function listAvailableProviders()
    {
        return SMSNotifier_Provider_Model::listAll();
    }

    public static function getActiveProviderInstance()
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_smsnotifier_servers WHERE isactive = 1 LIMIT 1", array());
        if ($result && $adb->num_rows($result)) {
            $resultrow = $adb->fetch_array($result);
            $provider = SMSNotifier_Provider_Model::getInstance($resultrow['providertype']);
            $parameters = array();
            if (!empty($resultrow['parameters'])) {
                $parameters = Zend_Json::decode(decode_html($resultrow['parameters']));
            }
            foreach ($parameters as $k=>$v) {
                $provider->setParameter($k, $v);
            }
            $provider->setAuthParameters($resultrow['username'], $resultrow['password']);

            return $provider;
        }
        return false;
    }

    public static function listConfiguredServer($id)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_smsnotifier_servers WHERE id=?", array($id));
        if ($result) {
            return $adb->fetch_row($result);
        }
        return false;
    }
    public static function listConfiguredServers()
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_smsnotifier_servers", array());
        $servers = array();
        if ($result) {
            while ($resultrow = $adb->fetch_row($result)) {
                $servers[] = $resultrow;
            }
        }
        return $servers;
    }
    public static function updateConfiguredServer($id, $frmvalues)
    {
        global $adb;
        $providertype = vtlib_purify($frmvalues['smsserver_provider']);
        $username     = vtlib_purify($frmvalues['smsserver_username']);
        $password     = vtlib_purify($frmvalues['smsserver_password']);
        $isactive     = vtlib_purify($frmvalues['smsserver_isactive']);

        $provider = SMSNotifier_Provider_Model::getInstance($providertype);

        $parameters = '';
        if ($provider) {
            $providerParameters = $provider->getRequiredParams();
            $inputServerParams = array();
            foreach ($providerParameters as $k=>$v) {
                $lookupkey = "smsserverparam_{$providertype}_{$v}";
                if (isset($frmvalues[$lookupkey])) {
                    $inputServerParams[$v] = vtlib_purify($frmvalues[$lookupkey]);
                }
            }
            $parameters = Zend_Json::encode($inputServerParams);
        }

        if (empty($id)) {
            $adb->pquery("INSERT INTO vtiger_smsnotifier_servers (providertype,username,password,isactive,parameters) VALUES(?,?,?,?,?)",
                array($providertype, $username, $password, $isactive, $parameters));
        } else {
            $adb->pquery("UPDATE vtiger_smsnotifier_servers SET username=?, password=?, isactive=?, providertype=?, parameters=? WHERE id=?",
                array($username, $password, $isactive, $providertype, $parameters, $id));
        }
    }
    public static function deleteConfiguredServer($id)
    {
        global $adb;
        $adb->pquery("DELETE FROM vtiger_smsnotifier_servers WHERE id=?", array($id));
    }
}
