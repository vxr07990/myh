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

$block45 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $moduleVendors);
$block43 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleVendors);
$block42 = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleVendors);
$field295 = Vtiger_Field::getInstance('createdtime', $moduleVendors);
$field734 = Vtiger_Field::getInstance('created_user_id', $moduleVendors);
$field296 = Vtiger_Field::getInstance('modifiedtime', $moduleVendors);
$field708 = Vtiger_Field::getInstance('assigned_user_id', $moduleVendors);
$field2866 = Vtiger_Field::getInstance('vendors_primcontact', $moduleVendors);
$field1674 = Vtiger_Field::getInstance('phone2', $moduleVendors);
$field291 = Vtiger_Field::getInstance('email', $moduleVendors);
$field290 = Vtiger_Field::getInstance('phone', $moduleVendors);
$field1675 = Vtiger_Field::getInstance('email2', $moduleVendors);
$field292 = Vtiger_Field::getInstance('website', $moduleVendors);
$field288 = Vtiger_Field::getInstance('vendorname', $moduleVendors);
$field2586 = Vtiger_Field::getInstance('type', $moduleVendors);
$field1934 = Vtiger_Field::getInstance('agentid', $moduleVendors);
$field289 = Vtiger_Field::getInstance('vendor_no', $moduleVendors);
$field2865 = Vtiger_Field::getInstance('vendors_vendornum', $moduleVendors);
$field1667 = Vtiger_Field::getInstance('vendor_status', $moduleVendors);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field295->id." THEN 1 WHEN fieldid=".$field734->id." THEN 3 WHEN fieldid=".$field296->id." THEN 2 WHEN fieldid=".$field708->id." THEN 4 WHEN fieldid=".$field2866->id." THEN 1 WHEN fieldid=".$field1674->id." THEN 3 WHEN fieldid=".$field291->id." THEN 5 WHEN fieldid=".$field290->id." THEN 2 WHEN fieldid=".$field1675->id." THEN 4 WHEN fieldid=".$field292->id." THEN 6 WHEN fieldid=".$field288->id." THEN 1 WHEN fieldid=".$field2586->id." THEN 3 WHEN fieldid=".$field1934->id." THEN 5 WHEN fieldid=".$field289->id." THEN 2 WHEN fieldid=".$field2865->id." THEN 4 WHEN fieldid=".$field1667->id." THEN 6 END, block=CASE WHEN fieldid=".$field295->id." THEN ".$block45->id." WHEN fieldid=".$field734->id." THEN ".$block45->id." WHEN fieldid=".$field296->id." THEN ".$block45->id." WHEN fieldid=".$field708->id." THEN ".$block45->id." WHEN fieldid=".$field2866->id." THEN ".$block43->id." WHEN fieldid=".$field1674->id." THEN ".$block43->id." WHEN fieldid=".$field291->id." THEN ".$block43->id." WHEN fieldid=".$field290->id." THEN ".$block43->id." WHEN fieldid=".$field1675->id." THEN ".$block43->id." WHEN fieldid=".$field292->id." THEN ".$block43->id." WHEN fieldid=".$field288->id." THEN ".$block42->id." WHEN fieldid=".$field2586->id." THEN ".$block42->id." WHEN fieldid=".$field1934->id." THEN ".$block42->id." WHEN fieldid=".$field289->id." THEN ".$block42->id." WHEN fieldid=".$field2865->id." THEN ".$block42->id." WHEN fieldid=".$field1667->id." THEN ".$block42->id." END WHERE fieldid IN (".$field295->id.",".$field734->id.",".$field296->id.",".$field708->id.",".$field2866->id.",".$field1674->id.",".$field291->id.",".$field290->id.",".$field1675->id.",".$field292->id.",".$field288->id.",".$field2586->id.",".$field1934->id.",".$field289->id.",".$field2865->id.",".$field1667->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";