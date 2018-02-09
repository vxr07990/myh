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

$block302 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $moduleOpportunities);

$block303 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_HIDEDELETE', $moduleOpportunities);

$field1166 = Vtiger_Field::getInstance('potentialname', $moduleOpportunities);

$field1168 = Vtiger_Field::getInstance('contact_id', $moduleOpportunities);

$field1173 = Vtiger_Field::getInstance('sales_stage', $moduleOpportunities);

$field1172 = Vtiger_Field::getInstance('business_line', $moduleOpportunities);

$field1170 = Vtiger_Field::getInstance('opportunity_type', $moduleOpportunities);

$field1176 = Vtiger_Field::getInstance('assigned_user_id', $moduleOpportunities);

$field1169 = Vtiger_Field::getInstance('related_to', $moduleOpportunities);

$field1181 = Vtiger_Field::getInstance('created_user_id', $moduleOpportunities);

$field1185 = Vtiger_Field::getInstance('closingdate', $moduleOpportunities);

$field1837 = Vtiger_Field::getInstance('assigned_date', $moduleOpportunities);

$field1841 = Vtiger_Field::getInstance('promotion_code', $moduleOpportunities);

$field2044 = Vtiger_Field::getInstance('billing_type', $moduleOpportunities);

$field2086 = Vtiger_Field::getInstance('lock_military_fields', $moduleOpportunities);

$field1830 = Vtiger_Field::getInstance('employer_comments', $moduleOpportunities);

$field1835 = Vtiger_Field::getInstance('move_type', $moduleOpportunities);

$field1167 = Vtiger_Field::getInstance('potential_no', $moduleOpportunities);

$field1175 = Vtiger_Field::getInstance('amount', $moduleOpportunities);

$field1836 = Vtiger_Field::getInstance('business_channel', $moduleOpportunities);

$field2085 = Vtiger_Field::getInstance('shipper_type', $moduleOpportunities);

$field1225 = Vtiger_Field::getInstance('sales_person', $moduleOpportunities);

$field1833 = Vtiger_Field::getInstance('order_number', $moduleOpportunities);

$field1182 = Vtiger_Field::getInstance('createdtime', $moduleOpportunities);

$field1183 = Vtiger_Field::getInstance('modifiedtime', $moduleOpportunities);

$field1838 = Vtiger_Field::getInstance('funded', $moduleOpportunities);

$field1846 = Vtiger_Field::getInstance('program_terms', $moduleOpportunities);

$field1840 = Vtiger_Field::getInstance('moving_a_vehicle', $moduleOpportunities);

$field1831 = Vtiger_Field::getInstance('special_terms', $moduleOpportunities);

$field1849 = Vtiger_Field::getInstance('opp_type', $moduleOpportunities);

$field1174 = Vtiger_Field::getInstance('leadsource', $moduleOpportunities);

$field1839 = Vtiger_Field::getInstance('receive_date', $moduleOpportunities);

$field1953 = Vtiger_Field::getInstance('converted_from', $moduleOpportunities);

$field1184 = Vtiger_Field::getInstance('isconvertedfromlead', $moduleOpportunities);

$field1834 = Vtiger_Field::getInstance('opportunity_detail_disposition', $moduleOpportunities);

$field1832 = Vtiger_Field::getInstance('opportunity_disposition', $moduleOpportunities);

$field1845 = Vtiger_Field::getInstance('phone_estimate', $moduleOpportunities);

$field1843 = Vtiger_Field::getInstance('out_of_origin', $moduleOpportunities);

$field1844 = Vtiger_Field::getInstance('small_move', $moduleOpportunities);

$field1842 = Vtiger_Field::getInstance('out_of_area', $moduleOpportunities);

$field1177 = Vtiger_Field::getInstance('nextstep', $moduleOpportunities);

$field1180 = Vtiger_Field::getInstance('forecast_amount', $moduleOpportunities);

$field1179 = Vtiger_Field::getInstance('probability', $moduleOpportunities);

