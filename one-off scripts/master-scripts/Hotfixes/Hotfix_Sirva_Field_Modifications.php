
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



require_once 'vtlib/Vtiger/Module.php';

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = 3 WHERE `fieldname` = 'closingdate' AND `tablename` = 'vtiger_potential'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `summaryfield` = 1 WHERE `fieldname` = 'register_sts_number' AND `tablename` = 'vtiger_potential'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = 3 WHERE `fieldname` = 'order_number' AND `tablename` = 'vtiger_potential'");

$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');

$block201 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleOpportunities);
$field1161 = Vtiger_Field::getInstance('potentialname', $moduleOpportunities);
$field1163 = Vtiger_Field::getInstance('contact_id', $moduleOpportunities);
$field1167 = Vtiger_Field::getInstance('sales_stage', $moduleOpportunities);
$field2329 = Vtiger_Field::getInstance('register_sts_number', $moduleOpportunities);
$field1166 = Vtiger_Field::getInstance('business_line', $moduleOpportunities);
$field1165 = Vtiger_Field::getInstance('opportunity_type', $moduleOpportunities);
$field1170 = Vtiger_Field::getInstance('assigned_user_id', $moduleOpportunities);
$field1218 = Vtiger_Field::getInstance('sales_person', $moduleOpportunities);
$field2042 = Vtiger_Field::getInstance('order_number', $moduleOpportunities);
$field1175 = Vtiger_Field::getInstance('createdtime', $moduleOpportunities);
$field2046 = Vtiger_Field::getInstance('assigned_date', $moduleOpportunities);
$field2050 = Vtiger_Field::getInstance('promotion_code', $moduleOpportunities);
$field1915 = Vtiger_Field::getInstance('agentid', $moduleOpportunities);
$field2081 = Vtiger_Field::getInstance('self_haul', $moduleOpportunities);
$field2049 = Vtiger_Field::getInstance('moving_a_vehicle', $moduleOpportunities);
$field2044 = Vtiger_Field::getInstance('move_type', $moduleOpportunities);
$field1162 = Vtiger_Field::getInstance('potential_no', $moduleOpportunities);
$field1169 = Vtiger_Field::getInstance('amount', $moduleOpportunities);
$field2045 = Vtiger_Field::getInstance('business_channel', $moduleOpportunities);
$field1945 = Vtiger_Field::getInstance('shipper_type', $moduleOpportunities);
$field2310 = Vtiger_Field::getInstance('sent_to_mobile', $moduleOpportunities);
$field1164 = Vtiger_Field::getInstance('related_to', $moduleOpportunities);
$field1174 = Vtiger_Field::getInstance('created_user_id', $moduleOpportunities);
$field1176 = Vtiger_Field::getInstance('modifiedtime', $moduleOpportunities);
$field2047 = Vtiger_Field::getInstance('funded', $moduleOpportunities);
$field2056 = Vtiger_Field::getInstance('program_terms', $moduleOpportunities);
$field1813 = Vtiger_Field::getInstance('billing_type', $moduleOpportunities);
$field2176 = Vtiger_Field::getInstance('lock_military_fields', $moduleOpportunities);
$field2074 = Vtiger_Field::getInstance('special_terms', $moduleOpportunities);
$field2073 = Vtiger_Field::getInstance('employer_comments', $moduleOpportunities);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1161->id." THEN 1 WHEN fieldid=".$field1163->id." THEN 3 WHEN fieldid=".$field1167->id." THEN 5 WHEN fieldid=".$field2329->id." THEN 7 WHEN fieldid=".$field1166->id." THEN 9 WHEN fieldid=".$field1165->id." THEN 11 WHEN fieldid=".$field1170->id." THEN 13 WHEN fieldid=".$field1218->id." THEN 15 WHEN fieldid=".$field2042->id." THEN 17 WHEN fieldid=".$field1175->id." THEN 19 WHEN fieldid=".$field2046->id." THEN 21 WHEN fieldid=".$field2050->id." THEN 23 WHEN fieldid=".$field1915->id." THEN 25 WHEN fieldid=".$field2081->id." THEN 27 WHEN fieldid=".$field2049->id." THEN 29 WHEN fieldid=".$field2044->id." THEN 2 WHEN fieldid=".$field1162->id." THEN 4 WHEN fieldid=".$field1169->id." THEN 6 WHEN fieldid=".$field2045->id." THEN 8 WHEN fieldid=".$field1945->id." THEN 10 WHEN fieldid=".$field2310->id." THEN 12 WHEN fieldid=".$field1164->id." THEN 14 WHEN fieldid=".$field1174->id." THEN 16 WHEN fieldid=".$field1176->id." THEN 18 WHEN fieldid=".$field2047->id." THEN 20 WHEN fieldid=".$field2056->id." THEN 22 WHEN fieldid=".$field1813->id." THEN 24 WHEN fieldid=".$field2176->id." THEN 26 WHEN fieldid=".$field2074->id." THEN 28 WHEN fieldid=".$field2073->id." THEN 30 END, block=CASE WHEN fieldid=".$field1161->id." THEN ".$block201->id." WHEN fieldid=".$field1163->id." THEN ".$block201->id." WHEN fieldid=".$field1167->id." THEN ".$block201->id." WHEN fieldid=".$field2329->id." THEN ".$block201->id." WHEN fieldid=".$field1166->id." THEN ".$block201->id." WHEN fieldid=".$field1165->id." THEN ".$block201->id." WHEN fieldid=".$field1170->id." THEN ".$block201->id." WHEN fieldid=".$field1218->id." THEN ".$block201->id." WHEN fieldid=".$field2042->id." THEN ".$block201->id." WHEN fieldid=".$field1175->id." THEN ".$block201->id." WHEN fieldid=".$field2046->id." THEN ".$block201->id." WHEN fieldid=".$field2050->id." THEN ".$block201->id." WHEN fieldid=".$field1915->id." THEN ".$block201->id." WHEN fieldid=".$field2081->id." THEN ".$block201->id." WHEN fieldid=".$field2049->id." THEN ".$block201->id." WHEN fieldid=".$field2044->id." THEN ".$block201->id." WHEN fieldid=".$field1162->id." THEN ".$block201->id." WHEN fieldid=".$field1169->id." THEN ".$block201->id." WHEN fieldid=".$field2045->id." THEN ".$block201->id." WHEN fieldid=".$field1945->id." THEN ".$block201->id." WHEN fieldid=".$field2310->id." THEN ".$block201->id." WHEN fieldid=".$field1164->id." THEN ".$block201->id." WHEN fieldid=".$field1174->id." THEN ".$block201->id." WHEN fieldid=".$field1176->id." THEN ".$block201->id." WHEN fieldid=".$field2047->id." THEN ".$block201->id." WHEN fieldid=".$field2056->id." THEN ".$block201->id." WHEN fieldid=".$field1813->id." THEN ".$block201->id." WHEN fieldid=".$field2176->id." THEN ".$block201->id." WHEN fieldid=".$field2074->id." THEN ".$block201->id." WHEN fieldid=".$field2073->id." THEN ".$block201->id." END WHERE fieldid IN (".$field1161->id.",".$field1163->id.",".$field1167->id.",".$field2329->id.",".$field1166->id.",".$field1165->id.",".$field1170->id.",".$field1218->id.",".$field2042->id.",".$field1175->id.",".$field2046->id.",".$field2050->id.",".$field1915->id.",".$field2081->id.",".$field2049->id.",".$field2044->id.",".$field1162->id.",".$field1169->id.",".$field2045->id.",".$field1945->id.",".$field2310->id.",".$field1164->id.",".$field1174->id.",".$field1176->id.",".$field2047->id.",".$field2056->id.",".$field1813->id.",".$field2176->id.",".$field2074->id.",".$field2073->id.")");

