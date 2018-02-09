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

error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
//create column agentmanager_id in vtiger_leadsource table
$adb->pquery("ALTER TABLE `vtiger_leadsource` ADD `agentmanager_id` INT(11)");




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";