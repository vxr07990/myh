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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('Quotes');
$blockInstance = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleInstance);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_QUOTES_MILEAGE';
$field1->name = 'interstate_mileage';
$field1->tablename = 'vtiger_quotes';
$field1->column = 'interstate_mileage';
$field1->columntype = 'INT(19)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$blockInstance->addField($field1);