$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$block194 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleEstimates);
$field1094 = Vtiger_Field::getInstance('weight', $moduleEstimates);
$field1098 = Vtiger_Field::getInstance('full_unpack', $moduleEstimates);
$field1101 = Vtiger_Field::getInstance('interstate_mileage', $moduleEstimates);
$field1715 = Vtiger_Field::getInstance('accessorial_disc', $moduleEstimates);
$field1717 = Vtiger_Field::getInstance('sit_disc', $moduleEstimates);
$field2188 = Vtiger_Field::getInstance('estimate_type', $moduleEstimates);
$field1964 = Vtiger_Field::getInstance('full_pack_rate_override', $moduleEstimates);
$field2117 = Vtiger_Field::getInstance('express_truckload', $moduleQuotes);
$field1096 = Vtiger_Field::getInstance('full_pack', $moduleEstimates);
$field1100 = Vtiger_Field::getInstance('bottom_line_discount', $moduleEstimates);
$field1714 = Vtiger_Field::getInstance('linehaul_disc', $moduleEstimates);
$field1716 = Vtiger_Field::getInstance('packing_disc', $moduleEstimates);
$field1727 = Vtiger_Field::getInstance('interstate_effective_date', $moduleEstimates);
$field1053 = Vtiger_Field::getInstance('validtill', $moduleEstimates);
$field1963 = Vtiger_Field::getInstance('apply_full_pack_rate_override', $moduleEstimates);
$field1713 = Vtiger_Field::getInstance('irr_charge', $moduleEstimates);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1094->id." THEN 1 WHEN fieldid=".$field1098->id." THEN 3 WHEN fieldid=".$field1101->id." THEN 5 WHEN fieldid=".$field1715->id." THEN 7 WHEN fieldid=".$field1717->id." THEN 9 WHEN fieldid=".$field2188->id." THEN 11 WHEN fieldid=".$field1964->id." THEN 13 WHEN fieldid=".$field2117->id." THEN 15 WHEN fieldid=".$field1096->id." THEN 2 WHEN fieldid=".$field1100->id." THEN 4 WHEN fieldid=".$field1714->id." THEN 6 WHEN fieldid=".$field1716->id." THEN 8 WHEN fieldid=".$field1727->id." THEN 10 WHEN fieldid=".$field1053->id." THEN 12 WHEN fieldid=".$field1963->id." THEN 14 WHEN fieldid=".$field1713->id." THEN 16 END, block=CASE WHEN fieldid=".$field1094->id." THEN ".$block194->id." WHEN fieldid=".$field1098->id." THEN ".$block194->id." WHEN fieldid=".$field1101->id." THEN ".$block194->id." WHEN fieldid=".$field1715->id." THEN ".$block194->id." WHEN fieldid=".$field1717->id." THEN ".$block194->id." WHEN fieldid=".$field2188->id." THEN ".$block194->id." WHEN fieldid=".$field1964->id." THEN ".$block194->id." WHEN fieldid=".$field2117->id." THEN ".$block194->id." WHEN fieldid=".$field1096->id." THEN ".$block194->id." WHEN fieldid=".$field1100->id." THEN ".$block194->id." WHEN fieldid=".$field1714->id." THEN ".$block194->id." WHEN fieldid=".$field1716->id." THEN ".$block194->id." WHEN fieldid=".$field1727->id." THEN ".$block194->id." WHEN fieldid=".$field1053->id." THEN ".$block194->id." WHEN fieldid=".$field1963->id." THEN ".$block194->id." WHEN fieldid=".$field1713->id." THEN ".$block194->id." END WHERE fieldid IN (".$field1094->id.",".$field1098->id.",".$field1101->id.",".$field1715->id.",".$field1717->id.",".$field2188->id.",".$field1964->id.",".$field2117->id.",".$field1096->id.",".$field1100->id.",".$field1714->id.",".$field1716->id.",".$field1727->id.",".$field1053->id.",".$field1963->id.",".$field1713->id.")");
    
    $moduleEstimates = Vtiger_Module::getInstance('Estimates');

