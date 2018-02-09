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


include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

$modulOpp = Vtiger_Module::getInstance('Opportunities');

$adb->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?", array(2, Vtiger_Field::getInstance('move_type', $modulOpp)->id));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";