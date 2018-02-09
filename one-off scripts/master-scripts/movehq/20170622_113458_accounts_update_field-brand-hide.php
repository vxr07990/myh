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

// OT18621 - Remove/Hide Brand field

echo "Start: Hide 'brand' field from Accounts<br>";

$moduleName = 'Accounts';
$module = Vtiger_Module::getInstance($moduleName);
if($module){
    $fieldName = 'brand';
    $field = Vtiger_Field::getInstance($fieldName,$module);
    if($field){
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 1 WHERE tabid = $module->id AND fieldid = $field->id");
    }
}

echo "End: Hide 'brand' field from Accounts<br>";