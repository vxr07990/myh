<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



$db = PearDatabase::getInstance();

$sql = "SELECT * FROM `vtiger_modtracker_basic_seq`";
$result = $db->pquery($sql, []);
if ($result == null) {
    $sql = "INSERT INTO `vtiger_modtracker_basic_seq` VALUES (1)";
    $result = $db->pquery($sql, []);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";