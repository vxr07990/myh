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
 *     filename: filename to be used for storing file on server
 *     doctitle: title of the document to be used in the CRM
 *     filetype: MIME type of file being uploaded
 *     userid: userid of CRM user to whom the Document should be assigned
 *     data: base64 encoded string with contents of file
 *     parentid: Optional - Array - ids of CRM entities (Opportunity, Account, etc.) to which document is related
 *     folderid: Optional - id of folder to which document should be linked; if not provided, default folder will be used
 * }
 */

include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'customWebserviceFunctions.php';

file_put_contents('logs/uploadCloneTest.log', "Inside of docuploadclone.php script\n", FILE_APPEND);

if (!isset($_POST) || empty($_POST)) {
    $errCode = "NO_POST_DATA_FOUND";
    $errMessage = "No POST data was found in the request";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

file_put_contents('logs/uploadCloneTest.log', "POST data is present\n", FILE_APPEND);

if (!isset($_POST['sessionName'])) {
    $errCode = "MISSING_SESSIONID";
    $errMessage = "Session Identifier was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($_POST['element'])) {
    $errCode = "MISSING_ELEMENT";
    $errMessage = "Element information was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

$sessionId = $_POST['sessionName'];

$webserviceURL = getenv('WEBSERVICE_URL');

//Perform Describe operation in order to verify that Session Identifier is valid
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=describe&sessionName=".$sessionId."&elementType=Documents");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);

$describeResult = json_decode($curlResult);

file_put_contents('logs/uploadCloneTest.log', "After describeResult\n", FILE_APPEND);

if ($describeResult->success != 1) {
    die($curlResult);
}
//Session Identifier has been verified - proceed with element parameter check

$postdata = json_decode($_POST['element'], true);

if (!isset($postdata['data'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'data' was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($postdata['filename'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Require parameter 'filename' was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($postdata['userid'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'userid' was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($postdata['doctitle'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'doctitle' was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}

if (!isset($postdata['filetype'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'filetype' was not provided";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}
//Element parameters are all present - proceed with User ID check

$userId = substr($postdata['userid'], strpos($postdata['userid'], 'x')+1);

$db = PearDatabase::getInstance();

$sql = "SELECT id FROM `vtiger_crmentity_seq`";
$params = array();

$result = $db->pquery($sql, $params);

$row = $result->fetchRow();
$crmIdToWrite = $row[0]+2;

$filename = $postdata['filename'];
$data_string = str_replace(' ', '+', $postdata['data']);

$sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
$params[] = $userId;

$result = $db->pquery($sql, $params);
unset($params);

$row = $result->fetchRow();

if ($row == null) {
    $errCode = "USER_NOT_FOUND";
    $errMessage = "User does not exist in database";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}
//Verified that provided User ID is valid - proceed with base64 decoding of data

$decodedData = base64_decode($data_string, true);

if (!$decodedData) {
    $errCode = "INVALID_DATA";
    $errMessage = "Parameter 'data' contains invalid base64 encoding";
    $response = json_encode(generateErrorArray($errCode, $errMessage));

    die($response);
}
//Verified that provided data successfully decoded - proceed with Document entity creation

$currentDateFilepath = date("Y/F/");
$currentMonth = date("n");
$currentDay = date("j");
$currentDayOfWeek = date("w");
$firstOfMonth = date("w", mktime(0, 0, 0, $currentMonth, 1));
$weekNum = floor((($currentDay+($firstOfMonth-1))/7)+1);

$filepath = "storage/".$currentDateFilepath."week".$weekNum;
$filetype = $postdata['filetype'];

if ($filetype == 'application/pdf') {
    if (substr($filename, strlen($filename)-4) != '.pdf') {
        $filename = $filename.'.pdf';
    }
}

if (!file_exists($filepath)) {
    mkdir($filepath, 0777, true);
}

$f = fopen($filepath."/".$crmIdToWrite."_".$filename, "w+b") or die(json_encode(generateErrorArray("UNABLE_TO_OPEN_FILE", "Unable to open file for writing")));
$written = fwrite($f, $decodedData) or die(json_encode(generateErrorArray("UNABLE_TO_WRITE_FILE", "Unable to write to file")));
fclose($f);

$folderId = "22x1";

if (isset($postdata['folderid'])) {
    $folderId = $postdata['folderid'];
}

$documentInfo = array('notes_title'=>$postdata['doctitle'], 'filename'=>$filename, 'assigned_user_id'=>$userId, 'filetype'=>$filetype, 'filelocationtype'=>'I', 'filestatus'=>1, 'folderid'=>$folderId, 'filesize'=>$written);
$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($documentInfo)."&elementType=Documents";

$curlResult = curlPOST($post_string, $webserviceURL);

$createResponse = json_decode($curlResult);

if ($createResponse->success != 1) {
    die($curlResult);
}
//Document entity successfully created - proceed with Document Attachment entity creation

$docid = substr($createResponse->result->id, strpos($createResponse->result->id, "x")+1);
$ctime = $createResponse->result->createdtime;
$mtime = $createResponse->result->modifiedtime;

$sql = "INSERT INTO `vtiger_crmentity` (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
$params[] = $docid+1;
$params[] = $userId;
$params[] = $userId;
$params[] = 'Documents Attachment';
$params[] = '';
$params[] = $ctime;
$params[] = $mtime;

$result = $db->pquery($sql, $params);
unset($params);

$sql = "INSERT INTO `vtiger_attachments` (attachmentsid, name, description, type, path) VALUES (?, ?, ?, ?, ?)";
$params[] = $docid+1;
$params[] = $filename;
$params[] = '';
$params[] = $filetype;
$params[] = $filepath."/";

$result = $db->pquery($sql, $params);
unset($params);

$sql = "UPDATE `vtiger_crmentity_seq` SET id=id+1";
$params = array();

$result = $db->pquery($sql, $params);

$sql = "DELETE FROM `vtiger_seattachmentsrel` WHERE crmid=?";
$params[] = $docid;

$result = $db->pquery($sql, $params);
unset($params);

$sql = "INSERT INTO `vtiger_seattachmentsrel` VALUES (?, ?)";
$params[] = $docid;
$params[] = $docid+1;

$result = $db->pquery($sql, $params);
unset($params);

if (!isset($postdata['parentid']) || empty($postdata['parentid'])) {
    die($curlResult);
}
//Verified that Parent ID has been provided - proceed with validity check for Parent ID

$errResponse = array();

$parentIds = (array) $postdata['parentid'];

foreach ($parentIds as $parentId) {
    $crmId = substr($parentId, strpos($parentId, "x")+1);

    $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
    $params[] = $crmId;

    $result = $db->pquery($sql, $params);
    unset($params);

    $row = $result->fetchRow();

    if ($row == null) {
        $res = json_decode($curlResult, true);
        $res['error'] = array("code"=>"INVALID_PARENTID", "message"=>"Provided ParentID is invalid. Document will remain unlinked");
        $response = json_encode($res);
        $errResponse[] = $response;
        continue;
    }
    //Verified that provided Parent ID is valid - proceed with relationship creation

    $sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
    $params[] = $crmId;
    $params[] = substr($createResponse->result->id, strpos($createResponse->result->id, "x")+1);

    $result = $db->pquery($sql, $params);
    unset($params);
}

$tmpResponse = json_decode($curlResult, true);

if (empty($errResponse)) {
    $tmpResponse['parentid'] = $postdata['parentid'];
} else {
    $tmpResponse['parentid'] = $errResponse;
}
die(json_encode($tmpResponse));
