<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT3997 - Picklist Customizer Updates
// OT4806 - New uitype type for a picklist that will allow set up the values by agent

echo 'Start: add new uitype => 1500, fieldtype => custompicklist<br />\n';

$db = PearDatabase::getInstance();
$sql = 'INSERT INTO vtiger_ws_fieldtype (uitype,fieldtype) VALUES ("1500","custompicklist")';
$result = $db->pquery($sql);

echo 'Finish: add new uitype 1500 => custompicklist<br />\n';