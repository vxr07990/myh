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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
// Some zip codes are int(10), should be varchar to store leading zeroes or nonnumeric characters

$db = PearDatabase::getInstance();
$moduleName = 'Orders';
$fieldName = 'orders_no';
$uitype = 4;

$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "NO $moduleName MODULE?<br />\n";
    return;
}

echo "$moduleName Module exists checking $fieldName field<br />\n";
$field3 = Vtiger_Field::getInstance($fieldName, $module);
if (!$field3) {
    echo "NO $fieldName column in The actual table?<br />\n";
    return;
}

if ($field3->uitype == $uitype) {
    echo "$fieldName is already $uitype.<br />\n";
    return;
}
$db   = PearDatabase::getInstance();
$stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid`=?';
$db->pquery($stmt, [$uitype, $field3->id]);
echo "Done with $moduleName -- $fieldName fixes<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";