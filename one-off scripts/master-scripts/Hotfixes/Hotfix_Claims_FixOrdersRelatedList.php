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



//Fix Orders Related List
$ordersInstance = Vtiger_Module::getInstance('Orders');
$claimsInstance = Vtiger_Module::getInstance('Claims');
$claimsSummaryInstance = Vtiger_Module::getInstance('ClaimsSummary');

$db = PearDatabase::getInstance();
$db->pquery("UPDATE vtiger_relatedlists SET name='get_dependents_list', related_tabid=$claimsSummaryInstance->id WHERE tabid=$ordersInstance->id AND related_tabid=$claimsInstance->id");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";