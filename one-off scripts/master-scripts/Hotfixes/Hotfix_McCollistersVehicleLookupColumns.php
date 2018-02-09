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

Vtiger_Utils::AddColumn('vtiger_vehiclelookup', 'is_non_standard', 'TINYINT(1)');
Vtiger_Utils::AddColumn('vtiger_vehiclelookup', 'inoperable', 'TINYINT(1)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";