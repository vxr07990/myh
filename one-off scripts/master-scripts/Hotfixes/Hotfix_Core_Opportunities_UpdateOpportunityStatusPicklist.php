<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$module = Vtiger_Module::getInstance('Opportunities');

if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('opportunitystatus', $module);
if ($field) {
    $db = PearDatabase::getInstance();
    $newValues = [
    'New',
    'Pending Estimate',
    'Estimate Received',
    'Closed Won',
    'Lost',
    'Cancelled'
    ];
    echo "Dumping old stuff making it new<br />\n";
    $db->pquery('TRUNCATE TABLE `vtiger_opportunitystatus`');
    $field->setPicklistValues($newValues);
}
