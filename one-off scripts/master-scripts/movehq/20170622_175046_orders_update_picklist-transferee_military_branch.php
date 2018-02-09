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

// OT18714 - Bug: Remove US Navy choice from picklist

$moduleName = "Orders";
$optionName = "US Navy";
$fieldName = "transferee_military_branch";

echo "Start: Remove $optionName option from $fieldName picklist<br>";

$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "Module $moduleName not present";
    return;
}
$field = Vtiger_Field::getInstance($fieldName, $module);
if(!$field){
    echo "Field $fieldName not found";
    return;
}

$db = PearDatabase::getInstance();
$sql = "DELETE FROM vtiger_$fieldName WHERE $fieldName = '$optionName'";
$result = $db->pquery($sql);

echo "Start: Remove $optionName option from $fieldName picklist<br>";