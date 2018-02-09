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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "UPDATE `vtiger_survey_type` SET presence=0 WHERE survey_type='Virtual'";
if($db->query($sql)) {
    print "UPDATED\n";
}else {
    print "FAILED\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
