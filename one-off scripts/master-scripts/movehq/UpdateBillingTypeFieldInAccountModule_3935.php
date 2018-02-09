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


// 2864: Creation of Item Codes Module
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;
global $adb;
$moduleInstance = Vtiger_Module::getInstance('Accounts');

$field = Vtiger_Field::getInstance('billing_type', $moduleInstance);
if ($field) {
    $adb->pquery("UPDATE `vtiger_field` SET `uitype`=? WHERE `fieldid`=?", array(3333, $field->id));
    //update data type billing_type field
    $adb->pquery("ALTER TABLE `vtiger_account` MODIFY  `billing_type` text");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";