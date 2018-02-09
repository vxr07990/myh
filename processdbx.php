<?php
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
use \Dropbox as dbx;

file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - Inside of dbxprocess\n", FILE_APPEND);

if(isset($_POST) && !empty($_POST)) {
	//file_put_contents('logs/dbxprocess.log', $_POST."\n", FILE_APPEND);
} else {
	//file_put_contents('logs/dbxprocess.log', "No POST data found!\n", FILE_APPEND);
	die("No POST data found!");
}

if(!file_exists('dbx')) {mkdir('dbx');}

foreach($_POST as $userId=>$token) {
	$dbxClient = new dbx\Client($token, "reloCRM");

	$delta = $dbxClient->getDelta();

	file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - After getDelta function\n", FILE_APPEND);

	foreach($delta["entries"] as $entry) {
		list($lcPath, $metadata) = $entry;
		$fileExtension = substr($lcPath, strlen($lcPath)-4);
		if(strcasecmp($fileExtension, '.xml') != 0) {continue;}

		$f = fopen("dbx".$lcPath, "w+b") or file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - Unable to open file!\n", FILE_APPEND);
		$fileMetadata = $dbxClient->getFile($lcPath, $f);
		fclose($f);
		$deleteResponse = $dbxClient->delete($lcPath);
		processXML("dbx".$lcPath, $userId);
		unlink("dbx".$lcPath);
	}
}

function fetchCursor($userId) {
	$db = PearDatabase::getInstance();

	$sql = "SELECT dbx_cursor FROM `vtiger_users` WHERE dbx_userid=?";
	$params[] = $userId;

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL || $row[0] == '') {return null;}

	return $row[0];
}

function writeCursor($userId, $cursor) {
	$db = PearDatabase::getInstance();

	$sql = "UPDATE `vtiger_users` SET dbx_cursor=? WHERE dbx_userid=?";
	$params[] = $cursor;
	$params[] = $userId;

	$result = $db->pquery($sql, $params);
}