$block328 = Vtiger_Block::getInstance('LBL_ESTIMATES_DATES', $moduleEstimates);
$field2377 = Vtiger_Field::getInstance('pack_date', $moduleEstimates);
$field2379 = Vtiger_Field::getInstance('preffered_ppdate', $moduleEstimates);
$field2380 = Vtiger_Field::getInstance('load_to_date', $moduleEstimates);
$field2382 = Vtiger_Field::getInstance('deliver_date', $moduleEstimates);
$field2384 = Vtiger_Field::getInstance('preferred_pddate', $moduleEstimates);
$field2386 = Vtiger_Field::getInstance('survey_time', $moduleEstimates);
$field2388 = Vtiger_Field::getInstance('decision_date', $moduleEstimates);
$field2378 = Vtiger_Field::getInstance('pack_to_date', $moduleEstimates);
$field1711 = Vtiger_Field::getInstance('load_date', $moduleEstimates);
$field2381 = Vtiger_Field::getInstance('preferred_pldate', $moduleEstimates);
$field2383 = Vtiger_Field::getInstance('deliver_to_date', $moduleEstimates);
$field2385 = Vtiger_Field::getInstance('survey_date', $moduleEstimates);
$field2387 = Vtiger_Field::getInstance('followup_date', $moduleEstimates);
$field2389 = Vtiger_Field::getInstance('days_to_move', $moduleEstimates);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field2377->id." THEN 1 WHEN fieldid=".$field2379->id." THEN 3 WHEN fieldid=".$field2380->id." THEN 5 WHEN fieldid=".$field2382->id." THEN 7 WHEN fieldid=".$field2384->id." THEN 9 WHEN fieldid=".$field2386->id." THEN 11 WHEN fieldid=".$field2388->id." THEN 13 WHEN fieldid=".$field2378->id." THEN 2 WHEN fieldid=".$field1711->id." THEN 4 WHEN fieldid=".$field2381->id." THEN 6 WHEN fieldid=".$field2383->id." THEN 8 WHEN fieldid=".$field2385->id." THEN 10 WHEN fieldid=".$field2387->id." THEN 12 WHEN fieldid=".$field2389->id." THEN 14 END, block=CASE WHEN fieldid=".$field2377->id." THEN ".$block328->id." WHEN fieldid=".$field2379->id." THEN ".$block328->id." WHEN fieldid=".$field2380->id." THEN ".$block328->id." WHEN fieldid=".$field2382->id." THEN ".$block328->id." WHEN fieldid=".$field2384->id." THEN ".$block328->id." WHEN fieldid=".$field2386->id." THEN ".$block328->id." WHEN fieldid=".$field2388->id." THEN ".$block328->id." WHEN fieldid=".$field2378->id." THEN ".$block328->id." WHEN fieldid=".$field1711->id." THEN ".$block328->id." WHEN fieldid=".$field2381->id." THEN ".$block328->id." WHEN fieldid=".$field2383->id." THEN ".$block328->id." WHEN fieldid=".$field2385->id." THEN ".$block328->id." WHEN fieldid=".$field2387->id." THEN ".$block328->id." WHEN fieldid=".$field2389->id." THEN ".$block328->id." END WHERE fieldid IN (".$field2377->id.",".$field2379->id.",".$field2380->id.",".$field2382->id.",".$field2384->id.",".$field2386->id.",".$field2388->id.",".$field2378->id.",".$field1711->id.",".$field2381->id.",".$field2383->id.",".$field2385->id.",".$field2387->id.",".$field2389->id.")");
    
