<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$sql = "UPDATE `vtiger_agentcompgr_type`
        SET `vtiger_agentcompgr_type`.`agentcompgr_type` = ? 
        WHERE `vtiger_agentcompgr_type`.`agentcompgr_type` = ?";

$adb->pquery($sql,array('Tariffs','Local Tariffs'));

echo "<br>Update picklist values of 'type' field within Agent Compensation Group module<br>";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";