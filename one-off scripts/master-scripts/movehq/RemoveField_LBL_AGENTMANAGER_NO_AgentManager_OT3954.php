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

error_reporting(E_ERROR);
ini_set('display_errors', 1);

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleName = 'AgentManager';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

$fieldName = 'agency_no';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if ($field){
//    $field->delete();
    $sql = "update `vtiger_field` set `presence`='1' where `fieldid`=?' ";
    $adb->pquery($sql,array($field->id));
    echo "<br>Removed '$fieldName' field on $moduleName Module";
}else{
    echo "<br>'$fieldName' not have already or removed on $moduleName Module";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";