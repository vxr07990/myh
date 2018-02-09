<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/28/2017
 * Time: 9:39 AM
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

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('OrdersTask');
if(!$module)
{
    return;
}
$block = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $module);
if(!$block)
{
    return;
}

$field = Vtiger_Field::getInstance('orderstask_account', $module);
if ($field) {
    echo "The orderstask_account field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_ORDERSTASK_ACCOUNT';
    $field->name       = 'orderstask_account';
    $field->table      = 'vtiger_orderstask';
    $field->column     = 'orderstask_account';
    $field->columntype = 'INT(11), ADD INDEX (orderstask_account)';
    $field->uitype     = 10;
    $field->typeofdata = 'I~O';
    $field->displaytype = 3;
    $block->addField($field);
    $field->setRelatedModules(['Accounts']);
}


$db->pquery('UPDATE vtiger_orderstask,vtiger_orders SET orderstask_account=orders_account WHERE vtiger_orderstask.ordersid=vtiger_orders.ordersid');



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";