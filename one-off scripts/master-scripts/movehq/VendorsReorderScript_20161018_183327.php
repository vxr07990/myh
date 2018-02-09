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

//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleVendors = Vtiger_Module::getInstance('Vendors');

$block42 = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleVendors);
$field288 = Vtiger_Field::getInstance('vendorname', $moduleVendors);
$field1667 = Vtiger_Field::getInstance('vendor_status', $moduleVendors);
$field2865 = Vtiger_Field::getInstance('vendors_vendornum', $moduleVendors);
$field1934 = Vtiger_Field::getInstance('agentid', $moduleVendors);
$field2586 = Vtiger_Field::getInstance('type', $moduleVendors);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field288->id." THEN 1 WHEN fieldid=".$field1667->id." THEN 3 WHEN fieldid=".$field2865->id." THEN 5 WHEN fieldid=".$field1934->id." THEN 2 WHEN fieldid=".$field2586->id." THEN 4 END, block=CASE WHEN fieldid=".$field288->id." THEN ".$block42->id." WHEN fieldid=".$field1667->id." THEN ".$block42->id." WHEN fieldid=".$field2865->id." THEN ".$block42->id." WHEN fieldid=".$field1934->id." THEN ".$block42->id." WHEN fieldid=".$field2586->id." THEN ".$block42->id." END WHERE fieldid IN (".$field288->id.",".$field1667->id.",".$field2865->id.",".$field1934->id.",".$field2586->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";