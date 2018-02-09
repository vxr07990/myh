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



//Hotfix_reports_AgentManager_password.php
//adds the `custom_reports_pw` field in `vtiger_agentmanager` for connectiong to our reports engine.

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if (Vtiger_Utils::CheckTable('vtiger_agentmanager')) {
    echo "<br> `vtiger_agentmanager` table exists, adding `custom_reports_pw` column if it doesn't already have it";
    Vtiger_Utils::AddColumn('vtiger_agentmanager', 'custom_reports_pw', 'VARCHAR(100)');
    echo "<br> done";
} else {
    echo "<br> `vtiger_agentmanager` doesn't exist, no action taken.";
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";