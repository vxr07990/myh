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
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Menu.php');

$module = Vtiger_Module::getInstance('Cubesheets');
$block = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $module);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_CUBESHEETS_ISPRIMARY';
$field1->name = 'is_primary';
$field1->table = 'vtiger_cubesheets';
$field1->column = 'is_primary';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';
$field1->summaryfield = 1;

$block->addField($field1);

$block->save();

$module = Vtiger_Module::getInstance('Quotes');
$block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_QUOTES_ISPRIMARY';
$field1->name = 'is_primary';
$field1->table = 'vtiger_quotes';
$field1->column = 'is_primary';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';
$field1->summaryfield = 1;

$block->addField($field1);

$block->save();
