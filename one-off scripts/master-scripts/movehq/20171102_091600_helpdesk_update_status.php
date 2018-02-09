<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$helpDeskModule = Vtiger_Module::getInstance('HelpDesk');
$ticketStatusField = Vtiger_Field::getInstance('ticketstatus', $helpDeskModule);

if($ticketStatusField) {
    if(!$db) {
        $db = PearDatabase::getInstance();
    }
    $db->query("TRUNCATE TABLE `vtiger_ticketstatus`");
    $ticketStatusField->setPicklistValues(['Open', 'In Progress', 'Wait For Response', 'Closed']);
}