//Hiding these fields as they are not being used
 Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = '3' WHERE `fieldname` IN ('ba_code', 'ba_name', 'ba_city', 'ba_state', 'oa_code', 'oa_name', 'oa_city', 'oa_state', 'ea_code', 'ea_name', 'ea_city', 'ea_state', 'ha_code', 'ha_name', 'ha_city', 'ha_state', 'da_code', 'da_name', 'da_city', 'da_state')");

 Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = '3' WHERE `fieldname` IN ('ba_code', 'ba_name', 'ba_city', 'ba_state', 'oa_code', 'oa_name', 'oa_city', 'oa_state', 'ea_code', 'ea_name', 'ea_city', 'ea_state', 'ha_code', 'ha_name', 'ha_city', 'ha_state', 'da_code', 'da_name', 'da_city', 'da_state')");

Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_picklist` WHERE `name` = 'source_type'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 16 WHERE `fieldname` = 'source_type'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_payment_type_sts` WHERE `payment_type_sts` = 'NAT'");

$moduleAccounts = Vtiger_Module::getInstance('Accounts');

$block9 = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleAccounts);
$field1 = Vtiger_Field::getInstance('accountname', $moduleAccounts);
$field2 = Vtiger_Field::getInstance('account_no', $moduleAccounts);
$field3 = Vtiger_Field::getInstance('phone', $moduleAccounts);
$field7 = Vtiger_Field::getInstance('otherphone', $moduleAccounts);
$field5 = Vtiger_Field::getInstance('fax', $moduleAccounts);
$field9 = Vtiger_Field::getInstance('email1', $moduleAccounts);
$field12 = Vtiger_Field::getInstance('ownership', $moduleAccounts);
$field13 = Vtiger_Field::getInstance('rating', $moduleAccounts);
$field15 = Vtiger_Field::getInstance('siccode', $moduleAccounts);
$field17 = Vtiger_Field::getInstance('annual_revenue', $moduleAccounts);
$field19 = Vtiger_Field::getInstance('notify_owner', $moduleAccounts);
$field21 = Vtiger_Field::getInstance('createdtime', $moduleAccounts);
$field726 = Vtiger_Field::getInstance('created_user_id', $moduleAccounts);
$field2494 = Vtiger_Field::getInstance('brand', $moduleAccounts);
$field1896 = Vtiger_Field::getInstance('agentid', $moduleAccounts);
$field2271 = Vtiger_Field::getInstance('apn', $moduleAccounts);
$field2458 = Vtiger_Field::getInstance('primary_phone_type', $moduleAccounts);
$field2459 = Vtiger_Field::getInstance('secondary_phone_type', $moduleAccounts);
$field4 = Vtiger_Field::getInstance('website', $moduleAccounts);
$field6 = Vtiger_Field::getInstance('tickersymbol', $moduleAccounts);
$field8 = Vtiger_Field::getInstance('account_id', $moduleAccounts);
$field10 = Vtiger_Field::getInstance('employees', $moduleAccounts);
$field11 = Vtiger_Field::getInstance('email2', $moduleAccounts);
$field14 = Vtiger_Field::getInstance('industry', $moduleAccounts);
$field16 = Vtiger_Field::getInstance('accounttype', $moduleAccounts);
$field18 = Vtiger_Field::getInstance('emailoptout', $moduleAccounts);
$field20 = Vtiger_Field::getInstance('assigned_user_id', $moduleAccounts);
$field22 = Vtiger_Field::getInstance('modifiedtime', $moduleAccounts);
$field700 = Vtiger_Field::getInstance('isconvertedfromlead', $moduleAccounts);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1->id." THEN 1 WHEN fieldid=".$field2->id." THEN 3 WHEN fieldid=".$field3->id." THEN 5 WHEN fieldid=".$field7->id." THEN 7 WHEN fieldid=".$field5->id." THEN 9 WHEN fieldid=".$field9->id." THEN 11 WHEN fieldid=".$field12->id." THEN 13 WHEN fieldid=".$field13->id." THEN 15 WHEN fieldid=".$field15->id." THEN 17 WHEN fieldid=".$field17->id." THEN 19 WHEN fieldid=".$field19->id." THEN 21 WHEN fieldid=".$field21->id." THEN 23 WHEN fieldid=".$field726->id." THEN 25 WHEN fieldid=".$field2494->id." THEN 27 WHEN fieldid=".$field1896->id." THEN 29 WHEN fieldid=".$field2271->id." THEN 2 WHEN fieldid=".$field2458->id." THEN 4 WHEN fieldid=".$field2459->id." THEN 6 WHEN fieldid=".$field4->id." THEN 8 WHEN fieldid=".$field6->id." THEN 10 WHEN fieldid=".$field8->id." THEN 12 WHEN fieldid=".$field10->id." THEN 14 WHEN fieldid=".$field11->id." THEN 16 WHEN fieldid=".$field14->id." THEN 18 WHEN fieldid=".$field16->id." THEN 20 WHEN fieldid=".$field18->id." THEN 22 WHEN fieldid=".$field20->id." THEN 24 WHEN fieldid=".$field22->id." THEN 26 WHEN fieldid=".$field700->id." THEN 28 END, block=CASE WHEN fieldid=".$field1->id." THEN ".$block9->id." WHEN fieldid=".$field2->id." THEN ".$block9->id." WHEN fieldid=".$field3->id." THEN ".$block9->id." WHEN fieldid=".$field7->id." THEN ".$block9->id." WHEN fieldid=".$field5->id." THEN ".$block9->id." WHEN fieldid=".$field9->id." THEN ".$block9->id." WHEN fieldid=".$field12->id." THEN ".$block9->id." WHEN fieldid=".$field13->id." THEN ".$block9->id." WHEN fieldid=".$field15->id." THEN ".$block9->id." WHEN fieldid=".$field17->id." THEN ".$block9->id." WHEN fieldid=".$field19->id." THEN ".$block9->id." WHEN fieldid=".$field21->id." THEN ".$block9->id." WHEN fieldid=".$field726->id." THEN ".$block9->id." WHEN fieldid=".$field2494->id." THEN ".$block9->id." WHEN fieldid=".$field1896->id." THEN ".$block9->id." WHEN fieldid=".$field2271->id." THEN ".$block9->id." WHEN fieldid=".$field2458->id." THEN ".$block9->id." WHEN fieldid=".$field2459->id." THEN ".$block9->id." WHEN fieldid=".$field4->id." THEN ".$block9->id." WHEN fieldid=".$field6->id." THEN ".$block9->id." WHEN fieldid=".$field8->id." THEN ".$block9->id." WHEN fieldid=".$field10->id." THEN ".$block9->id." WHEN fieldid=".$field11->id." THEN ".$block9->id." WHEN fieldid=".$field14->id." THEN ".$block9->id." WHEN fieldid=".$field16->id." THEN ".$block9->id." WHEN fieldid=".$field18->id." THEN ".$block9->id." WHEN fieldid=".$field20->id." THEN ".$block9->id." WHEN fieldid=".$field22->id." THEN ".$block9->id." WHEN fieldid=".$field700->id." THEN ".$block9->id." END WHERE fieldid IN (".$field1->id.",".$field2->id.",".$field3->id.",".$field7->id.",".$field5->id.",".$field9->id.",".$field12->id.",".$field13->id.",".$field15->id.",".$field17->id.",".$field19->id.",".$field21->id.",".$field726->id.",".$field2494->id.",".$field1896->id.",".$field2271->id.",".$field2458->id.",".$field2459->id.",".$field4->id.",".$field6->id.",".$field8->id.",".$field10->id.",".$field11->id.",".$field14->id.",".$field16->id.",".$field18->id.",".$field20->id.",".$field22->id.",".$field700->id.")");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldname` = 'agmt_id' AND `tablename` = 'vtiger_potential'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldname` = 'subagmt_nbr' AND `tablename` = 'vtiger_potential'");

//Add 3+ bedroom as option
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_dwelling_type` (dwelling_typeid, dwelling_type, sortorderid, presence) SELECT id + 2, '3+ Bedroom Apt', id + 2, 1 FROM `vtiger_dwelling_type_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_dwelling_type` WHERE dwelling_type = '3+ Bedroom Apt')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";