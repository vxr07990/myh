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

$moduleName = 'WFConfiguration';
$module = Vtiger_Module::getInstance($moduleName);

if (!$module) {
    print __FILE__." : Failed to find module (".$moduleName.").".PHP_EOL;
    return;
}

$blockOrder = [
    'LBL_WFCONFIGURATION_INVENTORY_INFORMATION',
    'LBL_WFCONFIGURATION_INVENTORY_DETAILS',
    'LBL_WFCONFIGURATION_UNIT_OF_MEASURES',
    'LBL_WFCONFIGURATION_SETUP',
    'LBL_WFCONFIGURATION_PHYSICAL_LOCATION',
    'LBL_WFCONFIGURATION_FINISH_DETAILS',
    'LBL_WFCONFIGURATION_PURCHASE_DETAILS',
    'LBL_WFCONFIGURATION_WAREHOUSE_DETAILS',
    'LBL_WFCONFIGURATION_DETAILS'
];

$db = &PearDatabase::getInstance();
foreach ($blockOrder as $index => $blockLabel) {
    $sequence = $index+1;
    $blockInstance = Vtiger_Block::getInstance($blockLabel,$module);
    if (!$blockInstance) {
        continue;
    }
    $stmt = 'UPDATE vtiger_blocks set sequence=? WHERE blockid=? LIMIT 1';
    $db->pquery($stmt, [$sequence, $blockInstance->id]);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
