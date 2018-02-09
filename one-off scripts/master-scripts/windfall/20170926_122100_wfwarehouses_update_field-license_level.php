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
$moduleInstance = Vtiger_Module_Model::getInstance('WFWarehouses');


if(!$moduleInstance){
    return;
}
$blockInstance = Vtiger_Block::getInstance('LBL_WFWAREHOUSE_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFWAREHOUSE_INFORMATION block doesn't exist<br>";
}

$fieldName = 'license_level';
$fieldLabel = 'LBL_WFWAREHOUSE_' . strtoupper($fieldName);

$fieldInstance = Vtiger_Field_Model::getInstance('license_level', $moduleInstance);

if($fieldInstance && $fieldInstance->table != $moduleInstance->basetable){
    $fieldInstance->delete();
    $fieldInstance = new Vtiger_Field();
    $fieldInstance->label = $fieldLabel;
    $fieldInstance->name = $fieldName;
    $fieldInstance->table = $moduleInstance->basetable;
    $fieldInstance->column = $fieldName;
    $fieldInstance->columntype = 'VARCHAR(255)';
    $fieldInstance->uitype = 16;
    $fieldInstance->typeofdata = 'V~M';
    $fieldInstance->setPicklistValues(['Unlimited', 'LITE', 'Basic', 'Essentials']);
    $blockInstance->addField($fieldInstance);
}

Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_crmentity` DROP COLUMN license_level');

