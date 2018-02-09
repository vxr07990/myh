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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once 'includes/main/WebUI.php';

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_quotestage` (quotestageid, quotestage, sortorderid, presence) SELECT id + 1, 'Booked', id + 1, 1 FROM `vtiger_quotestage_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_quotestage` WHERE quotestage = 'Booked')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
