<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();

$sql = "SELECT * FROM `vtiger_wfstatus`
LEFT JOIN `vtiger_crmentity` ON `vtiger_wfstatus`.`wfstatusid` = `vtiger_crmentity`.`crmid`
WHERE `vtiger_wfstatus`.`is_default` = ? AND `vtiger_crmentity`.`deleted` = ?";

$to_update = $db->pquery($sql,[1,0]);

while($row = $to_update->fetchRow()) {
  $db->pquery('UPDATE `vtiger_crmentity` SET `smownerid` = ?, `agentid` = ? WHERE `crmid` = ?',[0,0,$row['crmid']]);
}
