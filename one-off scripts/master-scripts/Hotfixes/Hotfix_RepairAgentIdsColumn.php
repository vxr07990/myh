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


    Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_users` MODIFY COLUMN agent_ids VARCHAR(255)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";