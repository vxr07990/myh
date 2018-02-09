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
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleName = 'Equipment';
$moduleEquipment = Vtiger_Module::getInstance($moduleName);

if (!$moduleEquipment) {
    print "Module: Equipment not found.\n";
    return;
}

$db = PearDatabase::getInstance();

foreach (['quantity','time_in'] as $fieldToRemove) {
    $field = Vtiger_Field::getInstance($fieldToRemove, $moduleEquipment);
    if (!$field) {
        print "Failed to find: $fieldToRemove in $moduleName\n";
        continue;
    }
    if ($field->presence != 1) {
        $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
        $db->pquery($stmt,[1,$field->id]);
    }
}
print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";