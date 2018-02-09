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


echo "<h1> ADDING CUSTOM RATE TO PACKING ITEMS TABLE</h1>";
Vtiger_Utils::AddColumn('vtiger_packing_items', 'custom_rate', 'DECIMAL(12,2)');
Vtiger_Utils::AddColumn('vtiger_quotes', 'apply_custom_sit_rate_override', 'VARCHAR(3)');
Vtiger_Utils::AddColumn('vtiger_quotes', 'apply_custom_pack_rate_override', 'VARCHAR(3)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";