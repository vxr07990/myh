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

$block201 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleOpportunities);
$field1161 = Vtiger_Field::getInstance('potentialname', $moduleOpportunities);
$field1163 = Vtiger_Field::getInstance('contact_id', $moduleOpportunities);
$field1167 = Vtiger_Field::getInstance('sales_stage', $moduleOpportunities);
$field2194 = Vtiger_Field::getInstance('disposition_lost_reasons', $moduleOpportunities);
$field1166 = Vtiger_Field::getInstance('business_line', $moduleOpportunities);
$field1165 = Vtiger_Field::getInstance('opportunity_type', $moduleOpportunities);
$field1170 = Vtiger_Field::getInstance('assigned_user_id', $moduleOpportunities);
$field1218 = Vtiger_Field::getInstance('sales_person', $moduleOpportunities);
$field1174 = Vtiger_Field::getInstance('created_user_id', $moduleOpportunities);
$field1176 = Vtiger_Field::getInstance('modifiedtime', $moduleOpportunities);
$field2047 = Vtiger_Field::getInstance('funded', $moduleOpportunities);
$field2056 = Vtiger_Field::getInstance('program_terms', $moduleOpportunities);
$field1915 = Vtiger_Field::getInstance('agentid', $moduleOpportunities);
$field2049 = Vtiger_Field::getInstance('moving_a_vehicle', $moduleOpportunities);
$field2081 = Vtiger_Field::getInstance('self_haul', $moduleOpportunities);
$field2530 = Vtiger_Field::getInstance('cf_record_id', $moduleOpportunities);
$field1168 = Vtiger_Field::getInstance('leadsource', $moduleOpportunities);
$field2048 = Vtiger_Field::getInstance('receive_date', $moduleOpportunities);
$field2054 = Vtiger_Field::getInstance('small_move', $moduleOpportunities);
$field2052 = Vtiger_Field::getInstance('out_of_area', $moduleOpportunities);
$field2121 = Vtiger_Field::getInstance('preferred_language', $moduleOpportunities);
$field2541 = Vtiger_Field::getInstance('source_name', $moduleOpportunities);
$field2543 = Vtiger_Field::getInstance('non_conforming', $moduleOpportunities);
$field2556 = Vtiger_Field::getInstance('appointment_type', $moduleOpportunities);
$field2630 = Vtiger_Field::getInstance('segment', $moduleOpportunities);
$field2634 = Vtiger_Field::getInstance('segment_desc', $moduleOpportunities);
$field2044 = Vtiger_Field::getInstance('move_type', $moduleOpportunities);
$field1162 = Vtiger_Field::getInstance('potential_no', $moduleOpportunities);
$field1169 = Vtiger_Field::getInstance('amount', $moduleOpportunities);
$field2193 = Vtiger_Field::getInstance('disposition_lost_reasons_other', $moduleOpportunities);
$field2329 = Vtiger_Field::getInstance('register_sts_number', $moduleOpportunities);
$field2045 = Vtiger_Field::getInstance('business_channel', $moduleOpportunities);
$field1945 = Vtiger_Field::getInstance('shipper_type', $moduleOpportunities);
$field2310 = Vtiger_Field::getInstance('sent_to_mobile', $moduleOpportunities);
$field1164 = Vtiger_Field::getInstance('related_to', $moduleOpportunities);
$field1175 = Vtiger_Field::getInstance('createdtime', $moduleOpportunities);
$field2046 = Vtiger_Field::getInstance('assigned_date', $moduleOpportunities);
$field2050 = Vtiger_Field::getInstance('promotion_code', $moduleOpportunities);
$field1813 = Vtiger_Field::getInstance('billing_type', $moduleOpportunities);
$field2176 = Vtiger_Field::getInstance('lock_military_fields', $moduleOpportunities);
$field2074 = Vtiger_Field::getInstance('special_terms', $moduleOpportunities);
$field2073 = Vtiger_Field::getInstance('employer_comments', $moduleOpportunities);
$field2051 = Vtiger_Field::getInstance('opp_type', $moduleOpportunities);
$field2055 = Vtiger_Field::getInstance('phone_estimate', $moduleOpportunities);
$field2053 = Vtiger_Field::getInstance('out_of_origin', $moduleOpportunities);
$field1177 = Vtiger_Field::getInstance('isconvertedfromlead', $moduleOpportunities);
$field2540 = Vtiger_Field::getInstance('lmp_lead_id', $moduleOpportunities);
$field2542 = Vtiger_Field::getInstance('program_name', $moduleOpportunities);
$field2544 = Vtiger_Field::getInstance('non_conforming_params', $moduleOpportunities);
$field2586 = Vtiger_Field::getInstance('warm_transfer', $moduleOpportunities);
$field2632 = Vtiger_Field::getInstance('segment_used', $moduleOpportunities);
$field2696 = Vtiger_Field::getInstance('lead_type', $moduleOpportunities);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1161->id." THEN 1 WHEN fieldid=".$field1163->id." THEN 3 WHEN fieldid=".$field1167->id." THEN 5 WHEN fieldid=".$field2194->id." THEN 7 WHEN fieldid=".$field1166->id." THEN 9 WHEN fieldid=".$field1165->id." THEN 11 WHEN fieldid=".$field1170->id." THEN 13 WHEN fieldid=".$field1218->id." THEN 15 WHEN fieldid=".$field1174->id." THEN 17 WHEN fieldid=".$field1176->id." THEN 19 WHEN fieldid=".$field2047->id." THEN 21 WHEN fieldid=".$field2056->id." THEN 23 WHEN fieldid=".$field1915->id." THEN 25 WHEN fieldid=".$field2049->id." THEN 27 WHEN fieldid=".$field2081->id." THEN 29 WHEN fieldid=".$field2530->id." THEN 31 WHEN fieldid=".$field1168->id." THEN 33 WHEN fieldid=".$field2048->id." THEN 35 WHEN fieldid=".$field2054->id." THEN 37 WHEN fieldid=".$field2052->id." THEN 39 WHEN fieldid=".$field2121->id." THEN 41 WHEN fieldid=".$field2541->id." THEN 43 WHEN fieldid=".$field2543->id." THEN 45 WHEN fieldid=".$field2556->id." THEN 47 WHEN fieldid=".$field2630->id." THEN 49 WHEN fieldid=".$field2634->id." THEN 51 WHEN fieldid=".$field2044->id." THEN 2 WHEN fieldid=".$field1162->id." THEN 4 WHEN fieldid=".$field1169->id." THEN 6 WHEN fieldid=".$field2193->id." THEN 8 WHEN fieldid=".$field2329->id." THEN 10 WHEN fieldid=".$field2045->id." THEN 12 WHEN fieldid=".$field1945->id." THEN 14 WHEN fieldid=".$field2310->id." THEN 16 WHEN fieldid=".$field1164->id." THEN 18 WHEN fieldid=".$field1175->id." THEN 20 WHEN fieldid=".$field2046->id." THEN 22 WHEN fieldid=".$field2050->id." THEN 24 WHEN fieldid=".$field1813->id." THEN 26 WHEN fieldid=".$field2176->id." THEN 28 WHEN fieldid=".$field2074->id." THEN 30 WHEN fieldid=".$field2073->id." THEN 32 WHEN fieldid=".$field2051->id." THEN 34 WHEN fieldid=".$field2055->id." THEN 36 WHEN fieldid=".$field2053->id." THEN 38 WHEN fieldid=".$field1177->id." THEN 40 WHEN fieldid=".$field2540->id." THEN 42 WHEN fieldid=".$field2542->id." THEN 44 WHEN fieldid=".$field2544->id." THEN 46 WHEN fieldid=".$field2586->id." THEN 48 WHEN fieldid=".$field2632->id." THEN 50 WHEN fieldid=".$field2696->id." THEN 52 END, block=CASE WHEN fieldid=".$field1161->id." THEN ".$block201->id." WHEN fieldid=".$field1163->id." THEN ".$block201->id." WHEN fieldid=".$field1167->id." THEN ".$block201->id." WHEN fieldid=".$field2194->id." THEN ".$block201->id." WHEN fieldid=".$field1166->id." THEN ".$block201->id." WHEN fieldid=".$field1165->id." THEN ".$block201->id." WHEN fieldid=".$field1170->id." THEN ".$block201->id." WHEN fieldid=".$field1218->id." THEN ".$block201->id." WHEN fieldid=".$field1174->id." THEN ".$block201->id." WHEN fieldid=".$field1176->id." THEN ".$block201->id." WHEN fieldid=".$field2047->id." THEN ".$block201->id." WHEN fieldid=".$field2056->id." THEN ".$block201->id." WHEN fieldid=".$field1915->id." THEN ".$block201->id." WHEN fieldid=".$field2049->id." THEN ".$block201->id." WHEN fieldid=".$field2081->id." THEN ".$block201->id." WHEN fieldid=".$field2530->id." THEN ".$block201->id." WHEN fieldid=".$field1168->id." THEN ".$block201->id." WHEN fieldid=".$field2048->id." THEN ".$block201->id." WHEN fieldid=".$field2054->id." THEN ".$block201->id." WHEN fieldid=".$field2052->id." THEN ".$block201->id." WHEN fieldid=".$field2121->id." THEN ".$block201->id." WHEN fieldid=".$field2541->id." THEN ".$block201->id." WHEN fieldid=".$field2543->id." THEN ".$block201->id." WHEN fieldid=".$field2556->id." THEN ".$block201->id." WHEN fieldid=".$field2630->id." THEN ".$block201->id." WHEN fieldid=".$field2634->id." THEN ".$block201->id." WHEN fieldid=".$field2044->id." THEN ".$block201->id." WHEN fieldid=".$field1162->id." THEN ".$block201->id." WHEN fieldid=".$field1169->id." THEN ".$block201->id." WHEN fieldid=".$field2193->id." THEN ".$block201->id." WHEN fieldid=".$field2329->id." THEN ".$block201->id." WHEN fieldid=".$field2045->id." THEN ".$block201->id." WHEN fieldid=".$field1945->id." THEN ".$block201->id." WHEN fieldid=".$field2310->id." THEN ".$block201->id." WHEN fieldid=".$field1164->id." THEN ".$block201->id." WHEN fieldid=".$field1175->id." THEN ".$block201->id." WHEN fieldid=".$field2046->id." THEN ".$block201->id." WHEN fieldid=".$field2050->id." THEN ".$block201->id." WHEN fieldid=".$field1813->id." THEN ".$block201->id." WHEN fieldid=".$field2176->id." THEN ".$block201->id." WHEN fieldid=".$field2074->id." THEN ".$block201->id." WHEN fieldid=".$field2073->id." THEN ".$block201->id." WHEN fieldid=".$field2051->id." THEN ".$block201->id." WHEN fieldid=".$field2055->id." THEN ".$block201->id." WHEN fieldid=".$field2053->id." THEN ".$block201->id." WHEN fieldid=".$field1177->id." THEN ".$block201->id." WHEN fieldid=".$field2540->id." THEN ".$block201->id." WHEN fieldid=".$field2542->id." THEN ".$block201->id." WHEN fieldid=".$field2544->id." THEN ".$block201->id." WHEN fieldid=".$field2586->id." THEN ".$block201->id." WHEN fieldid=".$field2632->id." THEN ".$block201->id." WHEN fieldid=".$field2696->id." THEN ".$block201->id." END WHERE fieldid IN (".$field1161->id.",".$field1163->id.",".$field1167->id.",".$field2194->id.",".$field1166->id.",".$field1165->id.",".$field1170->id.",".$field1218->id.",".$field1174->id.",".$field1176->id.",".$field2047->id.",".$field2056->id.",".$field1915->id.",".$field2049->id.",".$field2081->id.",".$field2530->id.",".$field1168->id.",".$field2048->id.",".$field2054->id.",".$field2052->id.",".$field2121->id.",".$field2541->id.",".$field2543->id.",".$field2556->id.",".$field2630->id.",".$field2634->id.",".$field2044->id.",".$field1162->id.",".$field1169->id.",".$field2193->id.",".$field2329->id.",".$field2045->id.",".$field1945->id.",".$field2310->id.",".$field1164->id.",".$field1175->id.",".$field2046->id.",".$field2050->id.",".$field1813->id.",".$field2176->id.",".$field2074->id.",".$field2073->id.",".$field2051->id.",".$field2055->id.",".$field2053->id.",".$field1177->id.",".$field2540->id.",".$field2542->id.",".$field2544->id.",".$field2586->id.",".$field2632->id.",".$field2696->id.")");
    

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";