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

$db = PearDatabase::getInstance();

//Only adds the column if it doesn't exist. If it does, it will ignore it and throw a warning, which we will ignore.
Vtiger_Utils::ExecuteQuery("ALTER TABLE  `vtiger_quotes` ADD  `sts_vehicles` TEXT NULL AFTER `pricing_type`");
Vtiger_Utils::ExecuteQuery("ALTER TABLE  `vtiger_potential` ADD  `register_sts` TINYINT NOT NULL");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";