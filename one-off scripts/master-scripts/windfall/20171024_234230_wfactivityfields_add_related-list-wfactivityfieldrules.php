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

$success = false;
global $adb;
$moduleInstance = Vtiger_Module::getInstance('WFActivityFields');
if ($moduleInstance) {
    $InventoryHistory = Vtiger_Module::getInstance('WFActivityFieldRules');
    if ($InventoryHistory) {
        $success = true;
        $moduleInstance->setRelatedList($InventoryHistory, 'WFActivityFieldRules', ['ADD'], 'get_dependents_list');
    }
}

if (!$success) {
    if (function_exists("removeScriptFromVersionLogs")) {
        removeScriptFromVersionLogs(__FILE__);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
