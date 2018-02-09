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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$hostModuleName = 'WFActivityFields';
$hostModuleBlockName = 'LBL_'.strtoupper($hostModuleName).'_DETAILS';

$guestModuleName = 'WFActivityFieldMaps';
$guestModuleBlockName = 'LBL_'.strtoupper($guestModuleName).'_DETAILS';

$hostModuleInstance = Vtiger_Module::getInstance($hostModuleName);
if (!$hostModuleInstance) {
    if (function_exists("removeScriptFromVersionLogs")) {
        removeScriptFromVersionLogs(__FILE__);
    }
    return false;
}

$guestModuleInstance = Vtiger_Module::getInstance($guestModuleName);
if (!$guestModuleInstance) {
    if (function_exists("removeScriptFromVersionLogs")) {
        removeScriptFromVersionLogs(__FILE__);
    }
    return false;
}

//set guestModule.
$hostModuleInstance->setGuestBlocks($guestModuleName, [$guestModuleBlockName], $hostModuleBlockName);
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
