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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleInstance = Vtiger_Module::getInstance('WFSlotConfiguration');
$filter = Vtiger_Filter::getInstance('All', $moduleInstance);
if ($filter) {
    $filter->delete(); //IF exists delete this filter to add the new columns
}
$field1 = Vtiger_Field::getInstance('code', $moduleInstance);
$field2 = Vtiger_Field::getInstance('description', $moduleInstance);
$field3 = Vtiger_Field::getInstance('agentid', $moduleInstance);
$field4 = Vtiger_Field::getInstance('label1', $moduleInstance);
$field5 = Vtiger_Field::getInstance('slotpercentage1', $moduleInstance);
$field6 = Vtiger_Field::getInstance('label2', $moduleInstance);
$field7 = Vtiger_Field::getInstance('slotpercentage2', $moduleInstance);
$field8 = Vtiger_Field::getInstance('label3', $moduleInstance);
$field9 = Vtiger_Field::getInstance('slotpercentage3', $moduleInstance);
$field10 = Vtiger_Field::getInstance('label4', $moduleInstance);
$field11 = Vtiger_Field::getInstance('slotpercentage4', $moduleInstance);
$field12 = Vtiger_Field::getInstance('label5', $moduleInstance);
$field13 = Vtiger_Field::getInstance('slotpercentage5', $moduleInstance);
$field14 = Vtiger_Field::getInstance('label6', $moduleInstance);
$field15 = Vtiger_Field::getInstance('slotpercentage6', $moduleInstance);


$filter = new Vtiger_Filter();
$filter->name = 'All';
$moduleInstance->addFilter($filter);

$filter->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6);
$filter->addField($field8, 7)->addField($field9, 8)->addField($field10, 9)->addField($field11, 10)->addField($field12, 11);
$filter->addField($field13, 12)->addField($field14, 13)->addField($field15, 14);
