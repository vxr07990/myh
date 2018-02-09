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

global $adb;

$moduleName = 'WFTransactions';

try {
    //@NOTE: Attempt to update the assigned to fieldlabel and block.
    $field = getFieldModel('assigned_user_id', $moduleName);
    updateFieldValue($field, 'fieldlabel', 'LBL_ASSIGNED_USER_ID');
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if ($moduleInstance) {
        $blockInstance = Vtiger_Block::getInstance('LBL_WFTRANSACTIONS_DETAILS', $moduleInstance);
        updateFieldValue($field, 'block', $blockInstance->id);
    }
} catch (Exception $exception) {
    //ignore.
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
