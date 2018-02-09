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
    'opportunities_vanline',
    'opportunities_reason'
];
$db = PearDatabase::getInstance();
$moduleName = 'Opportunities';
$moduleEquipment = Vtiger_Module::getInstance($moduleName);

if ($moduleEquipment) {
    foreach ($fieldsToHide as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $moduleEquipment);
        if (!$field) {
            print "Failed to find: $fieldName in $moduleName\n";
            continue;
        }

        if ($field->presence != 1) {
            print "Updated $moduleName: $fieldName\n";
            $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
            $db->pquery($stmt, [1, $field->id]);
        }

    }
}

print "END: " . __FILE__ . "<br />\n";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";