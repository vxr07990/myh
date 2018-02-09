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

echo "<h1>Start: Clean up Out of service Picklist Old Values</h1><br>";

$sqlquery = "DELETE FROM vtiger_outofservice_status WHERE outofservice_status IN ('Notice','Out')";
Vtiger_Utils::ExecuteQuery($sqlquery);

echo "Values: 'Notice' and 'Out' removed<br>";

$sqlquery2 = "UPDATE vtiger_outofservice_status SET sortorderid = CASE"
        . " WHEN outofservice_status = 'On Notice' THEN 1"
        . " WHEN outofservice_status = 'Out of Service' THEN 2"
        . " ELSE sortorderid"
        . " END";
Vtiger_Utils::ExecuteQuery($sqlquery2);

echo "Reordered: 1.- On Notice, 2.- Out of Service<br>";

echo "<h1>Finish: Clean up Out of service Picklist Old Values</h1><br>";