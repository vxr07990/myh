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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('WFArticles');

//Making fields not required
$removeMandatory = [
  'vendor',
  'vendor_num',
  'part_num',
];

foreach($removeMandatory as $field) {
  $fieldInstance = Vtiger_Field::getInstance($field, $module);
  $db->pquery("UPDATE vtiger_field SET typeofdata = ? WHERE fieldid = ?;",['V~O',$fieldInstance->id]);
}
