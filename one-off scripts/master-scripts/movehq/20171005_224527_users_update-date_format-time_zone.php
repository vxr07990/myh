<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

// OT5550 - User Module Default Date Format and Time Zone
$module = 'Users';
$tabid = getTabid($module);

$fieldName = 'date_format';
$sql = "UPDATE vtiger_field SET defaultvalue = 'mm-dd-yyyy' WHERE tabid = $tabid AND fieldname = '$fieldName'";
$result = $adb->pquery($sql);

$fieldName = 'time_zone';
$sql = "UPDATE vtiger_field SET defaultvalue = 'America/Chicago' WHERE tabid = $tabid AND fieldname = '$fieldName'";
$result = $adb->pquery($sql);