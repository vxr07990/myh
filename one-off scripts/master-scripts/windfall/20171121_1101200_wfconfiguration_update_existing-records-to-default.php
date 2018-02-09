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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_wfconfiguration` SET `articlenumber_group` = 1 WHERE `articlenumber_group` IS NULL');
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_wfconfiguration` SET `description_group` = 1 WHERE `description_group` IS NULL');
