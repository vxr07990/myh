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
require_once('include/Webservices/Create.php');

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;

$moduleName = 'WFConditions';

$moduleInstance = Vtiger_Module::getInstance($moduleName);
if (!$moduleInstance) {
    return "WFConditions doesn't exist";
}


$blockInstance = Vtiger_Block::getInstance('LBL_WFCONDITIONS_DETAILS', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFCONDITIONS_DETAILS block doesn't exist<br>";
}

$fieldDefault = Vtiger_Field::getInstance('is_default', $moduleInstance);
if ($fieldDefault) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 56, `displaytype` = 3 WHERE `fieldid` = $fieldDefault->id");
} else {
    $fieldDefault             = new Vtiger_Field();
    $fieldDefault->label      = 'LBL_IS_DEFAULT';
    $fieldDefault->name       = 'is_default';
    $fieldDefault->table      = 'vtiger_wfconditions';
    $fieldDefault->column     = 'is_default';
    $fieldDefault->columntype = 'VARCHAR(3)';
    $fieldDefault->uitype     = 56;
    $fieldDefault->displaytype = 3;
    $fieldDefault->readonly = 0;
    $fieldDefault->typeofdata = 'V~O';
    $blockInstance->addField($fieldDefault);
}
