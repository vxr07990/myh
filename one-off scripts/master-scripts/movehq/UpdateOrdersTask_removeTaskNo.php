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
global $adb;

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if ($moduleInstance) {
    //remove Actual Date Field
    if ($field3 = Vtiger_Field::getInstance('orderstask_no',$moduleInstance)) {
        if ($field3->presence != 1) {
            $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?", ['1', $field3->id]);
        }
    }
}
print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";