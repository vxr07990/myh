<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
 * This file is intended to remove the HHG - Interstate and HHG - Intrastate values from the business_line table.
 * These business lines are handled internally as Interstate Move and Intrastate Move respectively, and the database
 * values are translated within the UI to become HHG - Interstate and HHG - Intrastate where appropriate.
 *
 * Query is also run on business_line_est table to ensure consistency.
 */

Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line` WHERE business_line IN ('HHG - Interstate', 'HHG - Intrastate')");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line_est` WHERE business_line_est IN ('HHG - Interstate', 'HHG - Intrastate')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";