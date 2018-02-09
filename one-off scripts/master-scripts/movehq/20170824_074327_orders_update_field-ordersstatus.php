<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT5280 - Picklist Customizer - Blank State & Default Values


$moduleName = 'Orders';
$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "Module $moduleName not found.";
    return;
}
$fieldName = 'ordersstatus';

//updating uitype
$field = Vtiger_Field::getInstance($fieldName, $module);
if(!$field){
    echo "Field $fieldName don't exist.<br>";
    return;
}
$sql = "UPDATE vtiger_field SET uitype = '1500' WHERE fieldid = ? LIMIT 1";
$result = $db->pquery($sql,array($field->id));
echo "<li>$fieldName field updated<br>";
//reload field beacause uitype changed
$field = Vtiger_Field::getInstance($fieldName, $module);
//adding special options
$values = array('Booked','Cancelled');
$field->setPicklistSpecialValues($values);
echo "<li>Special values added<br>";