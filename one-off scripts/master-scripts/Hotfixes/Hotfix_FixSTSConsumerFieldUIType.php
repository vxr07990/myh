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


echo "<br>fixing STS Consumer Fields UI Type";
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = 16 WHERE fieldname = 'agrmt_cod' OR fieldname = 'subagrmt_cod'");
echo "<br>Completed fixing STS Consumer Fields UI Type";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";