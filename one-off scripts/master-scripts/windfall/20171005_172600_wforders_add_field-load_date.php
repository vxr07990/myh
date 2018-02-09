<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$db = PearDatabase::getInstance();


$moduleInstance = Vtiger_Module::getInstance('WFOrders');
$block = Vtiger_Block::getInstance('LBL_WFORDER_INFORMATION', $moduleInstance);


$fieldName = 'load_date';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->sequence = 3;
    $block->addField($field);
}





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
