<?php
/**
 * Webhook for Twilio responses.
 *
 * @requires:
 *	insert into `vtiger_ws_operation` set `name` = 'twilio.receiver', `handler_path` = 'modules/SMSNotifier/smsresponse.php', `handler_method` = 'sms_response_process', `type` = 'POST', `prelogin` = '0';
 *
 * Twilio responses are called TwiML and are sent from them to a predetermined callback URL.
 * The callback url is set via their account management page, you can base them on a number
 * of rules.
 * @link: https://www.twilio.com/user/account/
 * @NOTE: You can configure for the phone number, for the messaging service or for a TwiML app.
 *
 * For Messaging Service AND TwiML App (optional settings)
 * Set: Status Callback URL to be the web accessible location of this script using HTTP POST
 *
 * For All three Phonenumber and Messaging Service and TwiML App
 * Set: Request URL to be the web accessible location of this script using HTTP POST
 *
 * First step is to validate they sent the message.
 *
 * Second step:
 * IFF it was an outbound message
 *		just update the message's status in the table
 * ELSE //it is a message inbound to us
 *		IF the message is STOP
 *			Block the number for SMS, note twilio does block them, so sending to them again would produce an error.
 *		ELSE IF the message is START
 *			Remove the sms block for the number.
 *		ELSE IF the message is HELP
 *			It is not clear that we get this or if Twilio just returns the response
 *		ELSE
 *			relate it to a sent message
 *			Insert message record to database
 *
 * Third step:
 *	Return appropriate response to Twilio accepting the notice.
 *
 * @usage: setup a callback url in twilio setup that is only using POST
 * @author: jgriffin@igcsoftware.com
 * @link: https://www.twilio.com/docs/api/twiml
 * @link: https://twilio-php.readthedocs.org/en/latest/usage/twiml.html
 * @link: https://www.twilio.com/docs/quickstart/php/sms/sending-via-twiml
 * @TODO: add the corporate standard copyright
 * @copyright: add the corporate standard copyright
 */

/**
 *
 * STOP, STOPALL, UNSUBSCRIBE, CANCEL, END, and QUIT will stop customers from receiving messages from that Twilio number. Only single-word messages will trigger the block. Further, the block works and is logged even if your number currently has no messaging request URL.
 * START and YES will opt customers back in to the messages coming from your Twilio phone number, and these messages will also be delivered to your Twilio account so you can update your application logic.
 * HELP and INFO will return a message informing the customer that they can use the above commands to control the delivery of messages.
 *
 */

