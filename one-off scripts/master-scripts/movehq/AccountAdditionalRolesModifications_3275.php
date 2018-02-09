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
$sql = "ALTER TABLE `vtiger_additional_roles`
MODIFY COLUMN `commodity`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
";
$adb->pquery($sql,array());

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";