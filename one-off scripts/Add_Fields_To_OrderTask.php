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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('OrdersTask');

$block1 = new Vtiger_Block();
$block1 = $block1->getInstance('LBL_ORDERS_TASK_INFORMATION', $module);

$field1 = new Vtiger_Field();
$field1->label = 'Start Hour';
$field1->name = 'start_hour';
$field1->table = 'vtiger_orderstask';
$field1->column = 'start_hour';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'End Hour';
$field1->name = 'end_hour';
$field1->table = 'vtiger_orderstask';
$field1->column = 'end_hour';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';

$block1->addField($field1);

$block1->save($module);
