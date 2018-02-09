<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT18702 - Local Operation Task Allows Negative Numbers for Crew and Vehicles

echo 'Start: Updating typofdata<br />\n';
//update typeofdata to add option min=0

$moduleName = 'OrdersTask';
$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "Module $moduleName not found.";
    return;
}
$fieldNames = [
    'num_of_personal',
    'num_of_vehicle'
];

foreach ($fieldNames as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field){
        echo "Field $fieldName don't exist.<br>";
        continue;
    }
    $sql = "UPDATE vtiger_field SET typeofdata = 'I~O~MIN=0' WHERE fieldid = ? LIMIT 1";
    $result = $db->pquery($sql,array($field->id));
    echo "<li>$fieldName field updated<br>";
}

echo 'Finish: Updating typofdata<br />\n';