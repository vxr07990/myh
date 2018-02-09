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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$orderTabId = $db->pquery("SELECT tabid FROM `vtiger_tab` WHERE name = 'Orders'", [])->fetchRow()['tabid'];
$result = $db->pquery("DELETE FROM `vtiger_field` WHERE fieldname = 'sales_person' AND tabid = ?", [$orderTabId]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";