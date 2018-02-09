<?php

/*
 * HTTP_POST
 * Required parameters (all contained inside JSON parameter named element):
 * filename: filename that should be used to store file on the server
 * doctitle: title to be used for Document entity inside of CRM
 * filetype: file type of file to be uploaded
 * username: username of CRM user who is uploading the file
 * data: base64 encoded string with contents of file to be uploaded
 *
 * Optional parameters:
 * parentid: id of CRM entity to which uploaded Document entity should relate
 *			-> if not provided, Document entity will be unrelated
 * folderid: id of folder to which uploaded Document entity should be linked
 *			-> if not provided, the default folder with id 1 will be used
 */

include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

//file_put_contents('logs/uploadTest.log', "Before input checking\n", FILE_APPEND);

if (!isset($_POST) || empty($_POST)) {
    $errCode = "NO_POST_DATA_FOUND";
    $errMessage = "No POST data was found in the request";
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}

//file_put_contents('logs/uploadTest.log', "POST data is present\n", FILE_APPEND);

$postdata = json_decode($_POST['element'], true);

//file_put_contents('logs/uploadTest.log', "postdata variable initialized from JSON\n".print_r($postdata, true)."\n", FILE_APPEND);

if (!isset($postdata['data']) || !isset($postdata['filename'])) {
    $errCode = "MISSING_REQ_PARAM";
    if (isset($postdata['data'])) {
        $errMessage = "Required parameter 'filename' was not provided";
    } else {
        $errMessage = "Required parameter 'data' was not provided";
    }
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}

