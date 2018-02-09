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

$moduleName = 'Orders';
$module = Vtiger_Module::getInstance($moduleName);

if(!$module){
    return;
}

$picklistValues = [
    'Initial Order', 'Booked',
    'On Hold, Will Advise', 'Cancelled',
    'Pending Estimate', 'Estimate Received',
    'Registered', 'Residence to Dock',
    'Loaded & On Origin Dock', 'Loaded & In Transit',
    'In Transit', 'Storage @ Origin',
    'Delivered to Dock', 'Delivered to Residence',
    'Customs'
];

$field = Vtiger_Field_Model::getInstance('ordersstatus', $module);

if($field){
    updatePicklistValuesCOUOSP($field, $picklistValues);
}

function updatePicklistValuesCOUOSP($field, $pickList) {
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
    $db  = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, []);
    $field->setPicklistValues($pickList);
}
