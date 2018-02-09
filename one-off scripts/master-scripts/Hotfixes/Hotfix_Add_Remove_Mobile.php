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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//Insert NAT payment type if it doesn't exist
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = 3 WHERE `fieldname` = 'mobile' AND `tablename` = 'vtiger_leadaddress'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";