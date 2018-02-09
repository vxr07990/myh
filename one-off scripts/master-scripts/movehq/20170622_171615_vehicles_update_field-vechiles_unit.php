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


// OT18704 - Make Vehicle Unit # Required Field

$module = Vtiger_Module_Model::getInstance('Vehicles');
if(!$module){
    echo 'Module Vehicles not present';
    return;
}
$fieldName = 'vechiles_unit';//it is miss spell on purpose, it is the actual name in the db
$field = Vtiger_Field::getInstance($fieldName, $module);
if(!$field){
    echo "The field $fieldName not exist";
    return;
}

$db = PearDatabase::getInstance();
$sql_1 = "UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldid = ?";
$result = $db->pquery($sql_1,[$field->id]);

$sql_2 = "UPDATE vtiger_vehicles SET vechiles_unit = CONCAT('Vehicle ',vehiclesid) WHERE vechiles_unit = ''";
$result2 = $db->pquery($sql_2);


