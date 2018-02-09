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
include_once 'includes/main/WebUI.php';

echo "<br>modifying column type for accesorial_fuel_surcharge<br>";
$db = PearDatabase::getInstance();

$result = $db->pquery('SELECT * FROM `vtiger_modentity_num` WHERE `semodule` = "Orders"', []);

if ($db->num_rows($result) == 0) {
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $db->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Orders', 'HQ', 1, 1, 1));
}

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_entityname` SET `fieldname` = 'orders_no' WHERE `modulename` = 'Orders'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";