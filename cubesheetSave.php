<?php

/*
 * HTTP POST
 * Parameter name: cubesheetsid
 * Parameter type: Integer
 * Parameter contents: valid cubesheet id
 */

include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'customWebserviceFunctions.php';

if (!isset($_POST) || empty($_POST)) {
    $errCode = "NO_POST_DATA_FOUND";
    $errMessage = "No POST data was found in the request";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($_POST['cubesheetsid'])) {
    $errCode = "MISSING_CUBESHEETSID";
    $errMessage = "Cubesheets ID was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

$username = 'admin';
$webserviceURL = getenv('WEBSERVICE_URL');

//login
//look up our accesskey

$db = PearDatabase::getInstance();
$sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
$params[] = 1;

$result = $db->pquery($sql, $params);
unset($params);
$accessKey = $db->query_result($result, 0, 'accesskey');

//Step 1 if logging in to the webservice get the challenge with your user name
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$username);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);
$challengeResponse = json_decode($curlResult);

//Step 2 take the challenge and generate an access key
$generatedkey = md5($challengeResponse->result->token.$accessKey);
$post_string = "operation=login&username=".$username."&accessKey=".$generatedkey;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);

//Step 3 take response from the access key and get the sessionName this will be all that is required for all future webservice requests
$loginResponse = json_decode($curlResult);
$sessionId = $loginResponse->result->sessionName;

//end login

$cubesheetsid = $_POST['cubesheetsid'];

//Perform Describe operation in order to verify that Session Identifier is valid
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=describe&sessionName=".$sessionId."&elementType=Cubesheets");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);

$describeResult = json_decode($curlResult);

if ($describeResult->success != 1) {
    die($curlResult);
}

$db = PearDatabase::getInstance();
$ObjectId = getObjectTypeId($db, "Cubesheets");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?sessionName=".$sessionId."&operation=retrieve&id=".$ObjectId.$cubesheetsid);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);
$retrieveResponse= json_decode($curlResult, true);

if ($retrieveResponse['success'] != true) {
    die($curlResult);
}

$element = $retrieveResponse['result'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "sessionName=".$sessionId."&operation=update&element=".json_encode($element));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);

function getObjectTypeId($db, $modName)
{
    $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
    $params[] = $modName;

    $result = $db->pquery($sql, $params);
    unset($params);

    return $db->query_result($result, 0, 'id').'x';
}
