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


include_once('vtlib/Vtiger/Module.php');

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_custom_tariff_type` (custom_tariff_typeid, custom_tariff_type, sortorderid, presence) SELECT id + 2, 'Autos Only', id + 2, 1 FROM `vtiger_custom_tariff_type_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_custom_tariff_type` WHERE custom_tariff_type = 'Autos Only')");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tariffmanager` SET `custom_javascript` = 'Estimates_TPGTariff_Js' WHERE `vtiger_tariffmanager`.`tariffmanagername` = 'Autos Only'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";