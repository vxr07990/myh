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


include_once('vtlib/Vtiger/Module.php');

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `presence` = 1 WHERE `tabid` = 60 AND `fieldname` = 'phone_estimate'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";