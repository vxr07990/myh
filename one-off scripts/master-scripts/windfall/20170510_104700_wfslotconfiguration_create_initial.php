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
$filter = new Vtiger_Filter();
$filter->name = 'All';
$moduleInstance->addFilter($filter);
$filter->addField($field1)->addField($field2, 1);
