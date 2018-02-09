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

$moduleName = 'WFLocationHistory';
$fieldNames = [
    'from_slot',
    'to_slot'
];

$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    return;
}
foreach ($fieldNames as $fieldName){
    $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
    if($field){
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 3333 WHERE `fieldid` = ".$field->id);
    }
}

