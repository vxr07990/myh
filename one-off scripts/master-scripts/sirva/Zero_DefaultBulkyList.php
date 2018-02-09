<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = &PearDatabase::getInstance();
$sql = "UPDATE vtiger_local_bulky_defaults SET weight=0, rate=0";
$db->query($sql);

print "\e[36mFINISHED: " . __FILE__ . "<br />\n\e[0m";
