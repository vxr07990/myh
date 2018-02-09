<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/17/2017
 * Time: 5:08 PM
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

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');

$moduleOrders = Vtiger_Module::getInstance('Orders');

$blockOrders237 = Vtiger_Block::getInstance('LBL_ORDERS_DATES', $moduleOrders);
if ($blockOrders237) {
    echo "<br> The LBL_ORDERS_DATES block already exists in Orders <br>";
} else {
    $blockOrders237 = new Vtiger_Block();
    $blockOrders237->label = 'LBL_ORDERS_DATES';
    $moduleOrders->addBlock($blockOrders237);
}

$field = Vtiger_Field::getInstance('orders_actualdeliverydate', $moduleOrders);
if ($field) {
    echo "<br> The orders_actualdeliverydate field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_ORDERSACTUALDELIVERYDATE';
    $field->name = 'orders_actualdeliverydate';
    $field->table = 'vtiger_orders';
    $field->column ='orders_actualdeliverydate';
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders237->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";