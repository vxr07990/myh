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

$moduleInstance = Vtiger_Module::getInstance('WFSlotConfiguration');
if (!$moduleInstance) {
    return "SlotConfiguration doesn't exist";
}


$blockInstance = Vtiger_Block::getInstance('LBL_WFSLOTCONFIGURATION_DETAILS', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFSLOTCONFIGURATION_DETAILS block doesn't exist<br>";
}

$fieldName = 'agentid';
$fieldLabel = 'LBL_WFSLOTCONFIGURATION_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_crmentity';
    $field->column = $fieldName;
    $field->uitype = 1002;
    $field->typeofdata = 'I~M';
    $field->sequence = 15;
    $blockInstance->addField($field);
    $moduleInstance->setEntityIdentifier($field);
}
