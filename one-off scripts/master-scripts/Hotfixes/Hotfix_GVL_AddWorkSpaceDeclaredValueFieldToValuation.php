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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/6/2016
 * Time: 2:27 PM
 */

echo __FILE__.PHP_EOL;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Orders');

if (!$module) {
    return;
}

$block = Vtiger_Block::getInstance('LBL_ORDERS_BLOCK_VALUATION', $module);

if (!$block) {
    return;
}

$field = Vtiger_Field::getInstance('valuation_declared_value', $module);
if ($field) {
    echo "The valuation_declared_value field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_VALUATION_DECLARED_VALUE';
    $field->name       = 'valuation_declared_value';
    $field->table      = 'vtiger_orders';
    $field->column     = 'valuation_declared_value';
    $field->columntype = 'DECIMAL(13,2)';
    $field->uitype     = 71;
    $field->typeofdata = 'N~O';
    $block->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";