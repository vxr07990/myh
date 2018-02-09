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

$db = PearDatabase::getInstance();
$db->pquery("UPDATE vtiger_field SET sequence = 3, typeofdata = 'V~M' WHERE columnname = 'order_task_field' AND tablename = 'vtiger_capacitycalendarcounter'", array());

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";