if (!isset($postdata['username'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'username' was not provided";
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}

if (!isset($postdata['doctitle'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'doctitle' was not provided";
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}

if (!isset($postdata['filetype'])) {
    $errCode = "MISSING_REQ_PARAM";
    $errMessage = "Required parameter 'filetype' was not provided";
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}

//file_put_contents('logs/uploadTest.log', "All required parameters are present\n", FILE_APPEND);

$db = PearDatabase::getInstance();

$sql = "SELECT id FROM `vtiger_crmentity_seq`";
$params = array();

$result = $db->pquery($sql, $params);

$row = $result->fetchRow();
$crmIdToWrite = $row[0]+2;

$filename = $postdata['filename'];
$data_string = str_replace(' ', '+', $postdata['data']);

$sql = "SELECT * FROM `vtiger_notes` JOIN `vtiger_crmentity` ON vtiger_notes.notesid=vtiger_crmentity.crmid WHERE (filename=? OR title=?) AND deleted=0";
$params[] = $filename;
$params[] = "'".$postdata['doctitle']."'";

file_put_contents('logs/uploadTest.log', "Prior to DB Query\n", FILE_APPEND);

$result = $db->pquery($sql, $params);
unset($params);

//file_put_contents('logs/uploadTest.log', "After DB Query\n", FILE_APPEND);

/*if($result->numRows() > 0) {
    file_put_contents('logs/uploadTest.log', "EXISTS!\n", FILE_APPEND);
    $errCode = "DOCUMENT_EXISTS";
    $errMessage = "Document already exists inside of database";
    file_put_contents('logs/uploadTest.log', json_encode(generateErrorArray($errCode, $errMessage))."\n", FILE_APPEND);
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}*/

//file_put_contents('logs/uploadTest.log', "Prior to second DB Query\n", FILE_APPEND);

$sql = "SELECT accesskey FROM `vtiger_users` WHERE user_name=?";
$params[] = $postdata['username'];

$result = $db->pquery($sql, $params);
unset($params);

//file_put_contents('logs/uploadTest.log', "After second DB Query\n", FILE_APPEND);

$row = $result->fetchRow();

if ($row == null) {
    $errCode = "USERNAME_NOT_FOUND";
    $errMessage = "Username does not exist in database";
    file_put_contents('logs/uploadTest.log', print_r(generateErrorArray($errCode, $errMessage), true)."\n", FILE_APPEND);
    echo json_encode(generateErrorArray($errCode, $errMessage));
    return;
}
$accesskey = $row[0];

//file_put_contents('logs/uploadTest.log', $accesskey."\nPrior to date calls\n", FILE_APPEND);

$currentDateFilepath = date("Y/F/");
$currentMonth = date("n");
$currentDay = date("j");
$currentDayOfWeek = date("w");
$firstOfMonth = date("w", mktime(0, 0, 0, $currentMonth, 1));
$weekNum = floor((($currentDay+($firstOfMonth-1))/7)+1);

$filepath = "storage/".$currentDateFilepath."week".$weekNum;

//file_put_contents('logs/uploadTest.log', "Prior to file_exists call\n", FILE_APPEND);

if (!file_exists($filepath)) {
    mkdir($filepath, 0777, true);
}

$decodedData = base64_decode($data_string, true);

if (!$decodedData) {
    file_put_contents('logs/uploadTest.log', "Decoded base64 string is invalid!\n", FILE_APPEND);
}
//file_put_contents('logs/uploadTest.log', $data_string."\n", FILE_APPEND);
//file_put_contents('logs/uploadTest.log', $decodedData."\n", FILE_APPEND);

//file_put_contents('logs/uploadTest.log', "Prior to file open/write/close operation\n", FILE_APPEND);

$f = fopen($filepath."/".$crmIdToWrite."_".$filename, "w+b") or die(json_encode(generateErrorArray("UNABLE_TO_OPEN_FILE", "Unable to open file for writing")));
$written = fwrite($f, $decodedData) or file_put_contents('logs/uploadTest.log', print_r(generateErrorArray("UNABLE_TO_WRITE_FILE", "Unable to write to file"), true)."\n", FILE_APPEND);
fclose($f);

//file_put_contents('logs/uploadTest.log', $written." bytes written to file\n", FILE_APPEND);

//file_put_contents('logs/uploadTest.log', "After file open/write/close operation\n", FILE_APPEND);

$webserviceURL = "http://54.68.204.64/demo/webservice.php";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$postdata['username']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
$curlResult = curl_exec($ch);
curl_close($ch);

$challengeResponse = json_decode($curlResult);

//file_put_contents('logs/uploadTest.log', print_r($challengeResponse, true)."\n", FILE_APPEND);

$generatedkey = md5($challengeResponse->result->token.$accesskey);
$post_string = "operation=login&username=".$postdata['username']."&accessKey=".$generatedkey;

$curlResult = curlPOST($post_string, $webserviceURL);

$loginResponse = json_decode($curlResult);

//file_put_contents('logs/uploadTest.log', print_r($loginResponse, true)."\n", FILE_APPEND);

$sessionId = $loginResponse->result->sessionName;
$userid = $loginResponse->result->userId;

$folderId = "22x1";

if (isset($postdata['folderid'])) {
    $folderId = $postdata['folderid'];
}

$documentInfo = array('notes_title'=>$postdata['doctitle'], 'filename'=>$filename, 'assigned_user_id'=>$userid, 'filetype'=>$postdata['filetype'], 'filelocationtype'=>'I', 'filestatus'=>1, 'folderid'=>$folderId, 'filesize'=>$written);
$post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($documentInfo)."&elementType=Documents";

$curlResult = curlPOST($post_string, $webserviceURL);

$createResponse = json_decode($curlResult);

//file_put_contents('logs/uploadTest.log', print_r($createResponse, true)."\n", FILE_APPEND);

if ($createResponse->success != 1) {
    echo $curlResult;
    return;
}

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
$params[] = $postdata['filetype'];
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
    echo $curlResult;
    return;
}

$sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
$crmId = '';
if (strpos($postdata['parentid'], "x") != false) {
    $crmId = substr($postdata['parentid'], strpos($postdata['parentid'], "x")+1);
} else {
    $crmId = $postdata['parentid'];
}
$params[] = $crmId;
$params[] = substr($createResponse->result->id, strpos($createResponse->result->id, "x")+1);

$result = $db->pquery($sql, $params);
unset($params);

echo $curlResult;
return;

function generateErrorArray($errCode, $errMessage)
{
    $result = array();
    $result['success'] = '';
    $error = array();
    $error['code'] = $errCode;
    $error['message'] = $errMessage;
    $result['error'] = $error;
    
    return $result;
}

function curlPOST($post_string, $webserviceURL)
{
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
