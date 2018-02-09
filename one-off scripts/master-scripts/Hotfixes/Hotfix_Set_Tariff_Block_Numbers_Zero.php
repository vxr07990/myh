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

$db = PearDatabase::getInstance();

$sql = "UPDATE `vtiger_field` SET `block` = 0 WHERE `fieldname` in ('cuft_rate', 'cartage_cwt_rate', 'first_day_rate', 'additional_day_rate')";
$result  = $db->pquery($sql);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";