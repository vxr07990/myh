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


$Vtiger_Utils_Log = true;
Vtiger_Utils::AlterTable('vtiger_crates', ' MODIFY pack INT(10)');
Vtiger_Utils::AlterTable('vtiger_crates', ' MODIFY unpack INT(10)');
Vtiger_Utils::AlterTable('vtiger_crates', ' MODIFY ot_pack INT(10)');
Vtiger_Utils::AlterTable('vtiger_crates', ' MODIFY ot_unpack INT(10)');
echo "<h1>Setting Crates packing columns to INT(10) complete.</h1>";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
