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

//Updating Orders - Trips related list

$db = PearDatabase::getInstance();
$ordersModule = Vtiger_Module::getInstance('Orders');
$tripsModule = Vtiger_Module::getInstance('Trips');
$db->pquery("UPDATE vtiger_relatedlists SET name='get_trips', actions='ADD, SELECT' WHERE tabid=? AND related_tabid=?", array($ordersModule->id, $tripsModule->id));

//Update the orders with the missing Trip id in the orders_trip field but with an active relationship to a trip

$db->pquery("UPDATE vtiger_orders, vtiger_crmentityrel SET orders_trip=relcrmid 
		WHERE vtiger_orders.ordersid = vtiger_crmentityrel.crmid 
		AND vtiger_crmentityrel.relmodule = 'Trips'
		AND orders_trip = ''");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";