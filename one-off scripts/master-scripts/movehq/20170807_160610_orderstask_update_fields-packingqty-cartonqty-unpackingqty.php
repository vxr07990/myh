<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

// OT19139 - No negative entries

$module = Vtiger_Module_Model::getInstance('OrdersTask');
if(!$module){
    echo 'Module OrdersTask not present';
    return;
}
$fields = [
    'cartonqty',
    'packingqty',
    'unpackingqty'
];
$fieldIds = [];
foreach ($fields as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field){
        echo "The field $fieldName not exist";
    }else{
        $fieldIds[] = $field->id;
    }
}
$sql = "UPDATE vtiger_field SET typeofdata = 'I~O~MIN=0' WHERE fieldid IN (". generateQuestionMarks($fieldIds).")";
$result = $db->pquery($sql,[$fieldIds]);
echo $db->getAffectedRowCount($result)." fields updated correctly<br>";

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";