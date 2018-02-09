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



//Hotfix_misc_accessorials_contract_enforce.php
//hot fix to add enforce field to `vtiger_misc_accessorials.

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if (Vtiger_Utils::CheckTable('vtiger_misc_accessorials')) {
    echo "<br> `vtiger_misc_accessorials` table exists, adding `from_contract` column if it doesn't already have it";
    Vtiger_Utils::AddColumn('vtiger_misc_accessorials', 'from_contract', 'INT(10) DEFAULT Null');
    echo "<br> done";
} else {
    echo "<br> `vtiger_misc_accessorials` doesn't exist, no action taken.";
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";