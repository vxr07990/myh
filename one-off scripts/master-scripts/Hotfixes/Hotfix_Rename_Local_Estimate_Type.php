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

// Renames all past data
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariffreportsections SET tariff_orders_type = 'Not To Exceed' WHERE tariff_orders_type = 'Do Not Exceed'");
// Renames all past labels
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariffreportsections SET tariff_orders_title = 'Not To Exceed' WHERE tariff_orders_title = 'Do Not Exceed'");
// Rename the option in the picklist table, so future data is right
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariff_orders_type SET tariff_orders_type = 'Not To Exceed' WHERE tariff_orders_type = 'Do Not Exceed'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";