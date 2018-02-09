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

// OT5553 Local Dispatch - Add Filter Functionality to default Resource window collapsed or expanded

$db = PearDatabase::getInstance();
$sql = "ALTER TABLE `vtiger_localdispatch_resourcewidth` ADD COLUMN `collapsed` VARCHAR(3) NOT NULL DEFAULT '0' AFTER `percent`;";
$result = $db->pquery($sql);