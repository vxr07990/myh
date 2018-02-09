<?php
require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';
file_put_contents('logs/devLog.log', "\n Entering googleAutoSync.php\n", FILE_APPEND);
ignore_user_abort(1);
set_time_limit(0);

$db = PearDatabase::getInstance();
$sql = "SELECT `googlemodule`, `user` FROM `vtiger_google_sync`";
$result = $db->query($sql);

while ($row =& $result->fetchRow()) {
    if ($row['googlemodule'] != 'Calendar') {
        continue;
    }
    $sql = "SELECT * FROM `vtiger_google_oauth2` WHERE userid=?";
    $res = $db->pquery($sql, [$row['user']]);
    if ($db->num_rows($res) < 1) {
        //User has not previously synced, so attempting to process through auto-sync will cause the script to die on authorization
        continue;
    }
    $request = new Vtiger_Request(['module'=>'Google', 'view'=>'List', 'operation'=>'sync', 'sourcemodule'=>'Calendar']);
    $GLOBALS['current_user_id'] = $row['user'];
    $user = new Users();
    $GLOBALS['current_user'] = $user->retrieveCurrentUserInfoFromFile($row['user']);

    try {
        $focus = new Google_List_View();
        $focus->process($request);
    } catch (Exception $ex) {
        file_put_contents('logs/googleSync'.$row['user'].'.log', "\n".date('Y-m-d H:i:s - ')."Exception caught: ".$ex->getMessage(), FILE_APPEND);
        continue;
    }

    file_put_contents('logs/googleSync'.$row['user'].'.log', "\n".date('Y-m-d H:i:s - ').print_r($GLOBALS['record_sync'], true), FILE_APPEND);
}
