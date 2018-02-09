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
$sql = 'ALTER TABLE `vtiger_quotes` MODIFY accesorial_fuel_surcharge DECIMAL(5,4)';
$result = $db->pquery($sql, array());
echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";