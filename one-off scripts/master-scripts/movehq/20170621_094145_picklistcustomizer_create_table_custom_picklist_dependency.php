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
// OT4808 - Add a new tab within picklist customizer to show the picklist dependency feature

echo 'Start: Create table to save Custom Picklist Dependency values for diferent users<br />\n';

//Create table to save dependency picklist values for diferent users
$db = PearDatabase::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS `vtiger_custom_picklist_dependency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `picklistdependencyid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `picklistdependencyid` (`picklistdependencyid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
$db->pquery($sql);

echo 'End: Create table to save Custom Picklist Dependency values for diferent users<br />\n';