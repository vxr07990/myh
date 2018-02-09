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

$module = Vtiger_Module::getInstance('WFLocationTypes');
$filter = Vtiger_Filter::getInstance('All', $module);


foreach(['createdtime','agentid'] as $fieldName) {
  $field = Vtiger_Field_Model::getInstance($fieldName, $module);

  $filter->removeField($field);
}
