<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFWarehouses');

foreach(['Active Warehouses','Inactive Warehouses'] as $filterName) {
  $filter = Vtiger_Filter::getInstance($filterName);
  if($filter) {
    $filter->delete();
  }
  $filter = new Vtiger_Filter();
  $filter->name = $filterName;
  if($filterName == 'Active Warehouses') {
    $filter->isdefault = true;
  }
  $module->addFilter($filter);

  $fieldList = ['agentid',
                'code',
                'name',
                'address',
                'address2',
                'city',
                'state',
                'country',
                'postal_code',
               ];
  foreach($fieldList as $seq=>$fieldName) {
    $field = Vtiger_Field_Model::getInstance($fieldName, $module);
    $filter->addField($field,$seq);
  }

  $field = Vtiger_Field_Model::getInstance('wfwarehouse_status', $module);

  if($filterName == 'Active Warehouses') {
    $filter->addRule($field,'EQUALS','Active',0,1,'');
  } else {
    $filter->addRule($field,'EQUALS','Inactive',0,1,'');
  }
}

$filter = Vtiger_Filter::getInstance('All', $module);
if($filter) {
  $filter->delete();
}

$filter = new Vtiger_Filter();
$filter->name = 'All';
$module->addFilter($filter);

$fieldList = ['agentid',
              'code',
              'wfwarehouse_status',
              'name',
              'address',
              'address2',
              'city',
              'state',
              'country',
              'postal_code',
             ];
foreach($fieldList as $seq=>$fieldName) {
  $field = Vtiger_Field_Model::getInstance($fieldName, $module);
  $filter->addField($field,$seq);
}
