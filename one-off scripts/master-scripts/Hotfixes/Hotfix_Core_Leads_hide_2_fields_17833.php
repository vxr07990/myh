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

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Leads';
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    print "failed to open module ($moduleName).\n";
}

$db  = PearDatabase::getInstance();
$newPresence = 1;

foreach (['city','country'] as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if (!$field) {
        print "Field: $fieldName not found\n";
        continue;
    }
    if ($field->presence != $newPresence) {
        print "Update presence for $fieldName in $moduleName to $newPresence.\n";
        $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ?';
        $db->pquery($sql, [$newPresence, $field->id]);
    }
}
echo "<h3>Ending ". __FILE__ . "</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";