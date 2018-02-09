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

echo "<br> Adding columns to custom view table (for filters)<br>";

Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_customview` ADD is_agent VARCHAR(3)");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_customview` ADD agentmanager_id INT(11)");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";