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

$moduleInstance = Vtiger_Module::getInstance('VehicleMaintenance');

if(!$moduleInstance){
    return;
}


$sql = 'SELECT * FROM vtiger_ws_entry WHERE name = ?';
$result = $db->pQuery($sql, ['VehicleMaintenance']);
if($db->num_rows($result) == 0){
    $moduleInstance->initWebservice();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";