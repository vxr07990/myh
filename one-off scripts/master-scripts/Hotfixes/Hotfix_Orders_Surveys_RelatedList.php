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



//Hotfix_Orders_Surveys_RelatedList
$db = PearDatabase::getInstance();

$ordersInstance = Vtiger_Module::getInstance('Orders');
$surveysInstance = Vtiger_Module::getInstance('Surveys');

$db->pquery("UPDATE vtiger_relatedlists SET actions = 'ADD' WHERE tabid = ? and related_tabid = ?", array($ordersInstance->getId(), $surveysInstance->getId()));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";