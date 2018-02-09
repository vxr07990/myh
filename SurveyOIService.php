<?php

/*
 * HTTP POST
 * Parameter name: sessionName
 * Parameter type: String
 * Parameter contents: valid Session Identifier for web service
 *
 * Parameter name: element
 * Parameter type: JSON
 * Parameter contents:
 * {
 *     userid: userid of CRM user to whom the Document should be assigned
 *     data: base64 encoded string with contents of file
 * }
 */

include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'customWebserviceFunctions.php';

if(!isset($_POST) || empty($_POST)) {
	$errCode = "NO_POST_DATA_FOUND";
	$errMessage = "No POST data was found in the request";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

if(!isset($_POST['sessionName'])) {
	$errCode = "MISSING_SESSIONID";
	$errMessage = "Session Identifier was not provided";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

if(!isset($_POST['element'])) {
	$errCode = "MISSING_ELEMENT";
	$errMessage = "Element information was not provided";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

$sessionId = $_POST['sessionName'];

$webserviceURL = getenv('WEBSERVICE_URL');

//Perform Describe operation in order to verify that Session Identifier is valid
$curlResult = curlGET("?operation=describe&sessionName=".$sessionId."&elementType=Documents", $webserviceURL);

$describeResult = json_decode($curlResult);

if($describeResult->success != 1) {
	die($curlResult);
}
//Session Identifier has been verified - proceed with element parameter check

$postdata = json_decode($_POST['element'], true);

if(!isset($_POST['data'])) {
	$errCode = "MISSING_REQ_PARAM";
	$errMessage = "Require parameter 'data' was not provided";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

if(!isset($_POST['userid'])) {
	$errCode = "MISSING_REQ_PARAM";
	$errMessage = "Required parameter 'userid' was not provided";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

$data_string = str_replace(' ', '+', $_POST['data']);

$userId = $_POST['userid'];

if(strpos($userId, 'x')) {
	$userId = substr($userId, strpos($userId, 'x'));
}

$db = PearDatabase::getInstance();

$sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
$params[] = $userId;

$result = $db->pquery($sql, $params);
unset($params);

$row = $result->fetchRow();

if($row == NULL) {
	$errCode = "USER_NOT_FOUND";
	$errMessage = "User does not exist in database";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}
//Verified that provided User ID is valid - proceed with base64 decoding of data

$decodedData = base64_decode($data_string, true);

if(!$decodedData) {
	$errCode = "INVALID_DATA";
	$errMessage = "Parameter 'data' contains invalid base64 encoding";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

if(substr($decodedData, 0, 5) != "<?xml") {
	$errCode = "INVALID_DATA";
	$errMessage = "File contents provided in parameter 'data' do not comply with XML standard";
	$response = json_encode(generateErrorArray($errCode, $errMessage));
	die($response);
}

$response = processXML($db, $decodedData, $userId, $sessionId);
die($response);


function processXML($db, $xmlFile, $userId, $sessionId) {
	$parser = xml_parser_create();
	$values = array();
	$index = array();
	xml_parse_into_struct($parser, $xmlFile, &$values, &$index);

	$responseToEmit = array();

	$customerInfo = array();

	$contactInfo = array();
	$currentContact = 0;
	$isContact = false;

	$activities = array();
	$currentActivity = 0;

	$originDetails = array();
	$isOrigin = false;

	$destinationDetails = array();
	$isDestination = false;

	$surveyDetails = array();

	foreach($values as $tagInfo) {
		if($tagInfo['tag'] == 'OICONTACT') {
			if($tagInfo['type'] == 'open') {
				$contactInfo[] = array();
				$isContact = true;
			}
			else {
				$currentContact++;
				$isContact = false;
			}
		}
		else if($tagInfo['tag'] == 'LASTNAME') {
			if($isContact) {
				$contactInfo[$currentContact]['lastname'] = $tagInfo['value'];
			}
			else {
				$customerInfo['lastname'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'FIRSTNAME') {
			if($isContact) {
				$contactInfo[$currentContact]['firstname'] = $tagInfo['value'];
			}
			else {
				$customerInfo['firstname'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'EMAIL') {
			$customerInfo['email'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'EMAIL1') {
			$contactInfo[$currentContact]['email1'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'EMAIL2') {
			$contactInfo[$currentContact]['email2'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'COMPANYNAME') {
			$customerInfo['companyname'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'COMPANYWEBSITE') {
			$customerInfo['companywebsite'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PRIMARYPHONENUMBER') {
			$customerInfo['primaryphonenumber'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PHONE1') {
			$contactInfo[$currentContact]['phone1'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PHONE2') {
			$contactInfo[$currentContact]['phone2'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'SURVEYCUSTOMERACTIVITY') {
			if($tagInfo['type'] == 'open') {
				$activities[] = array();
			}
			else {
				$currentActivity++;
			}
		}
		else if($tagInfo['tag'] == 'CREATIONDATE') {
			$activities[$currentActivity]['creationdate'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PICTUREURL') {
			$activities[$currentActivity]['pictureurl'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'SUCCESSFLAG') {
			$activities[$currentActivity]['successflag'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'STARTDATE') {
			$activities[$currentActivity]['startdate'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'ACTIVITYTYPE') {
			$activities[$currentActivity]['activitytype'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'NOTES') {
			$activities[$currentActivity]['notes'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'MESSAGE') {
			$activities[$currentActivity]['message'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'SUBJECT') {
			$activities[$currentActivity]['subject'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'ENDDATE') {
			$activities[$currentActivity]['enddate'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'SURVEYCUSTOMERACTIVITYID') {
			$activities[$currentActivity]['activityid'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'ORIGINSURVEYLOCATION') {
			if($tagInfo['type'] == 'open') {
				$isOrigin = true;
			}
			else {
				$isOrigin = false;
			}
		}
		else if($tagInfo['tag'] == 'DESTINATIONSURVEYLOCATION') {
			if($tagInfo['type'] == 'open') {
				$isDestination = true;
			}
			else {
				$isDestination = false;
			}
		}
		else if($tagInfo['tag'] == 'CITY') {
			if($isOrigin) {
				$originDetails['city'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['city'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'ZIP') {
			if($isOrigin) {
				$originDetails['zip'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['zip'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'STATE') {
			if($isOrigin) {
				$originDetails['state'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['state'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'COUNTY') {
			if($isOrigin) {
				$originDetails['county'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['county'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'ADDRESS1') {
			if($isOrigin) {
				$originDetails['address1'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['address1'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'ADDRESS2') {
			if($isOrigin) {
				$originDetails['address2'] = $tagInfo['value'];
			}
			else if($isDestination) {
				$destinationDetails['address2'] = $tagInfo['value'];
			}
		}
		else if($tagInfo['tag'] == 'LOADTO') {
			$surveyDetails['dates']['loadto'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'FOLLOWUP') {
			$surveyDetails['dates']['followup'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PACKFROM') {
			$surveyDetails['dates']['packfrom'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'NOPACK') {
			$surveyDetails['nopack'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'SURVEY') {
			$surveyDetails['dates']['survey'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'DECISION') {
			$surveyDetails['dates']['decision'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'LOADFROM') {
			$surveyDetails['dates']['loadfrom'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'DELIVERFROM') {
			$surveyDetails['dates']['deliverfrom'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'NODELIVER') {
			$surveyDetails['nodeliver'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'NOLOAD') {
			$surveyDetails['noload'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'PACKTO') {
			$surveyDetails['dates']['packto'] = $tagInfo['value'];
		}
		else if($tagInfo['tag'] == 'DELIVERTO') {
			$surveyDetails['dates']['deliverto'] = $tagInfo['value'];
		}
	}

	$accountId = getAccountId($db, $contactInfo['companyname']);

	if($accountId == NULL) {
		$accountInfo = array("accountname"=>$customerInfo['companyname'], "assigned_user_id"=>$userId, "website"=>$customerInfo['companywebsite']);
		$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($accountInfo)."&elementType=Accounts";
		$curlResult = curlPOST($post_string, $webserviceURL);

		$accountId = getAccountId($db, $customerInfo['companyname']);

		$responseToEmit['account'] = json_decode($curlResult, true);
	}
	else {
		//file_put_contents('logs/xmlprocess.log', date('Y-m-d H:i:s')." - AccountId for this company is ".$accountId."\n", FILE_APPEND);
		$curlResult = curlGET("?operation=retrieve&sessionName=".$sessionId."&id=".$accountId, $webserviceURL);
		$responseToEmit['account'] = json_decode($curlResult, true);
	}

	foreach($contactInfo as $index=>$info) {
		$contactId[$index] = getContactId($db, $info['firstname'], $info['lastname'], $accountId);

		if($contactId[$index] == NULL) {
			$contactInfo = array("assigned_user_id"=>$userId, "firstname"=>$info['firstname'], "lastname"=>$info['lastname'], "email"=>$info['email1'], "phone"=>$info['primaryphonenumber'], "account_id"=>$accountId);
			$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($contactInfo)."&elementType=Contacts";
			$curlResult = curlPOST($post_string, $webserviceURL);

			$contactId[$index] = getContactId($db, $info['firstname'], $info['lastname'], $accountId);

			$responseToEmit['contact'] = json_decode($curlResponse, true);
		}
		else {
			//file_put_contents('logs/xmlprocess.log', date('Y-m-d H:i:s')." - ContactId for this contact is ".$contactId."\n", FILE_APPEND);
			$curlResult = curlGET("?operation=retrieve&sessionName=".$sessionId."&id=".$contactId[$index], $webserviceURL);
			$responseToEmit['contact'.$index] = json_decode($curlResult, true);
		}
	}

	$opportunityId = getOpportunityId($db, $accountId, $contactId[0], $userId);

	if($opportunityId == NULL) {
		$opportunityInfo = array('assigned_user_id'=>$userId, 'related_to'=>$accountId,
								'potentialname'=>$customerInfo['companyname'], 'closingdate'=>date('Y-m-d', strtotime('+30 days')),
								'sales_stage'=>'Qualification', 'contact_id'=>$contactId, 'business_line'=>'Commercial Move',
								'pack_date'=>strstr($surveyDetails['dates']['packfrom'], 'T', true),
								'pack_to_date'=>strstr($surveyDetails['dates']['packto'], 'T', true),
								'load_date'=>strstr($surveyDetails['dates']['loadfrom'], 'T', true),
								'load_to_date'=>strstr($surveyDetails['dates']['loadto'], 'T', true),
								'deliver_date'=>strstr($surveyDetails['dates']['deliverfrom'], 'T', true),
								'deliver_to_date'=>strstr($surveyDetails['dates']['deliverto'], 'T', true),
								'survey_date'=>strstr($surveyDetails['dates']['survey'], 'T', true),
								'survey_time'=>substr($surveyDetails['dates']['survey'], strpos($surveyDetails['dates']['survey'], 'T')+1),
								'followup_date'=>strstr($surveyDetails['dates']['followup'], 'T', true),
								'decision_date'=>strstr($surveyDetails['dates']['decision'], 'T', true),
								'origin_address1'=>$originDetails['address1'], 'destination_address1'=>$destinationDetails['address1'],
								'origin_address2'=>$originDetails['address2'], 'destination_address2'=>$destinationDetails['address2'],
								'origin_city'=>$originDetails['city'], 'destination_city'=>$destinationDetails['city'],
								'origin_state'=>$originDetails['state'], 'destination_city'=>$destinationDetails['state'],
								'origin_zip'=>$originDetails['zip'], 'destination_zip'=>$destinationDetails['zip']);

		$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($opportunityInfo)."&elementType=Potentials";
		$curlResult = curlPOST($post_string, $webserviceURL);

		$opportunityId = getOpportunityId($db, $accountId, $contactId, $userId);

		$responseToEmit['opportunity'] = json_decode($curlResult, true);
	}
	else {
		//file_put_contents('logs/xmlprocess.log', date('Y-m-d H:i:s')." - OpportunityId is ".$opportunityId."\n", FILE_APPEND);
		$curlResult = curlGET("?operation=retrieve&sessionName=".$sessionId."&id=".$opportunityId, $webserviceURL);
		$responseToEmit['opportunity'] = json_decode($curlResult, true);
	}

	relateAdditionalContacts($db, $contactId, $opportunityId);

	$activityTypes = array(1=>"E-mail To", 2=>"E-mail From", 3=>"Text Message To", 4=>"Text Message From", 5=>"Phone Call To", 6=>"Phone Call From", 7=>"Sales Call", 8=>"Follow Up", 9=>"Timer", 10=>"Notes", 11=>"Photo", 12=>"Drawing", 13=>"Site Survey", 14=>"Walk Through", 15=>"Move Planning", 16=>"Move Day");

	foreach($activities as $activity) {
		if($activity['activitytype']+0 < 9) {
			$activityId = getActivityId($db, $contactId[0], $opportunityId, $userId, $activity['activityid']);

			if($activityId == NULL) {
				$start_date = strstr($activity['startdate'], 'T', true);
				$start_time = substr($activity['startdate'], strlen($start_date)+2, 8);
				$end_date = strstr($activity['enddate'], 'T', true);
				$end_time = substr($activity['enddate'], strlen($enddate)+2, 8);
				if($end_date == "1970-01-01") {
					$end_date = date('Y-m-d', strtotme($activity['startdate'])+300);
					$end_time = date('H:i:s', strtotime($activity['startdate'])+300);
				}
				$activityInfo = array("assigned_user_id"=>$userId, "subject"=>$activityTypes[$activity['activitytype']]." - ".$customerInfo['companyname'],
									  "date_start"=>$start_date, "time_start"=>$start_time,
									  "due_date"=>$end_date, "time_end"=>$end_time,
									  "duration_hours"=>0, "parent_id"=>$opportunityId, "activitytype"=>$activityTypes[$activity['activitytype']],
									  "description"=>$activity['notes'], "contact_id"=>$contactId[0], "eventstatus"=>"Planned", "visibility"=>"Private", "oiactivityid"=>$activity['activityid']);

				$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($activityInfo)."&elementType=Events";
				$curlResult = curlPOST($post_string, $webserviceURL);

				$activityId = getActivityId($db, $contactId[0], $opportunityId, $userId, $activity['activityid']);

				$responseToEmit['activities'][] = json_decode($curlResult, true);
			}
			else {
				$curlResult = curlGET("?operation=retrieve&sessionName=".$sessionId."&id=".$activityId, $webserviceURL);
				$responseToEmit['activities'][] = json_decode($curlResult, true);
			}
		}
	}

	return $responseToEmit;

	/*
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=describe&sessionName=".$sessionId."&elementType=Events");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
	$curlResult = curl_exec($ch);
	curl_close($ch);

	file_put_contents('logs/xmlprocess.log', print_r(json_decode($curlResult), true)."\n", FILE_APPEND);
	*/

	//file_put_contents('logs/xmlprocess.log', $filepath.": ".print_r($contactInfo, true)."\n".print_r($activities, true)."\n".print_r($originDetails, true)."\n".print_r($destinationDetails, true)."\n".print_r($surveyDetails, true)."\n".$jsonResponse->result->token."\n".$accesskey."\n", FILE_APPEND);
}

function getAccountId($db, $accountname) {
	$sql = "SELECT accountid FROM `vtiger_account` JOIN `vtiger_crmentity` ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE accountname=? AND deleted=0";
	$params[] = $accountname;

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL) {return NULL;}
	$objTypeId = getObjectTypeId("Accounts");
	return $objTypeId.$row[0];
}

function getContactId($db, $firstname, $lastname, $accountid) {
	$sql = "SELECT contactid FROM `vtiger_contactdetails` JOIN `vtiger_crmentity` ON vtiger_contactdetails.contactid=vtiger_crmentity.crmid WHERE accountid=? AND firstname=? AND lastname=? AND deleted=0";
	$params[] = substr($accountid, strpos($accountid, 'x')+1);
	$params[] = $firstname;
	$params[] = $lastname;

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL) {return NULL;}
	$objTypeId = getObjectTypeId("Contacts");
	return $objTypeId.$row[0];
}

function getOpportunityId($db, $accountId, $contactId, $assigned_user_id) {
	$sql = "SELECT potentialid FROM `vtiger_potential` JOIN `vtiger_crmentity` ON vtiger_potential.potentialid=vtiger_crmentity.crmid WHERE related_to=? AND contact_id=? AND smownerid=? AND deleted=0";
	$params[] = substr($accountId, strpos($accountId, 'x')+1);
	$params[] = substr($contactId, strpos($contactId, 'x')+1);
	$params[] = substr($assigned_user_id, strpos($assigned_user_id, 'x')+1);

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL) {return NULL;}
	$objTypeId = getObjectTypeId("Potentials");
	return $objTypeId.$row[0];
}

function relateAdditionalContacts($db, $contactIdList, $opportunityId) {
	foreach($contactIdList as $contactId) {
		$sql = "SELECT * FROM `vtiger_contpotentialrel` WHERE contactid=? AND potentialid=?";
		$params[] = $contactId;
		$params[] = $opportunityId;

		$result = $db->pquery($sql, $params);
		$row = $result->fetchRow();
		if($row == NULL) {
			$sql = "INSERT INTO `vtiger_contpotentialrel` VALUES (?,?)";

			$result = $db->pquery($sql, $params);
		}
		unset($params);
	}
}

function getActivityId($db, $contactId, $opportunityId, $assigned_user_id, $surveyActivityId) {
	$sql = "SELECT vtiger_activity.activityid FROM `vtiger_activity` JOIN `vtiger_crmentity` ON vtiger_activity.activityid=vtiger_crmentity.crmid
			JOIN `vtiger_cntactivityrel` ON vtiger_activity.activityid=vtiger_cntactivityrel.activityid
			JOIN `vtiger_seactivityrel` ON vtiger_activity.activityid=vtiger_seactivityrel.activityid
			JOIN `vtiger_activitycf` ON vtiger_activity.activityid=vtiger_activitycf.activityid
			WHERE vtiger_cntactivityrel.contactid=? AND vtiger_seactivityrel.crmid=? AND vtiger_crmentity.smownerid=? AND vtiger_activitycf.oiactivityid=? AND deleted=0";
	$params[] = substr($contactId, strpos($contactId, 'x')+1);
	$params[] = substr($opportunityId, strpos($opportunityId, 'x')+1);
	$params[] = substr($assigned_user_id, strpos($assigned_user_id, 'x')+1);
	$params[] = $surveyActivityId;

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL) {return NULL;}
	$objTypeId = getObjectTypeId("Events");
	return $objTypeId.$row[0];
}

function getObjectTypeId($db, $modName) {
	$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
	$params[] = $modName;

	$result = $db->pquery($sql, $params);

	return $db->query_result($result, 0, 'id').'x';
}
?>
