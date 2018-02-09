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

$module = Vtiger_Module_Model::getInstance('WFConditions');
if(!$module) {
  return;
}

foreach(['agentid'=>4,'assigned_user_id'=>5] as $fieldName=>$seq) {
  $field = Vtiger_Field_Model::getInstance($fieldName,$module);

  if(!$field) {
    continue;
  }

  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $seq WHERE `fieldid` = $field->id");
}
