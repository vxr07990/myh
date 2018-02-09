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

$db = PearDatabase::getInstance();
$moduleName = 'Documents';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

$summaryRemove = ['folderid'];
$summaryAdd = ['invoice_packet_include'];

if ($moduleInstance) {
    print "Module: $moduleName found.\n";
    foreach ($summaryAdd as $workingFieldName) {
        print "Checking: $workingFieldName in $moduleName\n";
        $field = Vtiger_Field::getInstance($workingFieldName, $moduleInstance);
        if (!$field) {
            print "Failed to find: $workingFieldName in $moduleName\n";
            continue;
        }
        if ($field->summaryfield == 0) {
            print "Updated $moduleName: $workingFieldName -- setting summary field to 1\n";
            $stmt = 'UPDATE `vtiger_field` SET `summaryfield`=? WHERE `fieldid`=? LIMIT 1';
            $db->pquery($stmt, [1, $field->id]);
        }
    }
    foreach ($summaryRemove as $workingFieldName) {
        print "Checking: $workingFieldName in $moduleName\n";
        $field = Vtiger_Field::getInstance($workingFieldName, $moduleInstance);
        if (!$field) {
            print "Failed to find: $workingFieldName in $moduleName\n";
            continue;
        }
        if ($field->summaryfield == 1) {
            print "Updated $moduleName: $workingFieldName -- set summaryfield to 0\n";
            $stmt = 'UPDATE `vtiger_field` SET `summaryfield`=? WHERE `fieldid`=? LIMIT 1';
            $db->pquery($stmt, [0, $field->id]);
        }
    }
}

print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";