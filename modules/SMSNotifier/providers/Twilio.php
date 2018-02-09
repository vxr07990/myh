<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * Twilio provider class for the SMSNotifier (outbound)
 *
 * Class handles sending an SMS.
 *
 * Currently it can:
 * 1) Send SMS to outbound phonenumbers from a phone number or MessagingServiceSid
 * 2) Check the status of a single SMS from Twilio, based on a SMSMessageID.
 *
 * @package: Twilio.php
 * @subpackage: SMSNotifier.php
 * @author: jgriffin@igcsoftware.com
 * @author: Alf
 * @link: https://www.twilio.com/docs/api/rest/sms
 * @link: https://www.twilio.com/docs/quickstart/php/sms
 * @link: https://twilio-php.readthedocs.org/en/latest/
 * @link: https://www.twilio.com/docs/api/twiml/sms/twilio_request
 * @NOTE: The twiml link doesn't mention SmsStatus (alias of Status) or SmsMessageSid (alias MessageSid) and NumSegments (if the msg was split up)
 * @TODO: check on this
 * @copyright: see vtig above, rest is IGCSoftware probably.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../models/ISMSProvider.php';
require_once('vendor/twilio/sdk/Services/Twilio.php');

class SMSNotifier_Twilio_Provider implements SMSNotifier_ISMSProvider_Model
{
    private $AccountSid; // Twilio uses AccountSid for this field
    private $AuthToken; // Twilio uses AuthToken for this field
    private $FromType; // Twilio has either From or MessagingServiceSid
    private $From; // this is either a telephone number of a MessagingServiceSid
    private $twilioServiceUrl = "https://messaging.twilio.com/v1/Services";

    const SERVICE_URI = 'http://localhost:9898';
    //private static $REQUIRED_PARAMETERS = array('AccountSid', 'AuthToken', 'From');
    //private static $REQUIRED_PARAMETERS = array('From', 'FromType');
    private static $REQUIRED_PARAMETERS = array('NothingNeedsEnteredHere');

    /**
     * Function to get provider name
     * @return <String> provider name
     */
    public function getName()
    {
        return 'Twilio';
    }

    /**
     * Function to get required parameters other than (AccountSid, AuthToken)
     * @return <array> required parameters list
     */
    public function getRequiredParams()
    {
        return self::$REQUIRED_PARAMETERS;
    }

    /**
     * Function to get service URL to use for a given type
     * @param <String> $type like SEND, PING, QUERY
     */
    public function getServiceURL($type = false)
    {
        if ($type) {
            switch (strtoupper($type)) {
                case self::SERVICE_AUTH:  return self::SERVICE_URI . '/http/auth';
                case self::SERVICE_SEND:  return self::SERVICE_URI . '/http/sendmsg';
                case self::SERVICE_QUERY: return self::SERVICE_URI . '/http/querymsg';
            }
        }
        return false;
    }

    /**
     * Function to set authentication parameters
     * @param <String> $username
     * @param <String> $password
     */
    public function setAuthParameters($username, $password)
    {
        //$this->AccountSid = $username;
        //$this->AuthToken = $password;
        $this->AccountSid = getenv('TWILIO_ACCOUNT_SID');
        $this->AuthToken = getenv('TWILIO_AUTH_TOKEN');
    }

    /**
     * Function to set non-auth parameter.
     * @param <String> $key
     * @param <String> $value
     */
    public function setParameter($key, $value)
    {
        if (
                ($key == 'to') ||
                ($key == 'from')
            ) {
            //we want to make sure when we have a phonenumber that it is only digits and doesn't have a county code
            $value = $this->__cleanPhoneNumbers($value);
        }
        $this->parameters[$key] = $value;
    }

