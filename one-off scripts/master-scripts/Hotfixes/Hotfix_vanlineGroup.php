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



//vanlineGroupHotfix.php
//adds grouptype column to vtiger_groups so that vanline groups can be distinguished from agent groups.

//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

if (Vtiger_Utils::CheckTable('vtiger_groups')) {
    echo "<br> groups table exists, adding grouptype column if it doesn't already have it";
    Vtiger_Utils::AddColumn('vtiger_groups', 'grouptype', 'TINYINT(2)');
    echo "<br> done";
} else {
    echo "<br> vtiger_groups doesn't exist, no action taken.";
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";