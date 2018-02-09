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

// OT5300 - Adding "Default Resource Width" To local dispatch filters

echo 'Start: Create table to save Default Resource Width values for diferent filters<br />\n';

//Create table to save dependency picklist values for diferent users
$db = PearDatabase::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS `vtiger_localdispatch_resourcewidth` (
  `cvid` int(11) NOT NULL,
  `percent` int(11) NOT NULL,
  PRIMARY KEY (`cvid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$db->pquery($sql);

echo 'End: Create table to save Default Resource Width values for diferent filters<br />\n';