    /**
     * Function to get parameter value
     * @param <String> $key
     * @param <String> $defaultValue
     * @return <String> value/$default value
     */
    public function getParameter($key, $defaultValue = false)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        return $defaultValue;
    }

    /**
     * Function to get the ownerID information
     * @return <Array> $rvOwner
     */
    public function getParentID()
    {
        //@TODO: I may be thinking of the wrong thing.
        //return $this->ownerInfo;
        return false;
    }

    /**
     * Function to prepare parameters
     * @return <Array> $params
     */
    private function prepareParameters()
    {
        $params = array('AccountSid' => $this->AccountSid, 'AuthToken' => $this->AuthToken);
        foreach (self::$REQUIRED_PARAMETERS as $key) {
            $params[$key] = $this->getParameter($key);
        }
        return $params;
    }

    /**
     * Function to handle SMS Send operation
     * @param <String> $message
     * @param <Mixed> $toNumbers One String or an Array of Strings
     * @return <Array> $results
     */
    public function send($message, $toNumbers, $recordAgentMap=null)
    {
        global $adb;
        $this->FromType = getenv('SMS_SENDER_TYPE');
        $this->From = getenv('SMS_SENDER');

        // so make a string/int into an array of string/int
        if (!is_array($toNumbers)) {
            $toNumbers = array($toNumbers);
        }

        $params = $this->prepareParameters();
        $params['body'] = $message;

        if ($this->FromType == 'Phonenumber' || $this->FromType == 'From') {
            $this->FromType = 'From';
            $this->From = '+' . $this->__sanitizeToNumber($this->From);  //this is just to make sure
        }
        //$params['to'] = implode(',', $toNumbers);

        $results = [];

        // @TODO: I'm not sure if Twilio will accept multiple numbers,
        // the wording is THE number you sent, but other wording seen
        // specifically says ONLY 1 of xxx can be sent.

        foreach ($toNumbers as $crmid=>$to) {
            $subaccountSid = $this->AccountSid;
            $subaccountToken = $this->AuthToken;
            $areaCode = '614'; //Default in case something goes wrong
            if($recordAgentMap) {
                //Get owner for record
                $ownerId     = $recordAgentMap[$crmid];
                $ownerRecord = Vtiger_Record_Model::getInstanceById($ownerId, 'AgentManager');
                $areaCode = substr($ownerRecord->get('phone1'),0,3);
                $parentOwnerRecord = Vtiger_Record_Model::getInstanceById($ownerRecord->get('vanline_id'), 'VanlineManager');
                $accountLookupResult = $adb->pquery("SELECT account_sid, auth_token FROM `twilio_accountmap` WHERE vanlinemanagerid=?", [$ownerRecord->get('vanline_id')]);
                if($accountLookupResult && $adb->num_rows($accountLookupResult) != 0) {
                    $subaccountSid = $accountLookupResult->fields['account_sid'];
                    $subaccountToken = $accountLookupResult->fields['auth_token'];
                }
                $sql = "SELECT service_sid FROM `twilio_servicemap` WHERE agentmanagerid=?";
                $lookupResult = $adb->pquery($sql, [$ownerId]);
                if($lookupResult && $adb->num_rows($lookupResult) > 0) {
                    $this->From = $lookupResult->fields['service_sid'];
                } elseif($lookupResult) {
                    if($accountLookupResult && $adb->num_rows($accountLookupResult) == 0) {
                        $friendlyName  = getenv('INSTANCE_NAME').' - '.$parentOwnerRecord->getDisplayName();
                        extract($this->createSubaccount($friendlyName, $ownerRecord->get('vanline_id')));
                    }

                    if($subaccountSid != $this->AccountSid) {
                        $friendlyName = getenv('INSTANCE_NAME').' - '.$ownerRecord->getDisplayName();
                        $this->From = $this->createMessagingService($subaccountSid, $subaccountToken, $friendlyName, $ownerId);
                    }
                }
            }
            $client = new Services_Twilio($subaccountSid, $subaccountToken);

            if($this->getMessagingServicePhoneNumberCount($subaccountSid, $subaccountToken, $this->From) == 0) {
                $this->purchaseNewPhoneNumberForService($client, $subaccountSid, $subaccountToken, $this->From, $areaCode);
            }
            //sanitize $to and ensure the +1 is set.
            if ($cleanTo = $this->__sanitizeToNumber($to)) {
                // @TODO: allow out of country numbers?
                $params['to'] = $cleanTo;

                try {
                    //file_put_contents('logs/devLog.log', "\nHERE1! : " . $this->FromType . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $this->From . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['to'] . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['body'] . " \n", FILE_APPEND);

                    //all examples use SendMessage helper instead, but this is correct
                    //We also need to add the + symbol per Twilio's requirements.
                    $message = $client->account->messages->create(array(
                        $this->FromType => $this->From,  //should be configurable from vtiger module config.
                        "To" => '+' . $params['to'],
                        "Body" => $params['body'],
                    ));

                    $result = array(
                                    'error' => false,
                                    'to' => '',
                                    'id' => '',
                                    'statusmessage' => ''
                                );

                    if ($message) {
                        //successfully queued
                        $result['id'] = $message->sid;
                        $result['to'] = $params['to'];
                        $result['status'] = self::MSG_STATUS_PROCESSING;
                        $result['statusmessage'] = $message->status;
                        //file_put_contents('logs/devLog.log',"SUCCESS 1: " . $message->sid . " \n", FILE_APPEND);

                        /*
                         * @NOTE: all the other variables we have access to if needed.
                         * @TODO: perhaps add a returnedparameters field to the status table and encode this to keep it all.
                         * [sid] => SM14a2d6b0bee04b1d9b4c9e87b7e83c5b
                         * [date_created] => Wed, 09 Dec 2015 18:57:58 +0000
                         * [date_updated] => Wed, 09 Dec 2015 18:57:58 +0000
                         * [date_sent] =>
                         * [account_sid] => ACd2ceb7159557f3f915e98cf71da6e22a
                         * [to] => +12064273687
                         * [from] => +15005550006
                         * [messaging_service_sid] =>
                         * [body] => Some message.
                         * [status] => queued
                         * [num_segments] => 1
                         * [num_media] => 0
                         * [direction] => outbound-api
                         * [api_version] => 2010-04-01
                         * [price] =>
                         * [price_unit] => USD
                         * [uri] => /2010-04-01/Accounts/ACd2ceb7159557f3f915e98cf71da6e22a/Messages/SM14a2d6b0bee04b1d9b4c9e87b7e83c5b
                         *
                         */
                    } else {
                        //all errors should go to the exception handler instead of here.
                        $result['error'] = true;
                        $result['to'] = $params['to'];
                        $result['statusmessage'] = 'No message was returned?';
                        //file_put_contents('logs/devLog.log',"ERROR 1 \n", FILE_APPEND);
                    }
                    $results[] = $result;
                } catch (Services_Twilio_RestException $e) {
                    //$results = [$e->getMessage()];
                    $result['error'] = true;
                    $result['to'] = $params['to'];
                    $result['statusmessage'] = $e->getMessage();
                    //file_put_contents('logs/devLog.log',"ERROR 2: " . $e->getMessage() . " \n", FILE_APPEND);

                    /**
                     * @NOTE: all the other variables we have access to if needed.
                     * @TODO: perhaps add a returnedparameters field to the status table and encode this to keep it all.
                     *
                     * print 'Status: (' . $e->getStatus() . ")\n";
                     * print 'Info: (' . $e->getInfo() . ")\n";
                     * print 'Code: (' . $e->getCode() . ")\n";
                     * print 'Msg: (' . $e->getMessage() . ")\n";
                     *
                     * [status:protected] => 400
                     * [info:protected] => https://www.twilio.com/docs/errors/21606
                     * [message:protected] => The From phone number +12064273687 is not a valid, SMS-capable inbound phone number or short code for your account.
                     * [string:Exception:private] =>
                     * [code:protected] => 21606
                     * [file:protected] => /Users/mm_jg/Desktop/project/dev-develop/vendor/twilio/sdk/Services/Twilio.php
                     * [line:protected] => 297
                     * [trace:Exception:private] => Array
                     */
                }

                /*
                 *	@NOTE: This is doing it manually, instead of using Twilio's php module
                $serviceURL = $this->getServiceURL(self::SERVICE_SEND);
                $httpClient = new Vtiger_Net_Client($serviceURL);
                $response = $httpClient->doPost($params);
                $responseLines = split("\n", $response);

                $results = array();
                foreach($responseLines as $responseLine) {
                    $responseLine = trim($responseLine);
                    if(empty($responseLine)) continue;

                    $result = array( 'error' => false, 'statusmessage' => '' );
                    if(preg_match("/ERR:(.*)/", trim($responseLine), $matches)) {
                        $result['error'] = true;
                        $result['to'] = $toNumbers[$i++];
                        $result['statusmessage'] = $matches[0]; // Complete error message
                    } else if(preg_match("/ID: ([^ ]+)TO:(.*)/", $responseLine, $matches)) {
                        $result['id'] = trim($matches[1]);
                        $result['to'] = trim($matches[2]);
                        $result['status'] = self::MSG_STATUS_PROCESSING;
                    } else if(preg_match("/ID: (.*)/", $responseLine, $matches)) {
                        $result['id'] = trim($matches[1]);
                        $result['to'] = $toNumbers[0];
                        $result['status'] = self::MSG_STATUS_PROCESSING;
                    }
                    $results[] = $result;
                }
                */
            } else {
                //invalid phone number.
                // @TODO: make some error message display?
                $result['error'] = true;
                $result['to'] = $to;
                $result['statusmessage'] = 'Invalid phone number';
            }
        } //end foreach of the input phone numbers
        return $results;
    }

    /**
     * Function to get the status of a message
     *
     * @return <Array> $results
     */
    public function getStatus()
    {
        //in order to corelate the twiml with the regular sent out..
        //we need to also check with smsstatus and messagestatus.

        $status = $this->getParameter('status');
        $smsstatus = $this->getParameter('smsstatus');
        $messagestatus = $this->getParameter('messagestatus');

        if ($smsstatus) {
            $status = $this->getParameter('smsstatus');
        } elseif ($messagestatus) {
            $status = $this->getParameter('messagestatus');
        }

        return $this->__getMessageStatus($status);
    }

    /**
     * Function to see if the message is sent to us or from us.
     *
     * @return <Bool> $rv (true means the message was sent to us.
     */
    public function sentToUs()
    {
        $rv = false;

        //@NOTE: This is simpler than expected because only the postbacks have
        //	smsstatus and it is only set to received when it's sent to our number
        //	... at least according to the docs.
        //	@link: https://www.twilio.com/docs/api/rest/message#sms-status-values
        //	@link: https://www.twilio.com/docs/api/twiml/sms/twilio_request
        //	The twiml link doesn't mention smsstatus.... but it's returned.
        $smsstatus = $this->getParameter('smsstatus');
        if ($smsstatus == 'received') {
            $rv = true;
        } else {
            //This is returned from callback I'm not sure if they are deprecating SmsStatus,
            //because they are deprecating SmsSid in favor or MessageSid,
            //so perhaps MessageStatus is the coming version... it's not in the link doc though.
            $messagestatus = $this->getParameter('messagestatus');
            if ($messagestatus == 'received') {
                $rv = true;
            }
        }

        return $rv;
        ;
    }

    /**
     * Function to implement query that pulls the status of a message using message id from Twilio
     *
     * @param <string> $messageId
     * @return <Array> $results
     */
    public function query($messageId)
    {
        //$params = $this->prepareParameters();
        $params['apimsgid'] = $messageId;

        $client = new Services_Twilio($this->AccountSid, $this->AuthToken);
        $result = array(
                        'error' => false,
                        'needlookup' => 1,
                        'status' => '',
                        'statusmessage' => ''
                    );

        try {
            //all examples use SendMessage helper instead, but this is correct
            $message = $client->account->messages->get($params['apimsgid']);
            $result = $this->__getMessageStatus($message->status);
            //file_put_contents('logs/devLog.log', "HERE1! : " . $params['apimsgid'] . " -- " . $message->status . " \n", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "HERE1 results! : " . print_r($result, 1) . " \n", FILE_APPEND);
        } catch (Services_Twilio_RestException $e) {
            //$results = [$e->getMessage()];
            // @TODO: need to consider the cases for this maybe it shouldn't lookup again?
            //$result['needlookup'] = 0;
            $result['error'] = true;
            $result['statusmessage'] = $e->getMessage();
        } catch (RestException $e) {
            //$results = [$e->getMessage()];
            // @TODO: need to consider the cases for this maybe it shouldn't lookup again?
            //$result['needlookup'] = 0;
            $result['error'] = true;
            $result['statusmessage'] = $e->getMessage();
        }

        /*
         *	@NOTE: This is doing it manually, instead of using Twilio's php module
        $serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
        $httpClient = new Vtiger_Net_Client($serviceURL);
        $response = $httpClient->doPost($params);
        $response = trim($response);

        $result = array( 'error' => false, 'needlookup' => 1 );

        if(preg_match("/ERR: (.*)/", $response, $matches)) {
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['statusmessage'] = $matches[0];
        } else if(preg_match("/ID: ([^ ]+) Status: ([^ ]+)/", $response, $matches)) {
            $result['id'] = trim($matches[1]);
            $status = trim($matches[2]);

            // Capture the status code as message by default.
            $result['statusmessage'] = "CODE: $status";
            if($status === '1') {
                $result['status'] = self::MSG_STATUS_PROCESSING;
            } else if($status === '2') {
                $result['status'] = self::MSG_STATUS_DISPATCHED;
                $result['needlookup'] = 0;
            }
        }
         */
        //file_put_contents('logs/devLog.log', "HERE2! : " . $params['apimsgid'] . " -- " . $result['statusmessage'] . " \n", FILE_APPEND);
        return $result;
    }

    public function createSubaccount($friendlyName, $ownerId) {
        global $adb;
        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $client = new Services_Twilio($sid, $token);
        $account = $client->accounts->create(array(
                                                 "FriendlyName" => $friendlyName
                                             ));
        $subSid = $account->sid;
        $subToken = $account->auth_token;

        $sql = "INSERT INTO `twilio_accountmap` (vanlinemanagerid, account_sid, auth_token) VALUES (?,?,?)";
        $adb->pquery($sql, [$ownerId, $subSid, $subToken]);

        return ['subaccountSid'=>$subSid, 'subaccountToken'=>$subToken];
    }

    public function createMessagingService($subSid, $subToken, $friendlyName, $ownerId) {
        global $adb;
        $callbackUrl = rtrim(getenv('SITE_URL'), '/').'/twiml.php';
        $postString = array("FriendlyName"=>$friendlyName, "StatusCallback"=>$callbackUrl, "InboundRequestUrl"=>$callbackUrl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->twilioServiceUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$subSid:$subToken");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        $serviceCreationResponse = json_decode($curlResult);
        $serviceSid = $serviceCreationResponse->sid;

        $sql = "INSERT INTO `twilio_servicemap` (agentmanagerid, service_sid) VALUES (?,?)";
        $adb->pquery($sql, [$ownerId, $serviceSid]);

        return $serviceSid;
    }

    public function getMessagingServicePhoneNumberCount($subSid, $subToken, $serviceSid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->twilioServiceUrl."/$serviceSid/PhoneNumbers");
        curl_setopt($ch, CURLOPT_USERPWD, "$subSid:$subToken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        $phoneLookupResponse = json_decode($curlResult);
        return count($phoneLookupResponse->phone_numbers);
    }

    public function purchaseNewPhoneNumberForService($client, $sid, $token, $serviceSid, $areaCode) {
        $numbers = $client->account->available_phone_numbers->getList('US', 'Local', ["AreaCode" => $areaCode, "SmsEnabled" => "true"]);
        $firstNumber = $numbers->available_phone_numbers[0];

        // Purchase the first number on the list.
        $twilioNumber = $client->account->incoming_phone_numbers->create(array("PhoneNumber" => $firstNumber->phone_number));

        $phoneSid = $twilioNumber->sid;

        // Attach the new phone number to the provided messaging service
        $postString = array("PhoneNumberSid"=>$phoneSid);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://messaging.twilio.com/v1/Services/$serviceSid/PhoneNumbers");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Function to ensure the telephone number is only digits with a preceding 1.
     *
     * @param: <String> $inputPhoneNumber
     * @return: <Integer> $cleanPhoneNumber
     */
    private function __sanitizeToNumber($inputPhoneNumber)
    {
        $cleanPhoneNumber = false;

        //check that there are only digits,
        $inputPhoneNumber = preg_replace('/[^0-9]/', '', $inputPhoneNumber);

        //assume North American phone number so just ensure there's a 1 on the front
        //add the plus symbol?  No just add that in the twilio send statement.

        // @TODO: should I have used substr?
        $checkForOne = preg_match('/^1/', $inputPhoneNumber);
        //print "checkForOne = $checkForOne\n";
        if ($checkForOne === false) {
            // an error occurred I guess this would mean the regexp is bad.
            $cleanPhoneNumber = false;
        } elseif ($checkForOne === 1) {
            //already has a one! hope for the best.
            $cleanPhoneNumber = $inputPhoneNumber;
        } else {
            //there is only no 1 on the front
            $cleanPhoneNumber = '1' . $inputPhoneNumber;
        }

        return $cleanPhoneNumber;
    }

    /**
     * Function clean phone numbers to "standard"
     *
     * @NOTE: standard means drop all non-int, and drop the preceeding 1,
     * @TODO: expand to drop the Country Code based on Twilio's FromCountry if rec'v OR the send user's address.
     *
     * @return: <int> $rv
     */
    private function __cleanPhoneNumbers($phonenumber)
    {
        $phonenumber = preg_replace('/[^0-9]/', '', $phonenumber);
        $phonenumber = preg_replace('/^1/', '', $phonenumber);
        return $phonenumber;
    }


    /**
     * Function to generate the variables associated with the returned status
     *
     * @param <String> $status
     * @return <Array> ($rvStatus, $rvNeedLookup, $rvError);
     */
    private function __getMessageStatus($status)
    {
        $result = array(
                        'statusmessage' => $status,
                        'status' => self::MSG_STATUS_PROCESSING,
                        'error' => false,
                        'needlookup' => 1
                        );

        /**
         *
         * @NOTE: status possible results are:
         * accepted - returned instead of queued when messageServiceSid is used, msg then goes to queue
         * queued	- The API request to send an SMS message was successful and the message is queued to be sent out.
         * sending	- Twilio is in the process of dispatching your message to the nearest upstream carrier in the SMS network.
         * sent		- The message was sent to the nearest upstream carrier, and that carrier accepted the message.
         * failed	- The message could not be sent, most likely because the "To" number is non-existent.
         * received	- On inbound messages only. The message was received by one of your Twilio numbers.
         *
         * this one is obvious but is not listed in their doc as a possible return results
         * delivered- the message was delivered to the phone number.
         *
         * flow: [accepted->]queued->sending->sent-><failed|delivered>
         */

        switch ($status) {
           case 'accepted':
                $result['status'] = self::MSG_STATUS_PROCESSING;
                break;
            case 'queued':
                $result['status'] = self::MSG_STATUS_PROCESSING;
                break;
            case 'sending':
                $result['status'] = self::MSG_STATUS_DISPATCHED;
                break;
            case 'sent':
                $result['status'] = self::MSG_STATUS_DISPATCHED;
                break;
            case 'delivered':
                $result['status'] = self::MSG_STATUS_DELIVERED;
                $result['needlookup'] = 0;
                break;
            case 'failed':
                $result['status'] = self::MSG_STATUS_FAILED;
                $result['error'] = true;
                $result['needlookup'] = 0;
                break;
            case 'received':
                //OK we should only be looking up SENT messages not rec'v ones so this is a failure
                $result['error'] = true;
                $result['needlookup'] = 0;
            default:
                break;
        }

        return $result;
    }
}
