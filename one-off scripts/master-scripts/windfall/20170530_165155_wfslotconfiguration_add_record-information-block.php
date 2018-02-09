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

$moduleInstance = Vtiger_Module::getInstance('WFSlotConfiguration');
if (!$moduleInstance) {
    return "SlotConfiguration doesn't exist";
}
$block = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleInstance);
if ($block) {
    echo "<h3>The LBL_RECORD_UPDATE_INFORMATION block already exists</h3><br> \n";
} else {
    $block        = new Vtiger_Block();
    $block->label = 'LBL_RECORD_UPDATE_INFORMATION';
    $moduleInstance->addBlock($block);
}

$field2 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$field2) {
    $field2              = new Vtiger_Field();
    $field2->label       = 'LBL_WFSLOTCONFIGURATION_CREATEDTIME';
    $field2->name        = 'createdtime';
    $field2->table       = 'vtiger_crmentity';
    $field2->column      = 'createdtime';
    $field2->uitype      = 70;
    $field2->typeofdata  = 'T~O';
    $field2->displaytype = 2;

    $block->addField($field2);
}

$field3 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field3) {
    $field3              = new Vtiger_Field();
    $field3->label       = 'LBL_WFSLOTCONFIGURATION_MODIFIEDTIME';
    $field3->name        = 'modifiedtime';
    $field3->table       = 'vtiger_crmentity';
    $field2->column      = 'modifiedtime';
    $field3->uitype      = 70;
    $field3->typeofdata  = 'T~O';
    $field3->displaytype = 2;

    $block->addField($field3);
}
$field4 = Vtiger_Field::getInstance('createdby', $moduleInstance);
if ($field4) {
    echo "The createdby field already exists<br>\n";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'LBL_WFSLOTCONFIGURATION_CREATEDBY';
    $field4->name       = 'createdby';
    $field4->table      = 'vtiger_crmentity';
    $field4->column     = 'smcreatorid';
    $field4->uitype     = 52;
    $field4->typeofdata = 'V~O';
    $field4->displaytype = 2;
    $block->addField($field4);
}

$blockInstance = Vtiger_Block::getInstance('LBL_WFSLOTCONFIGURATION_DETAILS', $moduleInstance);
if (!$blockInstance) {
    return "The LBL_WFSLOTCONFIGURATION_DETAILS block doesn't exist<br>";
}


$field1 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field1) {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_WFSLOTCONFIGURATION_ASSIGNED_TO';
    $field1->name       = 'assigned_user_id';
    $field1->table      = 'vtiger_crmentity';
    $field1->column     = 'smownerid';
    $field1->uitype     = 53;
    $field1->typeofdata = 'V~M';

    $blockInstance->addField($field1);
}

