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

$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');

$block308 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_REGISTERSTS', $moduleOpportunities);
$field2075 = Vtiger_Field::getInstance('payment_type_sts', $moduleOpportunities);
$field2079 = Vtiger_Field::getInstance('brand', $moduleOpportunities);
$field2083 = Vtiger_Field::getInstance('cbs_ind', $moduleOpportunities);
$field2084 = Vtiger_Field::getInstance('credit_check', $moduleOpportunities);
$field2481 = Vtiger_Field::getInstance('cbs_contact', $moduleOpportunities);
$field2086 = Vtiger_Field::getInstance('ref_number', $moduleOpportunities);
$field2088 = Vtiger_Field::getInstance('credit_check_amount', $moduleOpportunities);
$field2110 = Vtiger_Field::getInstance('sts_response', $moduleOpportunities);
$field2077 = Vtiger_Field::getInstance('agmt_id', $moduleOpportunities);
$field2278 = Vtiger_Field::getInstance('agrmt_cod', $moduleOpportunities);
$field2076 = Vtiger_Field::getInstance('payment_method', $moduleOpportunities);
$field2080 = Vtiger_Field::getInstance('national_account_number', $moduleOpportunities);
$field2085 = Vtiger_Field::getInstance('billing_apn', $moduleOpportunities);
$field2087 = Vtiger_Field::getInstance('ref_type', $moduleOpportunities);
$field2109 = Vtiger_Field::getInstance('registration_date', $moduleOpportunities);
$field2111 = Vtiger_Field::getInstance('booker_split', $moduleOpportunities);
$field2082 = Vtiger_Field::getInstance('express_shipment', $moduleOpportunities);
$field2078 = Vtiger_Field::getInstance('subagmt_nbr', $moduleOpportunities);
$field2279 = Vtiger_Field::getInstance('subagrmt_cod', $moduleOpportunities);
$field2112 = Vtiger_Field::getInstance('origin_split', $moduleOpportunities);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field2075->id." THEN 1 WHEN fieldid=".$field2079->id." THEN 3 WHEN fieldid=".$field2083->id." THEN 5 WHEN fieldid=".$field2084->id." THEN 7 WHEN fieldid=".$field2481->id." THEN 9 WHEN fieldid=".$field2086->id." THEN 11 WHEN fieldid=".$field2088->id." THEN 13 WHEN fieldid=".$field2110->id." THEN 15 WHEN fieldid=".$field2077->id." THEN 17 WHEN fieldid=".$field2278->id." THEN 19 WHEN fieldid=".$field2076->id." THEN 2 WHEN fieldid=".$field2080->id." THEN 4 WHEN fieldid=".$field2085->id." THEN 6 WHEN fieldid=".$field2087->id." THEN 8 WHEN fieldid=".$field2109->id." THEN 10 WHEN fieldid=".$field2111->id." THEN 12 WHEN fieldid=".$field2082->id." THEN 14 WHEN fieldid=".$field2078->id." THEN 16 WHEN fieldid=".$field2279->id." THEN 18 WHEN fieldid=".$field2112->id." THEN 20 END, block=CASE WHEN fieldid=".$field2075->id." THEN ".$block308->id." WHEN fieldid=".$field2079->id." THEN ".$block308->id." WHEN fieldid=".$field2083->id." THEN ".$block308->id." WHEN fieldid=".$field2084->id." THEN ".$block308->id." WHEN fieldid=".$field2481->id." THEN ".$block308->id." WHEN fieldid=".$field2086->id." THEN ".$block308->id." WHEN fieldid=".$field2088->id." THEN ".$block308->id." WHEN fieldid=".$field2110->id." THEN ".$block308->id." WHEN fieldid=".$field2077->id." THEN ".$block308->id." WHEN fieldid=".$field2278->id." THEN ".$block308->id." WHEN fieldid=".$field2076->id." THEN ".$block308->id." WHEN fieldid=".$field2080->id." THEN ".$block308->id." WHEN fieldid=".$field2085->id." THEN ".$block308->id." WHEN fieldid=".$field2087->id." THEN ".$block308->id." WHEN fieldid=".$field2109->id." THEN ".$block308->id." WHEN fieldid=".$field2111->id." THEN ".$block308->id." WHEN fieldid=".$field2082->id." THEN ".$block308->id." WHEN fieldid=".$field2078->id." THEN ".$block308->id." WHEN fieldid=".$field2279->id." THEN ".$block308->id." WHEN fieldid=".$field2112->id." THEN ".$block308->id." END WHERE fieldid IN (".$field2075->id.",".$field2079->id.",".$field2083->id.",".$field2084->id.",".$field2481->id.",".$field2086->id.",".$field2088->id.",".$field2110->id.",".$field2077->id.",".$field2278->id.",".$field2076->id.",".$field2080->id.",".$field2085->id.",".$field2087->id.",".$field2109->id.",".$field2111->id.",".$field2082->id.",".$field2078->id.",".$field2279->id.",".$field2112->id.")");



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";