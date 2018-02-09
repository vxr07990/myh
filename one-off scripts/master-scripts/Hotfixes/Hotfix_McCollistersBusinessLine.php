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


Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line` WHERE business_line != 'Auto Transportation'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";