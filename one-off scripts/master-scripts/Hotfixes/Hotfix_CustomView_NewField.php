<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

echo 'Updating vtiger_customview table with new "view" field <br>\n';

$db = PearDatabase::getInstance();
$db->pquery("ALTER TABLE `vtiger_customview` ADD `view` VARCHAR(100);",array());

