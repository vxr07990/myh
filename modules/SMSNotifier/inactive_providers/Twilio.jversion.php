<?php
//@TODO: ask regarding accepted practices for the require/include.
require_once('/vagrant/vendor/twilio/sdk/Services/Twilio.php');

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class SMSNotifier_Twilio_Provider implements SMSNotifier_ISMSProvider_Model
{

    // @TODO: utilize the SMSNotifier Configuration for the From number
    // must allow for alternate number pool identifier which I cn't recall offhand
    // use the username and password for the AccountSid and AuthToken
    private $AccountSid; // Twilio uses AccountSid for this field
    private $AuthToken; // Twilio uses AuthToken for this field

    const SERVICE_URI = 'http://localhost:9898';
    //private static $REQUIRED_PARAMETERS = array('AccountSid', 'AuthToken', 'From');
    private static $REQUIRED_PARAMETERS = array('From', 'FromType');

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
        $this->AccountSid = $username;
        $this->AuthToken = $password;
    }

    /**
     * Function to set non-auth parameter.
     * @param <String> $key
     * @param <String> $value
     */
    public function setParameter($key, $value)
    {
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
     * Function to prepare parameters
     * @return <Array> $params
     */
    protected function prepareParameters()
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
     * @param <Mixed> $toNumbers One or Array of numbers
     * @return <Array> $results
     */
    public function send($message, $toNumbers)
    {

        // so make a string/int into an array of string/int
        if (!is_array($toNumbers)) {
            $toNumbers = array($toNumbers);
        }

        $params = $this->prepareParameters();
        $params['Body'] = $message;

        if ($params['FromType'] == 'Phonenumber' || $params['FromType'] == 'From') {
            $params['FromType'] = 'From';
            $params['From'] = '+' . $this->__sanitizeToNumber($params['From']);  //this is just to make sure
        }
        //$params['To'] = implode(',', $toNumbers);

        $client = new Services_Twilio($this->AccountSid, $this->AuthToken);
        $results = [];

        // @TODO: I'm not sure if Twilio will accept multiple numbers,
        // the wording is THE number you sent, but other wording seen
        // specifically says ONLY 1 of xxx can be sent.

        foreach ($toNumbers as $to) {
            //sanitize toNumber and ensure the +1 is set.
            if ($cleanTo = $this->__sanitizeToNumber($to)) {
                // @TODO: allow out of country numbers?
                $params['To'] = $cleanTo;

                try {
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['FromType'] . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['From'] . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['To'] . " \n", FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "HERE1! : " . $params['Body'] . " \n", FILE_APPEND);

                    //all examples use SendMessage helper instead, but this is correct
                    //We also need to add the + symbol per Twilio's requirements.
                    $message = $client->account->messages->create(array(
                        $params['FromType'] => $params['From'],  //should be configurable from vtiger module config.
                        "To" => '+' . $params['To'],
                        "Body" => $params['Body'],
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
                        $result['to'] = $params['To'];
                        $result['status'] = self::MSG_STATUS_PROCESSING;
                        //file_put_contents('logs/devLog.log',"SUCCESS 1: " . $message->sid . " \n", FILE_APPEND);

                        /*
                         * @NOTE: all the other variables we have access to if needed.
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
                        //file_put_contents('logs/devLog.log',"ERROR 1 \n", FILE_APPEND);
                        //all errors should go to the exception handler instead of here.
                        $result['error'] = true;
                        $result['to'] = $params['To'];
                        $result['statusmessage'] = 'No message was returned?';
                    }
                    $results[] = $result;
                } catch (Services_Twilio_RestException $e) {
                    //$results = [$e->getMessage()];
                    $result['error'] = true;
                    $result['to'] = $params['To'];
                    $result['statusmessage'] = $e->getMessage();
                    //file_put_contents('logs/devLog.log',"ERROR 2: " . $e->getMessage() . " \n", FILE_APPEND);

                    /**
                     * @NOTE: all the other variables we have access to if needed.
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
                 *	@NOTE: this is attempting to do the work manually...
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
     * Function to get query for status using message id
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

            $result['statusmessage'] = $message->status;
            switch ($message->status) {
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
                    break;
                default:
                    break;
            }
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

    /**
     * Function to ensure the telephone number is only digits with a preceding 1.
     *
     * @param: <String> $inputPhoneNumber
     * @return: <Integer> $cleanPhoneNumber
     */
    public function __sanitizeToNumber($inputPhoneNumber)
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
}
