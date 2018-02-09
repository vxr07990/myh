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



require_once 'vtlib/Vtiger/Module.php';

// Get tariff ID for MAX3
$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT tariffsid FROM `vtiger_tariffs`
                       JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_tariffs.tariffsid
                       WHERE tariff_name = 'MAX3'
                       AND vtiger_crmentity.deleted = 0",[]);
$maxId = $result->fetchRow()[0];
// Sets type for Elevator
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariffservices SET rate_type = 'CWT Per Quantity' WHERE related_tariff = $maxId AND service_name = 'Elevator'");
// Sets type for Stairs
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariffservices SET rate_type = 'CWT Per Quantity' WHERE related_tariff = $maxId AND service_name = 'Stairs'");
// Sets type for Long Carry
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tariffservices SET rate_type = 'CWT Per Quantity' WHERE related_tariff = $maxId AND service_name = 'Long Carry'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";