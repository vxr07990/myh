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

$fieldName = 'number';
$module = 'WFOperationsTasks';
$newValue = 'varchar(255)';

$moduleInstance = Vtiger_Module::getInstance($module);
if (!$moduleInstance) {
    print "FAILED\n";
}
$x = updateFieldColumnType($fieldName,$moduleInstance,$newValue);
print "\e[39mFINISHED: " . __FILE__ . "<br />\n\e[0m";
