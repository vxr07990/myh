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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//Insert NAT payment type if it doesn't exist
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_payment_type_sts` (payment_type_stsid, payment_type_sts, sortorderid, presence) SELECT id + 1, 'NAT', id + 1, 1 FROM `vtiger_payment_type_sts_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_payment_type_sts` WHERE payment_type_sts = 'NAT')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";