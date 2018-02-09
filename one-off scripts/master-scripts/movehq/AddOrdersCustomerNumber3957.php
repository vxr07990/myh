<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/22/2017
 * Time: 12:38 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once ('libraries/MoveCrm/AccountingIntegration.php');

$module  = Vtiger_Module::getInstance('Orders');
if(!$module)
{
    return;
}
$block = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $module);
if(!$block)
{
    return;
}
$field = Vtiger_Field::getInstance('orders_custnum', $module);
if ($field) {
    echo "The orders_custnum field already exists<br>\n";
} else {
    $db = &PearDatabase::getInstance();
    $db->pquery('UPDATE vtiger_field SET sequence=sequence+1 WHERE block=?',
                [$block->id]);
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERS_CUSTOMER_NUMBER';
    $field->name       = 'orders_custnum';
    $field->table      = 'vtiger_orders';
    $field->column     = 'orders_custnum';
    $field->columntype = 'INT(11)';
    $field->uitype     = 7;
    $field->sequence = 1;
    $field->typeofdata = 'I~O';
    $block->addField($field);
    $field = Vtiger_Field::getInstance('orders_custnum', $module);
}

\MoveCrm\AccountingIntegration::setFieldUIType($field->id, 'Customer', null);