{
    require_once('config.php');
    include_once('vendor/twilio/sdk/Services/Twilio.php');
    include_once('include/Webservices/Relation.php');
    include_once('vtlib/Vtiger/Module.php');
    include_once('includes/main/WebUI.php');
    include_once('customWebserviceFunctions.php');
    require_once('modules/SMSNotifier/smsresponse.php');

    /************************************
     *
     * let's start with what they say they return:
     * @link: https://www.twilio.com/docs/api/twiml
     *
     * PARAMETER -- DESCRIPTION
     * MessageSid -- A 34 character unique identifier for the message. May be used to later retrieve this message from the REST API.
     * SmsSid -- Same value as MessageSid. Deprecated and included for backward compatibility.
     * AccountSid -- The 34 character id of the Account this message is associated with.
     * MessagingServiceSid -- The 34 character id of the Messaging Service associated to the message.
     * From -- The phone number that sent this message.
     * to -- The phone number of the recipient.
     * Body -- The text body of the message. Up to 1600 characters long.
     * NumMedia -- The number of media items associated with your message
     *
     * PARAMETER -- DESCRIPTION
     * MediaContentType{N} ContentTypes for the Media stored at MediaUrl{N}. The order of MediaContentType{N} matches the order of MediaUrl{N}. If more than one media element is indicated by NumMedia than MediaContentType{N} will be used, where N is the count of the Media
     * MediaUrl{N} URL referencing the content of the media received in the Message. If more than one media element is indicated by NumMedia than MediaUrl{N} will be used, where N is the count of the Media
     *
     * PARAMETER -- DESCRIPTION
     * FromCity -- The city of the sender
     * FromState -- The state or province of the sender.
     * FromZip -- The postal code of the called sender.
     * FromCountry -- The country of the called sender.
     * ToCity -- The city of the recipient.
     * ToState -- The state or province of the recipient.
     * ToZip -- The postal code of the recipient.
     * ToCountry -- The country of the recipient.
     *
     * I would bet this is not all the parameters and possibly not the exact wording "to" vs "To" ?
     * I was right there are other return params and "to" is in fact "To"
     *
     * Example return results:
     *
     ************************************
     * POSTED values from a CALLBACK URL  <-- comes from a messaging service UNLESS you set StatusCallback with your send request (i think)
     *
     * [SmsSid]					=> SM77d98c452b4040e7b0940e2ba905a4a3
     * [MessageSid]				=> SM77d98c452b4040e7b0940e2ba905a4a3
     *
     * [SmsStatus]				=> delivered
     * [MessageStatus]			=> delivered
     *
     * [To]						=> +12064273687
     * [From]					=> +12568261088
     *
     * [AccountSid]				=> AC7f1329630eefae59504c54719f785e70
     * [MessagingServiceSid]	=> MG82cd1fc7ddc55c941d80189d6327dc08
     *
     * [ApiVersion]				=> 2010-04-01
     *
     ***********************************
     *
     * POSTED values from a REQUEST URL  <-- you need this set in the twilio manager in order to recv msg to a server.
     *
     * [SmsSid]					=> SM2853a88e7afab665fb7d3194fc66c424
     * [SmsMessageSid]			=> SM2853a88e7afab665fb7d3194fc66c424
     * [MessageSid]				=> SM2853a88e7afab665fb7d3194fc66c424
     *
     * [AccountSid]				=> AC7f1329630eefae59504c54719f785e70
     * [MessagingServiceSid]	=> MG82cd1fc7ddc55c941d80189d6327dc08
     *
     * [To]						=> +12568261088
     * [ToCity]					=>
     * [ToState]				=> AL
     * [ToCountry]				=> US
     * [ToZip]					=>
     *
     * [Body]					=> test auto responder
     * [NumSegments]			=> 1
     * [NumMedia]				=> 0
     *
     * [SmsStatus]				=> received
     *
     * [From]					=> +12064273687
     * [FromCity]				=> SEATTLE
     * [FromState]				=> WA
     * [FromZip]				=> 98154
     * [FromCountry]			=> US
     *
     * [ApiVersion]				=> 2010-04-01
     ***********************************/

    if (!isset($_POST) || empty($_POST)) {
        $errCode = "NO_POST_DATA_FOUND";
        $errMessage = "No POST data was found in the request";
        $response = json_encode(generateErrorArray($errCode, $errMessage));
    } else {
        if (validateIncoming()) {


            /*
            file_put_contents('logs/devLog.log', "TwiMl.php responese! : \n" .
                                                "##################################\n"
                                                . print_r($_SERVER, 1)
                                                . print_r($_POST, 1)
                                                . "##################################\n"
                                                , FILE_APPEND);
             //*/

            $smsResponderModel = Vtiger_Module_Model::getInstance('SMSResponder');

            if ($smsResponderModel && $smsResponderModel->isActive()) {
                $db = PearDatabase::getInstance();
                $ObjectId = getObjectTypeId($db, "SMSResponder");

                /**********************************
                 * Do Work here,
                 **********************************/

                global $current_user;
                $responderResponse = sms_response_process($current_user);

                //Actually we need to responde with XML
                $results = json_decode($responderResponse, true);
                $interiorXML = '';
                if ($results['success'] === true) {
                    //$interiorXML = '<Error></Error>';
                    // DO NOTHING on response
                    // @TODO: possibly feature expansion we can change this to have an auto-responder if we want
                } else {
                    $interiorXML = '<Error>Failed Validations</Error>';
                }
/* they expect ths for a reply response
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <Message to="+14150001111" from="+14152223333">Hello World</Message>
</Response>
//this is for do nothign
<?xml version="1.0" encoding="UTF-8"?>
<Response>
</Response>
*/
            print <<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<Response>
$interiorXML
</Response>
RESPONSE;

                /**********************************
                 * End Do Work here,
                 **********************************/
            }
        } else {
            print <<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <Error>Failed Validations</Error>
</Response>
RESPONSE;
        }
    }
    exit();
}

    /**
     * Function to ensure Twilio sent it to us
     *
     * @return: <bool> $rv
     */
    function validateIncoming()
    {
        global $adb;
        $accountSid = $_POST['AccountSid'];
        $sql = "SELECT auth_token FROM `twilio_accountmap` WHERE account_sid=?";
        $result = $adb->pquery($sql, [$accountSid]);
        if($result && $adb->num_rows($result) > 0) {
            $authToken = $result->fields['auth_token'];
        } else {
            $authToken = getenv('TWILIO_AUTH_TOKEN');
        }
        $rv = false;
        //build the full url of the script... presuming this was what's set in the Twilio Config
        $url = 'http'
            . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? 's' : '')
            . '://'
            . $_SERVER['HTTP_HOST']
            . $_SERVER['REQUEST_URI'];

        //drop all of the trailing slashs if there are any at least.
        $url = preg_replace('/\/+$/', '', $url);

        //print "<br />URL IS $url<br />";

        //initialize validator module
        $validator = new Services_Twilio_RequestValidator($authToken);
        if ($validator && $_SERVER["HTTP_X_TWILIO_SIGNATURE"] && $url && $_POST) {
            if ($validator->validate($_SERVER["HTTP_X_TWILIO_SIGNATURE"], $url, $_POST)) {
                //echo "Confirmed to have come from Twilio.";
                $rv = true;
            } else {
                //echo "NOT VALID. It might have been spoofed!";
            }
        }
        return $rv;
        /*
         */
        return true;
    }

    /**
     * Function to get the entity name.
     *
     * @return: <bool> $rv
     */
    function getObjectTypeId($db, $modName)
    {
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);
        unset($params);

        return $db->query_result($result, 0, 'id').'x';
    }
