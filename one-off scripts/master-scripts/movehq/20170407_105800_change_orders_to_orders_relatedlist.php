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

//OT18379 - Orders to Orders Related List

$db = PearDatabase::getInstance();
$ordersModule = Vtiger_Module::getInstance('Orders');

$orderTabID = $ordersModule->getId();

$db->pquery("UPDATE vtiger_relatedlists SET actions = '' WHERE tabid = ? AND related_tabid = ?", array($orderTabID,$orderTabID));

print "Orders to Orders Related List Updated!";