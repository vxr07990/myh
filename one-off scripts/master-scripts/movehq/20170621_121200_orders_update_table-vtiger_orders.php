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

// OT18712 - Bug: Format of GBL Number field should allow alpha-numeric

echo 'Start: Change datatype of gbl_number column of vtiger_orders to varchar(100)<br>';

$sqlquery = "ALTER TABLE `vtiger_orders` CHANGE COLUMN `gbl_number` `gbl_number` VARCHAR(100) NULL DEFAULT NULL";
Vtiger_Utils::ExecuteQuery($sqlquery);

echo 'End: Change datatype of gbl_number column of vtiger_orders to varchar(100)<br>';