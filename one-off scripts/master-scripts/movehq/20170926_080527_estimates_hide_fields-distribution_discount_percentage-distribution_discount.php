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


// OT5390 - Remove/hide old fields in SIT details block

$module = Vtiger_Module_Model::getInstance('Estimates');
if(!$module){
    echo 'Module Estimates not present';
    return;
}
$db = PearDatabase::getInstance();
$fieldName = 'distribution_discount_percentage';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ( ! $field ) {
    echo "The field $fieldName not exist";
} else {
    if($field->presence != 1){
        $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?', [$field->id]);
    }
}

$fieldName = 'distribution_discount';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ( ! $field ) {
    echo "The field $fieldName not exist";
} else {
    if($field->presence != 1){
        $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?', [$field->id]);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";