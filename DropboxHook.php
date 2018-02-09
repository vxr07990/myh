<?php

include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

global $log;

if (isset($_GET['challenge'])) {
    echo $_GET['challenge'];
    return;
}

$log->debug("Webhook hit!");
$body = @file_get_contents('php://input');
$decoded = json_decode($body, true);
$userIds = $decoded["delta"]["users"];

if (isset($_SERVER['HTTP_X_DROPBOX_SIGNATURE'])) {
    if ($_SERVER['HTTP_X_DROPBOX_SIGNATURE'] != hash_hmac('sha256', $body, "94l3eja11oqb2xy")) {
        die("Incorrect signature detected. Ending script.");
    }
    $log->debug("DBX Signature Verified");
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - Verified signature\n", FILE_APPEND);
} else {
    die("No signature detected. Ending script.");
}

file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".$userIds[0]."\n", FILE_APPEND);

if (isset($userIds) && !empty($userIds)) {
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".print_r($userIds, true)."\n", FILE_APPEND);
} else {
    $log->debug("No userID data found");
    die("No userID data found");
}

$accessTokens = array();

file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - accessTokens array created\n", FILE_APPEND);

foreach ($userIds as $userId) {
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".$userId."\n", FILE_APPEND);
    $token = getAccessToken($userId);
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".$token."\n", FILE_APPEND);
    if ($token != "0") {
        file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".$userId.": has token ".$token."\n", FILE_APPEND);
        $accessTokens[$userId] = $token;
    }
}

file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".print_r($accessTokens, true)."\n", FILE_APPEND);

$postParams = array();

foreach ($accessTokens as $userId=>$token) {
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - ".$userId.": ".$token."\n", FILE_APPEND);
    $postParams[] = $userId."=".urlencode($token);
}

$post_string = implode('&', $postParams);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, getenv('WEBSERVICE_URL'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$result = curl_exec($ch);
curl_close($ch);

file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - After cURL call\n", FILE_APPEND);

function getAccessToken($userId)
{
    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - Inside getAccessToken function\n", FILE_APPEND);
    $db = PearDatabase::getInstance();

    $sql = "SELECT dbx_token FROM `vtiger_users` WHERE dbx_userid=?";
    $params[] = $userId;

    $result = $db->pquery($sql, $params);

    file_put_contents('logs/dbxhook.log', date('Y-m-d H:i:s')." - After SQL Query\n", FILE_APPEND);

    $row = $result->fetchRow();

    if ($row == null) {
        return "0";
    }

    return $row[0];
}
