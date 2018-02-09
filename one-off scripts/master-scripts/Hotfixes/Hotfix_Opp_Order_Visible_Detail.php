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



require_once 'vtlib/Vtiger/Module.php';

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `summaryfield` = '1' WHERE `vtiger_field`.`fieldname` = 'order_number' AND `tablename` = 'vtiger_potential'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";