function processXML($filepath, $dbxUserId) {
	$xmlFile = file_get_contents($filepath);

	$parser = xml_parser_create();
	$values = array();
	$index = array();
	xml_parse_into_struct($parser, $xmlFile, &$values, &$index);

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

	$db = PearDatabase::getInstance();

	$sql = "SELECT id, user_name, accesskey FROM `vtiger_users` WHERE dbx_userid=?";
	$params[] = $dbxUserId;

	$result = $db->pquery($sql, $params);
	unset($params);

	$row = $result->fetchRow();

	$id = $row[0];
	$userName = $row[1];
	$accesskey = $row[2];

	$webserviceURL = getenv('WEBSERVICE_URL');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$userName);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
	$curlResult = curl_exec($ch);
	curl_close($ch);

	$challengeResponse = json_decode($curlResult);

	$generatedkey = md5($challengeResponse->result->token.$accesskey);
	$post_string = "operation=login&username=".$userName."&accessKey=".$generatedkey;

	$curlResult = curlPOST($post_string, $webserviceURL);

	$loginResponse = json_decode($curlResult);

	$sessionId = $loginResponse->result->sessionName;
	$crmUserId = $loginResponse->result->userId;

	$accountId = getAccountId($db, $customerInfo['companyname']);

	if($accountId == NULL) {
		$accountInfo = array("accountname"=>$customerInfo['companyname'], "assigned_user_id"=>$crmUserId, "website"=>$customerInfo['companywebsite']);
		$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($accountInfo)."&elementType=Accounts";
		$curlResult = curlPOST($post_string, $webserviceURL);

		$accountId = getAccountId($db, $customerInfo['companyname']);
	}
	else {
		file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - AccountId for this company is ".$accountId."\n", FILE_APPEND);
	}

	file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s - ')."CONTACT_INFO - ".print_r($contactInfo, true)."\n", FILE_APPEND);

	$contactId = array();

	foreach($contactInfo as $index=>$info) {
		$contactId[$index] = getContactId($db, $info['firstname'], $info['lastname'], $accountId);

		if($contactId[$index] == NULL) {
			file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s - ')."Contact not found\n", FILE_APPEND);
			$contactInfoString = array("assigned_user_id"=>$crmUserId, "firstname"=>$info['firstname'], "lastname"=>$info['lastname'], "email"=>$info['email1'], "phone"=>$info['phone1'], "account_id"=>$accountId);
			file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s - ').print_r($contactInfoString, true)."\n", FILE_APPEND);
			$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($contactInfoString)."&elementType=Contacts";
			$curlResult = curlPOST($post_string, $webserviceURL);

			$contactId[$index] = getContactId($db, $info['firstname'], $info['lastname'], $accountId);

			$responseToEmit['contact'] = json_decode($curlResponse, true);
		}
		else {
			$curlResult = curlGET("?operation=retrieve&sessionName=".$sessionId."&id=".$contactId[$index], $webserviceURL);
			$responseToEmit['contact'.$index] = json_decode($curlResult, true);
		}
	}

	$opportunityId = getOpportunityId($db, $accountId, $contactId[0], $crmUserId);

	if($opportunityId == NULL) {
		$opportunityInfo = array('assigned_user_id'=>$crmUserId, 'related_to'=>$accountId,
								'potentialname'=>$customerInfo['companyname'], 'closingdate'=>date('Y-m-d', strtotime('+30 days')),
								'sales_stage'=>'Qualification', 'contact_id'=>$contactId[0], 'business_line'=>'Commercial Move',
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

		file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - ".print_r(json_decode($curlResult), true)."\n", FILE_APPEND);

		$opportunityId = getOpportunityId($db, $accountId, $contactId[0], $crmUserId);
	}
	else {
		file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - OpportunityId is ".$opportunityId."\n", FILE_APPEND);
	}

	relateAdditionalContacts($db, $contactId, $opportunityId);

	$activityTypes = array(1=>"E-mail To", 2=>"E-mail From", 3=>"Text Message To", 4=>"Text Message From", 5=>"Phone Call To", 6=>"Phone Call From", 7=>"Sales Call", 8=>"Follow Up", 9=>"Timer", 10=>"Notes", 11=>"Photo", 12=>"Drawing", 13=>"Site Survey", 14=>"Walk Through", 15=>"Move Planning", 16=>"Move Day");

	file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s - ').print_r($activities, true)."\n", FILE_APPEND);
	foreach($activities as $activity) {
		if($activity['activitytype']+0 < 9) {
			$activityId = getActivityId($db, $contactId[0], $opportunityId, $crmUserId, $activity['activityid']);

			if($activityId == NULL) {
				$start_date = strstr($activity['startdate'], 'T', true);
				$start_time = substr($activity['startdate'], strlen($start_date)+2, 8);
				$end_date = strstr($activity['enddate'], 'T', true);
				$end_time = substr($activity['enddate'], strlen($end_date)+2, 8);
				if($end_date == "1970-01-01") {
					$end_date = date('Y-m-d', strtotime($activity['startdate'])+300);
					$end_time = date('H:i:s', strtotime($activity['startdate'])+300);
				}
				$activityInfo = array("assigned_user_id"=>$crmUserId, "subject"=>$activityTypes[$activity['activitytype']]." - ".$customerInfo['companyname'],
									  "date_start"=>$start_date, "time_start"=>$start_time,
									  "due_date"=>$end_date, "time_end"=>$end_time,
									  "duration_hours"=>0, "parent_id"=>$opportunityId, "activitytype"=>$activityTypes[$activity['activitytype']],
									  "description"=>$activity['notes'], "contact_id"=>$contactId[0], "eventstatus"=>"Planned", "visibility"=>"Private", "oiactivityid"=>$activity['activityid']);

				$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($activityInfo)."&elementType=Events";
				$curlResult = curlPOST($post_string, $webserviceURL);

				file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s')." - ".print_r(json_decode($curlResult), true)."\n", FILE_APPEND);

				$activityId = getActivityId($db, $contactId[0], $opportunityId, $userId, $activity['activityid']);
			}
			else {
				file_put_contents('logs/dbxprocess.log', date('Y-m-d H:i:s - ').$activityId."\n", FILE_APPEND);
			}
		}
	}
}

function getAccountId($db, $accountname) {
	$sql = "SELECT accountid FROM `vtiger_account` JOIN `vtiger_crmentity` ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE accountname=? AND deleted=0";
	$params[] = $accountname;

	$result = $db->pquery($sql, $params);

	$row = $result->fetchRow();

	if($row == NULL) {return NULL;}
	$objTypeId = getObjectTypeId($db, "Accounts");
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
	$objTypeId = getObjectTypeId($db, "Contacts");
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
	$objTypeId = getObjectTypeId($db, "Potentials");
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
	$objTypeId = getObjectTypeId($db, "Events");
	return $objTypeId.$row[0];
}

function getObjectTypeId($db, $modName) {
	$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
	$params[] = $modName;

	$result = $db->pquery($sql, $params);

	return $db->query_result($result, 0, 'id').'x';
}

function curlPOST($post_string, $webserviceURL) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webserviceURL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
		$curlResult = curl_exec($ch);
		curl_close($ch);

		return $curlResult;
}
?>
