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


//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$fieldsToHide = [
    'accessorial_space_reserve_bool',
    'accesorial_exclusive_vehicle',
    'accesorial_ot_packing',
    'accesorial_ot_unpacking'
];
$db = PearDatabase::getInstance();
$moduleName = 'Estimates';
$moduleEquipment = Vtiger_Module::getInstance($moduleName);

if ($moduleEquipment) {
    print "Module: Estimates found.\n";
    foreach ($fieldsToHide as $fieldToRemove) {
        $field = Vtiger_Field::getInstance($fieldToRemove, $moduleEquipment);
        if (!$field) {
            print "Failed to find: $fieldToRemove in $moduleName\n";
            continue;
        }
        if ($field->presence != 1) {
            print "Updated Estimates: $fieldToRemove\n";
            $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
            $db->pquery($stmt, [1, $field->id]);
        }
    }
}

$db = PearDatabase::getInstance();
$moduleName = 'Quotes';
$moduleEquipment = Vtiger_Module::getInstance($moduleName);

if ($moduleEquipment) {
    print "Module: Quotes found.\n";
    foreach ($fieldsToHide as $fieldToRemove) {
        $field = Vtiger_Field::getInstance($fieldToRemove, $moduleEquipment);
        if (!$field) {
            print "Failed to find: $fieldToRemove in $moduleName\n";
            continue;
        }
        if ($field->presence != 1) {
            print "Updated Quotes: $fieldToRemove\n";
            $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
            $db->pquery($stmt, [1, $field->id]);
        }
    }
}
print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";