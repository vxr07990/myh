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

$Vtiger_Utils_Log = true;
$isNew = false;
echo "<h2>Add columns to Document Designer module</h2><br>";

$stmt = "ALTER TABLE `vtiger_quotingtool_settings`
	ADD COLUMN `page_format` TEXT NULL AFTER `background`;";
$adb->pquery($stmt);
echo "Done - Add columns to Document Designer module<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";