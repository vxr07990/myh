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



//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//*/


Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_languages` WHERE `languages` = 'Spanish' OR `languages` = 'Others'");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
