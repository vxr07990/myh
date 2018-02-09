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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$result = $db->getOne('SELECT `leadsource` FROM `vtiger_leadsource` WHERE `leadsource` LIKE "Sirva Military"');
if (!$result) {
    $leadSourceId = $db->getOne('SELECT `id` FROM `vtiger_leadsource_seq') + 1;
    $picklistValueId = $db->getOne('SELECT `id` FROM `vtiger_picklistvalues_seq') + 1;

    Vtiger_Utils::ExecuteQuery("INSERT INTO`vtiger_leadsource` (leadsourceid,leadsource,presence,picklist_valueid,sortorderid) VALUES ('$leadSourceId','Sirva Military', 1 ,'$picklistValueId',7)");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_leadsource_seq` SET id = '$leadSourceId'");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_picklistvalues_seq` SET id = '$picklistValueId'");
}

$result = $db->getOne('SELECT `opportunity_type` FROM `vtiger_opportunity_type` WHERE `opportunity_type` LIKE "Military Award Survey"');
if (!$result) {
    $oppTypeId = $db->getOne('SELECT `id` FROM `vtiger_opportunity_type_seq') + 1;
    $picklistValueId = $db->getOne('SELECT `id` FROM `vtiger_picklistvalues_seq') + 1;

    Vtiger_Utils::ExecuteQuery("INSERT INTO`vtiger_opportunity_type` (opptypeid,opportunity_type,presence,picklist_valueid,sortorderid) VALUES ('$oppTypeId','Military Award Survey', 1 ,'$picklistValueId',3)");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_opportunity_type_seq` SET id = '$oppTypeId'");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_picklistvalues_seq` SET id = '$picklistValueId'");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";