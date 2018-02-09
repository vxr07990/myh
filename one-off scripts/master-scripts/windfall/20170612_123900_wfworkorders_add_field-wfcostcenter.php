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

$moduleInstance = Vtiger_Module::getInstance('WFWorkOrders');
if (!$moduleInstance) {
    return "WFWorkOrders doesn't exist";
}


$blockInstance = Vtiger_Block::getInstance('LBL_WFWORKORDERS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFWORKORDERS_INFORMATION block doesn't exist<br>";
}

$checkField = Vtiger_Field::getInstance('wfworkorder_costcenter', $moduleInstance);
if($checkField) {
  $checkField->delete();
}

Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_wfworkorders` DROP COLUMN `wfworkorder_costcenter`');

$fieldName = 'wfworkorder_wfcostcenter';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 8;
    $blockInstance->addField($field);
}
