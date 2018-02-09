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

require_once 'vtlib/Vtiger/Module.php';

$moduleEquipment = Vtiger_Module::getInstance('Equipment');

$block = Vtiger_Block::getInstance('LBL_EQUIPMENT_INFORMATION', $moduleEquipment);
if ($block) {
    echo "<br> The LBL_EQUIPMENT_INFORMATION block already exists in Equipment <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_EQUIPMENT_INFORMATION';
    $moduleEquipment->addBlock($block);
}

//Active Field
$field = Vtiger_Field::getInstance('equipment_active', $moduleInstance);
if ($field) {
    echo "<br> The equipment_active field already exists in Equipment <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_EQUIPMENT_ACTIVE';
    $field->name = 'equipment_active';
    $field->table = 'vtiger_equipment';
    $field->column ='equipment_active';
    $field->columntype = 'varchar(100)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->defaultvalue = 'Active';
    $field->quickcreate = 0;

    $block->addField($field);
    $field->setPicklistValues(['Active', 'Inactive']);
}

$block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleEquipment);
if ($block) {
    echo "<br> The LBL_CUSTOM_INFORMATION block already exists in Equipment <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_CUSTOM_INFORMATION';
    $moduleEquipment->addBlock($block);
}

//Created By
$field = Vtiger_Field::getInstance('createdby', $moduleEquipment);
if ($field) {
    echo "<li>The createdby field already exists</li><br> \n";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_EQUIPMENT_CREATEDBY';
    $field->name = 'createdby';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smcreatorid';
    $field->uitype = 52;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;

    $block->addField($field);
}

$block = Vtiger_Block::getInstance('LBL_EQUIPMENT_FIELDS_TO_REMOVE', $moduleEquipment);
if ($block) {
    echo "<br> The LBL_EQUIPMENT_FIELDS_TO_REMOVE block already exists in Equipment <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_EQUIPMENT_FIELDS_TO_REMOVE';
    $moduleEquipment->addBlock($block);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";