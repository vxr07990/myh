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

$extrastop_seg = 0;

$location_type = array('Extra Pickup 1','Extra Pickup 2','Extra Pickup 3','Extra Pickup 4','Extra Pickup 5',
                       'Extra Delivery 1','Extra Delivery 2','Extra Delivery 3','Extra Delivery 4','Extra Delivery 5',
                       'O - SIT','D - SIT','Self Stg PU','Perm Dlv','Perm PU','Self Stg Dlv');

Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_extrastops_type`');
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_extrastops_type_seq` SET id = 0');

foreach ($location_type as $value) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_extrastops_type_seq` SET id = '.$extrastop_seg.'");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_extrastops_type` (extrastops_typeid, extrastops_type, sortorderid, presence) SELECT id + 1, '$value', id + 1, 1 FROM `vtiger_extrastops_type_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_extrastops_type` WHERE extrastops_type = '$value')");

    $extrastop_seg++;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";