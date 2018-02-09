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

$module = Vtiger_Module::getInstance('Employees');

$field1 = Vtiger_Field::getInstance('employee_type', $module);
$field2 = Vtiger_Field::getInstance('name', $module);
$field3 = Vtiger_Field::getInstance('employee_lastname', $module);
$field4 = Vtiger_Field::getInstance('employee_hphone', $module);
$field5 = Vtiger_Field::getInstance('employee_mphone', $module);

$filter1 = new Vtiger_Filter();
$filter1->name = 'Contractors';
$filter1->isdefault = true;
$module->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addRule($field1, 'EQUALS', 'Contractor');;
