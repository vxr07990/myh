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

// OT5249 - Claim Status Pick list

$moduleName = 'ClaimsSummary';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    echo "Module $moduleName not found.";
    return;
}

$fieldName = 'item_status';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    echo "<br> Field $fieldName not found <br>";
    return;
} else {
    // Remove existing values except Booked and Cancelled
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE vtiger_".$fieldName);
    $field->setPicklistValues(['Open','Closed']);
    
    //set default value 'Open'
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET defaultvalue = 'Open' WHERE fieldname = '$field->name'");
    
    //set item_status = 'Open' for records that has no ordersstatus value on db or values are diferent from Open or Closed
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_claimitems SET $fieldName = 'Open' WHERE ( $fieldName IS NULL OR $fieldName = '' OR ( $fieldName != 'Open' AND $fieldName != 'Closed'))");
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_claimssummary SET $fieldName = 'Open' WHERE ( $fieldName IS NULL OR $fieldName = '' OR ( $fieldName != 'Open' AND $fieldName != 'Closed'))");
}