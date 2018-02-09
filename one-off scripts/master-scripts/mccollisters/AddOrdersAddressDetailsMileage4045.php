<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/26/2017
 * Time: 8:59 AM
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

$field = Vtiger_Field::getInstance('orders_address_miles', $module);
if ($field) {
    echo "The orders_address_miles field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_ADDRESS_MILES';
    $field->name       = 'orders_address_miles';
    $field->table      = 'vtiger_orders';
    $field->column     = 'orders_address_miles';
    $field->columntype = 'INT(10)';
    $field->uitype     = 7;
    $field->typeofdata = 'I~O';
    $block->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";