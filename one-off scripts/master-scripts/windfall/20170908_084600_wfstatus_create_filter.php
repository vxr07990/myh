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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$module = Vtiger_Module_Model::getInstance('WFStatus');

$filter = Vtiger_Filter::getInstance('All', $module);
if($filter) {
  $filter->delete();
}

$filter = new Vtiger_Filter();
$filter->name = 'All';

$module->addFilter($filter);

$field = Vtiger_Field::getInstance('wfstatus_code',$module);

if($field) {
  $filter->addField($field,1);
}

$field = Vtiger_Field::getInstance('wfstatus_description',$module);

if($field) {
  $filter->addField($field,2);
}
