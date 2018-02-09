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
$isNew = false;
global $adb;

$moduleInstance = Vtiger_Module::getInstance('WFLocations');
if (!$moduleInstance) {
    return "Locations doesn't exist";
}


$blockInstance = Vtiger_Block::getInstance('LBL_WFLOCATIONS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFLOCATIONS_INFORMATION block doesn't exist<br>";
}

$field = Vtiger_Field::getInstance('name', $moduleInstance);
if ($field) {
  // Commenting this out because the function to update fields doesn't work
  // $field->typeofdata = 'V~M';
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldid = '$field->id'");
} else {
  echo 'LBL_WFLOCATIONS_NAME doesn\'t exist';
}
