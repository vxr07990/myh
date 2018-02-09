<?php
// OT4985
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('WFLocations');

if(!$module)
{
    return;
}

$db = &PearDatabase::getInstance();

foreach(['percentused','percentusedoverride'] as $fieldname) {
  $field = Vtiger_Field::getInstance($fieldname, $module);

  if(!$field)
  {
      return;
  }
  // Update field to presence 1 since field->update doesn't do anything
  $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?', [1, $field->id]);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
