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

// OT19671	BUG: Local Dispatch - Vehicles table - Update default filter label

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('OrdersTask');
if(!$module) {
    return;
}

$filter = Vtiger_Filter::getInstance('Equipment Default Filter', $module);
if(!$filter) {
    return;
}

$sql = "UPDATE `vtiger_customview` SET `viewname` = ? WHERE `cvid` = ?";
$db->pquery($sql, ['Vehicles Default Filter', $filter->id]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";