//JG NOTE: I need the self haul field to be added and sequenced logically.
//adding to here seemed better than adding another order script after it.
//I did not change block assignment
$fieldSelf_haul = Vtiger_Field::getInstance('self_haul', $moduleOpportunities);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1166->id." THEN 1 WHEN fieldid=".$field1168->id." THEN 3 WHEN fieldid=".$field1173->id." THEN 5 WHEN fieldid=".$field1172->id." THEN 7 WHEN fieldid=".$field1170->id." THEN 9 WHEN fieldid=".$field1176->id." THEN 11 WHEN fieldid=".$field1169->id." THEN 13 WHEN fieldid=".$field1181->id." THEN 15 WHEN fieldid=".$field1185->id." THEN 17 WHEN fieldid=".$field1837->id." THEN 19 WHEN fieldid=".$field1841->id." THEN 21 WHEN fieldid=".$field2044->id." THEN 23 WHEN fieldid=".$field2086->id." THEN 25 WHEN fieldid=".$field1830->id." THEN 28 WHEN fieldid=".$field1835->id." THEN 2 WHEN fieldid=".$field1167->id." THEN 4 WHEN fieldid=".$field1175->id." THEN 6 WHEN fieldid=".$field1836->id." THEN 8 WHEN fieldid=".$field2085->id." THEN 10 WHEN fieldid=".$field1225->id." THEN 12 WHEN fieldid=".$field1833->id." THEN 14 WHEN fieldid=".$field1182->id." THEN 16 WHEN fieldid=".$field1183->id." THEN 18 WHEN fieldid=".$field1838->id." THEN 20 WHEN fieldid=".$field1846->id." THEN 22 WHEN fieldid=".$field1840->id." THEN 26 WHEN fieldid=".$field1831->id." THEN 27 WHEN fieldid=".$field1849->id." THEN 1 WHEN fieldid=".$field1174->id." THEN 3 WHEN fieldid=".$field1839->id." THEN 5 WHEN fieldid=".$field1953->id." THEN 7 WHEN fieldid=".$field1184->id." THEN 9 WHEN fieldid=".$field1834->id." THEN 11 WHEN fieldid=".$field1832->id." THEN 2 WHEN fieldid=".$field1845->id." THEN 4 WHEN fieldid=".$field1843->id." THEN 6 WHEN fieldid=".$field1844->id." THEN 8 WHEN fieldid=".$field1842->id." THEN 10 WHEN fieldid=".$field1177->id." THEN 1 WHEN fieldid=".$field1180->id." THEN 3 WHEN fieldid=".$field1179->id." THEN 2 WHEN fieldid=".$fieldSelf_haul->id." THEN 24 END, block=CASE WHEN fieldid=".$field1166->id." THEN ".$block201->id." WHEN fieldid=".$field1168->id." THEN ".$block201->id." WHEN fieldid=".$field1173->id." THEN ".$block201->id." WHEN fieldid=".$field1172->id." THEN ".$block201->id." WHEN fieldid=".$field1170->id." THEN ".$block201->id." WHEN fieldid=".$field1176->id." THEN ".$block201->id." WHEN fieldid=".$field1169->id." THEN ".$block201->id." WHEN fieldid=".$field1181->id." THEN ".$block201->id." WHEN fieldid=".$field1185->id." THEN ".$block201->id." WHEN fieldid=".$field1837->id." THEN ".$block201->id." WHEN fieldid=".$field1841->id." THEN ".$block201->id." WHEN fieldid=".$field2044->id." THEN ".$block201->id." WHEN fieldid=".$field2086->id." THEN ".$block201->id." WHEN fieldid=".$field1830->id." THEN ".$block201->id." WHEN fieldid=".$field1835->id." THEN ".$block201->id." WHEN fieldid=".$field1167->id." THEN ".$block201->id." WHEN fieldid=".$field1175->id." THEN ".$block201->id." WHEN fieldid=".$field1836->id." THEN ".$block201->id." WHEN fieldid=".$field2085->id." THEN ".$block201->id." WHEN fieldid=".$field1225->id." THEN ".$block201->id." WHEN fieldid=".$field1833->id." THEN ".$block201->id." WHEN fieldid=".$field1182->id." THEN ".$block201->id." WHEN fieldid=".$field1183->id." THEN ".$block201->id." WHEN fieldid=".$field1838->id." THEN ".$block201->id." WHEN fieldid=".$field1846->id." THEN ".$block201->id." WHEN fieldid=".$field1840->id." THEN ".$block201->id." WHEN fieldid=".$field1831->id." THEN ".$block201->id." WHEN fieldid=".$field1849->id." THEN ".$block302->id." WHEN fieldid=".$field1174->id." THEN ".$block302->id." WHEN fieldid=".$field1839->id." THEN ".$block302->id." WHEN fieldid=".$field1953->id." THEN ".$block302->id." WHEN fieldid=".$field1184->id." THEN ".$block302->id." WHEN fieldid=".$field1834->id." THEN ".$block302->id." WHEN fieldid=".$field1832->id." THEN ".$block302->id." WHEN fieldid=".$field1845->id." THEN ".$block302->id." WHEN fieldid=".$field1843->id." THEN ".$block302->id." WHEN fieldid=".$field1844->id." THEN ".$block302->id." WHEN fieldid=".$field1842->id." THEN ".$block302->id." WHEN fieldid=".$field1177->id." THEN ".$block303->id." WHEN fieldid=".$field1180->id." THEN ".$block303->id." WHEN fieldid=".$field1179->id." THEN ".$block303->id." END WHERE fieldid IN (".$field1166->id.",".$field1168->id.",".$field1173->id.",".$field1172->id.",".$field1170->id.",".$field1176->id.",".$field1169->id.",".$field1181->id.",".$field1185->id.",".$field1837->id.",".$field1841->id.",".$field2044->id.",".$field2086->id.",".$field1830->id.",".$field1835->id.",".$field1167->id.",".$field1175->id.",".$field1836->id.",".$field2085->id.",".$field1225->id.",".$field1833->id.",".$field1182->id.",".$field1183->id.",".$field1838->id.",".$field1846->id.",".$field1840->id.",".$field1831->id.",".$field1849->id.",".$field1174->id.",".$field1839->id.",".$field1953->id.",".$field1184->id.",".$field1834->id.",".$field1832->id.",".$field1845->id.",".$field1843->id.",".$field1844->id.",".$field1842->id.",".$field1177->id.",".$field1180->id.",".$field1179->id.")");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid IN (".$field1177->id.",".$field1180->id.",".$field1179->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";