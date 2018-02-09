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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>start set summary fields for vehicles<br>";

$db = PearDatabase::getInstance();

$sql = "UPDATE `vtiger_field` SET summaryfield = 1 WHERE fieldname IN ('vehicle_number', 'vehicle_type', 'vehicle_status') AND tabid = 67";

$db->pquery($sql, []);

echo "<br>end set summary fields for vehicles";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";