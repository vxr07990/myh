<?php

if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//Updating Related Orders list for Orders

$db = PearDatabase::getInstance();
$ordersModule = Vtiger_Module::getInstance('Orders');
$db->pquery("UPDATE vtiger_relatedlists SET name='get_dependents_list' WHERE tabid=? AND related_tabid=?", array($ordersModule->id, $ordersModule->id));
