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

// OT5270 Order Module - Add Lead Source Field

$module = Vtiger_Module::getInstance('Orders');
$block = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $module);

$field = Vtiger_Field::getInstance('leadsource', $module);
if ($field) {
    echo "<li>The leadsource field already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_LEADSOURCE';
    $field->name = 'leadsource';
    $field->table = 'vtiger_orders';
    $field->column = 'leadsource';
    $field->uitype = 1500;
    $field->typeofdata = 'V~O';
    $field->columntype = 'VARCHAR(255)';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->presence = 2;

    $block->addField($field);
}