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

//Adds this field if it doesn't exist, if it does.. well it will just throw an error.. which we will ignore, obviously
Vtiger_Utils::ExecuteQuery("ALTER TABLE  `vtiger_emailtemplates` ADD `owner_id` INT NULL");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_emailtemplates` SET `owner_id` = 1");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";