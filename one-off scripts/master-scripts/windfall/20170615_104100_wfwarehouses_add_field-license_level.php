<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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


$moduleInstance = Vtiger_Module::getInstance('WFWarehouses');
if (!$moduleInstance) {
    return;
}



$blockInstance = Vtiger_Block::getInstance('LBL_WFWAREHOUSE_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFWAREHOUSE_INFORMATION block doesn't exist<br>";
}

$fieldName = 'license_level';
$fieldLabel = 'LBL_WFWAREHOUSE_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->setPicklistValues(array('Unlimited', 'LITE', 'Basic', 'Essentials'));
    $blockInstance->addField($field);
}
