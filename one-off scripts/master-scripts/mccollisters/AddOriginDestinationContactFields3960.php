<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/24/2017
 * Time: 9:16 AM
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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Orders');

if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS', $module);

if(!$block)
{
    return;
}

$field = Vtiger_Field::getInstance('origin_contact', $module);
if ($field) {
    echo "The origin_contact field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_ORIGIN_CONTACT';
    $field->name       = 'origin_contact';
    $field->table      = 'vtiger_orders';
    $field->column     = 'origin_contact';
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('destination_contact', $module);
if ($field) {
    echo "The destination_contact field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_DESTINATION_CONTACT';
    $field->name       = 'destination_contact';
    $field->table      = 'vtiger_orders';
    $field->column     = 'destination_contact';
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";