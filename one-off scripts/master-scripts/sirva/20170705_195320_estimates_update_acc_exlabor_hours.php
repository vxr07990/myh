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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

// This is fine because they're INTs, won't nuke any data.
// This seriously requires spaces? Why even have this function smh.
Vtiger_Utils::AlterTable('vtiger_quotes', ' MODIFY COLUMN acc_exlabor_origin_hours DECIMAL(10,2)');
Vtiger_Utils::AlterTable('vtiger_quotes', ' MODIFY COLUMN acc_exlabor_dest_hours DECIMAL(10,2)');
Vtiger_Utils::AlterTable('vtiger_quotes', ' MODIFY COLUMN acc_exlabor_ot_origin_hours DECIMAL(10,2)');
Vtiger_Utils::AlterTable('vtiger_quotes', ' MODIFY COLUMN acc_exlabor_ot_dest_hours DECIMAL(10,2)');

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
