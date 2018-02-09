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

$moduleName = 'Vehicles';

$module = Vtiger_Module::getInstance($moduleName);

if ($module) {
    $fields = ['vehicle_tareweight','vehicle_outsideheight'];
    foreach ($fields as $fieldName){
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
	    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET presence=1 WHERE fieldid=$field->id");
        }
    }
}
echo "OK<br>\n";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";