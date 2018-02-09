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


// OT5171 - Remove Cubic Feet Capacity field

$module = Vtiger_Module_Model::getInstance('Vehicles');
if(!$module){
    echo 'Module Vehicles not present';
    return;
}
$fieldName = 'vehicle_feetcapacity';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ( ! $field ) {
    echo "The field $fieldName not exist";
    return;
} else {
    if($field->presence != 1){
        $db = PearDatabase::getInstance();
        $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?', [$field->id]);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

