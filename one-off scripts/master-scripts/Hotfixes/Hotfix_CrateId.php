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
/*include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');*/
Vtiger_Utils::AlterTable('vtiger_crates', ' CHANGE crateid crateid VARCHAR(10)');
Vtiger_Utils::AlterTable('vtiger_crates', ' CHANGE discount discount DECIMAL(5, 1)');
Vtiger_Utils::AlterTable('vtiger_misc_accessorials', ' CHANGE discount discount DECIMAL(5, 1)');
echo "<h1>crateid Script Complete</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";