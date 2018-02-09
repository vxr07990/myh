<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/7/2017
 * Time: 2:35 PM
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

$block = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $module);

if(!$block)
{
    return;
}

$fieldName = 'driver_trip';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ($field) {
    echo "<p>$fieldName Field already present</p>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_'.strtoupper($fieldName);
    $field->name       = $fieldName;
    $field->table      = 'vtiger_orders';
    $field->column     = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype     = 10;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $field->setRelatedModules(['Employees']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";