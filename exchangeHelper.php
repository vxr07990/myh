<?php
require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';
require_once('modules/Emails/mail.php');
file_put_contents('logs/devLog.log', "\n Entering exchangeHelper.php\n", FILE_APPEND);
ignore_user_abort(1);
set_time_limit(0);

parse_str(implode('&', array_slice($argv, 1)), $_GET);

$request = new Vtiger_Request($_GET);
//file_put_contents('logs/devLog.log', "\n \$_POST['globals'] : ".print_r($_POST['current_user'], true), FILE_APPEND);
file_put_contents('logs/devLog.log', "\n Helper Request : ".print_r($request, true), FILE_APPEND);
//file_put_contents('logs/devLog.log', "\n Helper _POST : ".print_r($_POST, true), FILE_APPEND);
//file_put_contents('logs/devLog.log', "\n Helper GLOBALS : ".print_r($GLOBALS, true), FILE_APPEND);
//file_put_contents('logs/devLog.log', "\n Helper _SESSION : ".print_r($_SESSION, true), FILE_APPEND);

$focus = new Exchange_List_View();
$focus->process($request);

$loginURL = getenv('SITE_URL');

if (getenv('IGC_MOVEHQ')) {
    $softwareName = 'MoveHQ';
    $developerName = 'WIRG';
    $developerSite = 'www.mobilemover.com';
    $logo = '<img src="test/logo/MoveHQ.png" title="MoveHQ.png" alt="MoveHQ.png">';
    $website = 'www.mobilemover.com';
    $supportTeam = 'MoveHQ Support Team';
    $supportEmail = 'crmsupport@igcsoftware.com';
} else {
    $softwareName = 'MoveCRM';
    $developerName = 'IGC Software';
    $developerSite = 'www.igcsoftware.com';
    $logo = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
    $website = 'www.igcsoftware.com';
    $supportTeam = 'MoveCRM Support Team';
    $supportEmail = 'crmsupport@igcsoftware.com';
}

if (empty($GLOBALS['exchange_error_code'])) {
    $message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">Thank you for synchronizing your Exchange calendar with '.$softwareName.'. The process has completed successfully.<br /> <br />
'.$GLOBALS['record_sync']['vtiger']['create'].' records were imported from Exchange.<br />'.$GLOBALS['record_sync']['exchange']['create'].' records were exported to Exchange.<br></div>		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
        <p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
    </div></div>';
} else {
    $message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">Thank you for synchronizing your Exchange calendar with '.$softwareName.'. Unfortunately, an error occurred during the initial sync process. Details are provided below.<br /> <br />
'.vtranslate($GLOBALS['exchange_error_message']).'<br /></div>		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
        <p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
    </div></div>';
}

$db = PearDatabase::getInstance();
$sql = "SELECT email1 FROM `vtiger_users` WHERE id=?";

$result = $db->pquery($sql, [$request->get('current_user_id')]);
$row = $result->fetchRow();

global $vtiger_current_version;

$subject = $softwareName.' Exchange Calendar Synchronization';

file_put_contents('logs/devLog.log', "\n Email Target: ".$row['email1']."\n", FILE_APPEND);

if (!$GLOBALS['exchangeAutoSyncRun'] && !$request->get('exchangeAutoSyncRun')) {
    $mail_status = send_mail('Exchange', $row['email1'], $softwareName.' Support', 'crmsupport@igcsoftware', $subject, $message, '', '', '', '', '', true);
}

file_put_contents('logs/devLog.log', "\n Exiting exchangeHelper.php\n", FILE_APPEND);



/*//Send an email to provided email address with login credentials
$loginURL = getenv('SITE_URL');

if(getenv('IGC_MOVEHQ')){
$softwareName = 'MoveHQ';
$developerName = 'WIRG';
$developerSite = 'www.mobilemover.com';
$logo = '<img src="test/logo/MoveHQ.png" title="MoveHQ.png" alt="MoveHQ.png">';
$website = 'www.mobilemover.com';
$supportTeam = 'MoveHQ Support Team';
$supportEmail = 'crmsupport@igcsoftware.com';
} else{
$softwareName = 'MoveCRM';
$developerName = 'IGC Software';
$developerSite = 'www.igcsoftware.com';
$logo = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
$website = 'www.igcsoftware.com';
$supportTeam = 'MoveCRM Support Team';
$supportEmail = 'crmsupport@igcsoftware.com';
}

global $vtiger_current_version;

$subject = 'Welcome to '.$softwareName;
$message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">Thank you for using '.$softwareName.'. To get started, proceed to '.$loginURL." and login with the credentials provided below.<br /> <br />Username: ".$newUserInfo['user_name']."<br />Password: ".$newPassword.'<br></div>		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
        <p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
    </div></div>';

$mail_status = send_mail('AgentManager', $request->get('email1'), 'MoveCRM Support', 'crmsupport@igcsoftware', $subject, $message,'','','','','',true);*/
