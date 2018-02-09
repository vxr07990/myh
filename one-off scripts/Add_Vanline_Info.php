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


$module = Vtiger_Module::getInstance('Users');
$block1 = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $module);

$field1 = new Vtiger_Field();
$field1->label = 'Vanline';
$field1->name = 'vanline';
$field1->table = 'vtiger_users';
$field1->column = 'vanline';
$field1->columntype = 'VARCHAR(75)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Custom Reports Password';
$field1->name = 'custom_reports_pw';
$field1->table = 'vtiger_users';
$field1->column = 'custom_reports_pw';
$field1->columntype = 'VARCHAR(25)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';
$field1->displaytype = '4';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Vanline ID';
$field1->name = 'vanline_id';
$field1->table = 'vtiger_users';
$field1->column = 'vanline_id';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$block1->save($module);
