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

$moduleInstance = Vtiger_Module::getInstance('DrivingViolation');

if(!$moduleInstance){
    return;
}

$filter1 = Vtiger_filter::getInstance('All', $moduleInstance);
if($filter1){
    $db = PearDatabase::getInstance();
    $sql = "SELECT * FROM `vtiger_cvccoumnlist` WHERE columnname LIKE 'vtiger_drivingviolation%'";
    $result = $db->pquery($sql, array());
    if ($db->num_rows($result) > 0){
        return;
    }
} else {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
}

$fieldArray = [];

$field1 = Vtiger_Field::getInstance('drivingviolation_employeeid', $moduleInstance);
if($field1){
    $fieldArray[] = $field1;
}
$field2 = Vtiger_Field::getInstance('drivingviolation_convtype', $moduleInstance);
if($field2){
    $fieldArray[] = $field2;
}
$field3 = Vtiger_Field::getInstance('drivingviolation_convictiondate', $moduleInstance);
if($field3){
    $fieldArray[] = $field3;
}

if(!empty($fieldArray)){
    $i = 0;
    foreach($fieldArray as $field){
        $filter1->addField($field, $i);
        $i++;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";