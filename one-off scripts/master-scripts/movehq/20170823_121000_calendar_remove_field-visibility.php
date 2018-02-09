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


//OT5321	Event Module - Remove / Hide "Visibility" field

$module = Vtiger_Module_Model::getInstance('Events');
if(!$module){
    echo 'Module Events not present';
    return;
}

$fieldName = 'visibility';//it is miss spell on purpose, it is the actual name in the db
$field = Vtiger_Field::getInstance($fieldName, $module);
if($field){
    $db = PearDatabase::getInstance();
    $sql_1 = "UPDATE vtiger_field SET presence = 1 WHERE fieldid = ?";
    $result = $db->pquery($sql_1,[$field->id]);
}



