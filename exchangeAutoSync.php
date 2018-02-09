<?php
require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';
file_put_contents('logs/devLog.log', "\n Entering exchangeAutoSync.php\n", FILE_APPEND);
ignore_user_abort(1);
set_time_limit(0);

$db = PearDatabase::getInstance();
$sql = "SELECT id FROM `vtiger_users` WHERE exchange_hostname IS NOT NULL AND exchange_hostname != '' AND exchange_username IS NOT NULL AND exchange_username != '' AND exchange_password IS NOT NULL AND exchange_password != ''";
$result = $db->query($sql);
$GLOBALS['exchangeAutoSyncRun'] = true;

while ($row =& $result->fetchRow()) {
    $userId = $row['id'];
    $cliString = 'nohup php -f exchangeHelper.php module=Exchange view=List operation=sync sourcemodule=Calendar exchangeAutoSyncRun=1 forked=1 current_user_id='.$userId.' >/logs/exchangeOutput'.$userId.'.log 2>/logs/exchangeErrors'.$userId.'.log &';
    $cliString = str_replace('\\', '/', $cliString);

    shell_exec($cliString);
}
