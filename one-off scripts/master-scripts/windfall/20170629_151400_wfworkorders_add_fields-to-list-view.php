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
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFWorkOrders');
$filter = Vtiger_Filter::getInstance('All', $module);
$fields = ['wfworkorder_warehouse' => 0,
           'wfaccount' => 1,
           'wfworkorder_priority' => 2,
           'wfworkorder_status_history' => 3,
           'wfworkorder_scheduled' => 4,
           'wfworkorder_request_name' => 5];

foreach($fields as $field=>$seq) {
  $fieldInstance = Vtiger_Field_Model::getInstance($field, $module);

  if($fieldInstance) {
    echo "Adding $field->name to filter";
    $filter->addField($fieldInstance, $seq);
  }
}
