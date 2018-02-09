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

echo "<br> begin account related list rename orders";

$db = PearDatabase::getInstance();

$accountTabId = $db->pquery("SELECT tabid FROM `vtiger_tab` WHERE name = 'Accounts'", [])->fetchRow()['tabid'];
$ordersTabId = $db->pquery("SELECT tabid FROM `vtiger_tab` WHERE name = 'Orders'", [])->fetchRow()['tabid'];
$relationExists = $db->pquery("SELECT * FROM `vtiger_relatedlists` WHERE tabid = ? AND related_tabid = ?", [$accountTabId, $ordersTabId])->fetchRow();

if ($relationExists) {
    $db->pquery("UPDATE `vtiger_relatedlists` SET label = 'Orders' WHERE tabid = ? AND related_tabid = ?", [$accountTabId, $ordersTabId]);
} else {
    echo "<br> Orders doesn't exist in the accounts related list. <br> No action taken.";
}

echo "<br> end account related list rename orders";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";