<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT3997 - Picklist Customizer Updates
// OT4806 - Create table to save CustomPicklist picklist values for diferent users

echo 'Start: Create table to save CustomPicklist picklist values for diferent users<br />\n';

//Create table to save picklist values for diferent users
$db = PearDatabase::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS `vtiger_custompicklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` VARCHAR(75) NOT NULL,
  `valueid` INT(11) NOT NULL,
  `agentid` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
$db->pquery($sql);

echo 'End: Create table to save CustomPicklist picklist values for diferent users<br />\n';