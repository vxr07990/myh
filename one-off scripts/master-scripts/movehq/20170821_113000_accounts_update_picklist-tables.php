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

//OT 19122 Industry field in customized picklist module not working correctly

//Picklist customizer needs duplicated values
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_industry` DROP INDEX `industry_industry_idx`');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_industry` ADD INDEX (`industry`)');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_accounttype` DROP INDEX `accounttype_accounttype_idx`');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_accounttype` ADD INDEX (`accounttype`)');
