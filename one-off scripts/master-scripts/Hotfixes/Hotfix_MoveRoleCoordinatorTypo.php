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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>Begin hotfix repair customer service coordinator...";
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_moveroles_role` SET moveroles_role = 'Customer Service Coordinator' WHERE moveroles_role = 'Customer Service Cordinator'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_moveroles` SET moveroles_role = 'Customer Service Coordinator' WHERE moveroles_role = 'Customer Service Cordinator'");
echo "done.<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";