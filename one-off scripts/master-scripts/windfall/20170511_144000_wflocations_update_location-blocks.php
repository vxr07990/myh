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

$Vtiger_Utils_Log = true;
global $adb;

$moduleInstance = Vtiger_Module::getInstance('WFLocations');
if (!$moduleInstance) {
    return "Locations doesn't exist";
}

$blockInstance = Vtiger_Block::getInstance('LBL_WFLOCATIONS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFLOCATIONS_INFORMATION block doesn't exist<br>";
}
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = '0' WHERE blockid = '$blockInstance->id'");
