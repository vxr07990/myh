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

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = (SELECT `blockid` FROM `vtiger_blocks` WHERE `tabid` = (SELECT tabid FROM `vtiger_tab` WHERE `name` LIKE 'Estimates') AND `blocklabel` LIKE 'LBL_QUOTES_INTERSTATEMOVEDETAILS') WHERE `tablename` LIKE 'vtiger_quotes' AND `fieldname` LIKE 'accesorial_fuel_surcharge'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";