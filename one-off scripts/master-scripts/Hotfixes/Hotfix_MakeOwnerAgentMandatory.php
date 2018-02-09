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
require_once('includes/main/WebUI.php');

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='I~M' WHERE fieldname='agentid' AND typeofdata='I~O'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";