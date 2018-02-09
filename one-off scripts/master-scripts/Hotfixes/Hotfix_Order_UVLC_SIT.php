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

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 0 WHERE fieldname LIKE 'ori_sit2_container_or_warehouse'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 0 WHERE fieldname LIKE 'des_sit2_container_or_warehouse'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";