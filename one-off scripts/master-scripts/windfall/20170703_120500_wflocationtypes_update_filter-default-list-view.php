<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 7/3/2017
 * Time: 10:26 AM
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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');


$moduleInstance = Vtiger_Module::getInstance('WFLocationTypes');
$filter = Vtiger_Filter::getInstance('All', $moduleInstance);
if ($filter) {
    $filter->delete(); //IF exists delete this filter to add the new columns
}
$field1 = Vtiger_Field::getInstance('warehouse', $moduleInstance);
$field2 = Vtiger_Field::getInstance('wflocationtypes_prefix', $moduleInstance);
$field3 = Vtiger_Field::getInstance('wflocationtypes_type', $moduleInstance);
$field4 = Vtiger_Field::getInstance('base', $moduleInstance);
$field5 = Vtiger_Field::getInstance('container', $moduleInstance);


$filter = new Vtiger_Filter();
$filter->name = 'All';
$moduleInstance->addFilter($filter);

$filter->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4);
