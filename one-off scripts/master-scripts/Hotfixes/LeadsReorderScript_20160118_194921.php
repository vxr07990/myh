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



$moduleLeads = Vtiger_Module::getInstance('Leads');


$block13 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $moduleLeads);

$block300 = Vtiger_Block::getInstance('LBL_LEADS_BLOCK_LMPDETAILS', $moduleLeads);

$block124 = Vtiger_Block::getInstance('LBL_LEADS_NATIONALACCOUNT', $moduleLeads);

$field38 = Vtiger_Field::getInstance('firstname', $moduleLeads);

$field1847 = Vtiger_Field::getInstance('primary_phone_type', $moduleLeads);

$field46 = Vtiger_Field::getInstance('email', $moduleLeads);

$field1964 = Vtiger_Field::getInstance('lead_receive_date', $moduleLeads);

$field1790 = Vtiger_Field::getInstance('move_type', $moduleLeads);

$field727 = Vtiger_Field::getInstance('created_user_id', $moduleLeads);

$field57 = Vtiger_Field::getInstance('modifiedtime', $moduleLeads);

$field1848 = Vtiger_Field::getInstance('primary_phone_ext', $moduleLeads);

$field1707 = Vtiger_Field::getInstance('sales_person', $moduleLeads);

$field1812 = Vtiger_Field::getInstance('employer_comments', $moduleLeads);

$field41 = Vtiger_Field::getInstance('lastname', $moduleLeads);

$field40 = Vtiger_Field::getInstance('phone', $moduleLeads);

$field55 = Vtiger_Field::getInstance('secondaryemail', $moduleLeads);

$field50 = Vtiger_Field::getInstance('leadstatus', $moduleLeads);

$field1723 = Vtiger_Field::getInstance('shipper_type', $moduleLeads);

$field56 = Vtiger_Field::getInstance('createdtime', $moduleLeads);

$field54 = Vtiger_Field::getInstance('assigned_user_id', $moduleLeads);

$field42 = Vtiger_Field::getInstance('mobile', $moduleLeads);

$field751 = Vtiger_Field::getInstance('business_line', $moduleLeads);

$field1965 = Vtiger_Field::getInstance('prefer_time', $moduleLeads);

$field1970 = Vtiger_Field::getInstance('offer_number', $moduleLeads);

$field1961 = Vtiger_Field::getInstance('cc_disposition', $moduleLeads);

$field1968 = Vtiger_Field::getInstance('program_name', $moduleLeads);

$field1969 = Vtiger_Field::getInstance('source_name', $moduleLeads);

$field1792 = Vtiger_Field::getInstance('funded', $moduleLeads);

$field1796 = Vtiger_Field::getInstance('phone_estimate', $moduleLeads);

$field1791 = Vtiger_Field::getInstance('business_channel', $moduleLeads);

$field1795 = Vtiger_Field::getInstance('small_move', $moduleLeads);

$field1813 = Vtiger_Field::getInstance('special_terms', $moduleLeads);

$field1966 = Vtiger_Field::getInstance('timezone', $moduleLeads);

$field1725 = Vtiger_Field::getInstance('lead_type', $moduleLeads);

$field1793 = Vtiger_Field::getInstance('out_of_area', $moduleLeads);

$field1960 = Vtiger_Field::getInstance('lmp_lead_id', $moduleLeads);

$field39 = Vtiger_Field::getInstance('lead_no', $moduleLeads);

$field709 = Vtiger_Field::getInstance('emailoptout', $moduleLeads);

$field1962 = Vtiger_Field::getInstance('brand', $moduleLeads);

$field1850 = Vtiger_Field::getInstance('disposition_lost_reasons', $moduleLeads);

$field47 = Vtiger_Field::getInstance('leadsource', $moduleLeads);

$field1983 = Vtiger_Field::getInstance('offer_valuation', $moduleLeads);

$field1967 = Vtiger_Field::getInstance('languages', $moduleLeads);

$field1794 = Vtiger_Field::getInstance('out_of_origin', $moduleLeads);

$field1984 = Vtiger_Field::getInstance('out_of_time', $moduleLeads);

$field1971 = Vtiger_Field::getInstance('promotion_terms', $moduleLeads);

$field1963 = Vtiger_Field::getInstance('organization', $moduleLeads);

$field59 = Vtiger_Field::getInstance('lane', $moduleLeads);

$field61 = Vtiger_Field::getInstance('city', $moduleLeads);

$field60 = Vtiger_Field::getInstance('code', $moduleLeads);

$field64 = Vtiger_Field::getInstance('pobox', $moduleLeads);

$field63 = Vtiger_Field::getInstance('state', $moduleLeads);

$field62 = Vtiger_Field::getInstance('country', $moduleLeads);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field38->id." THEN 1 WHEN fieldid=".$field1847->id." THEN 3 WHEN fieldid=".$field46->id." THEN 5 WHEN fieldid=".$field1964->id." THEN 7 WHEN fieldid=".$field1790->id." THEN 9 WHEN fieldid=".$field727->id." THEN 11 WHEN fieldid=".$field57->id." THEN 13 WHEN fieldid=".$field1848->id." THEN 15 WHEN fieldid=".$field1707->id." THEN 17 WHEN fieldid=".$field1812->id." THEN 19 WHEN fieldid=".$field41->id." THEN 2 WHEN fieldid=".$field40->id." THEN 4 WHEN fieldid=".$field55->id." THEN 6 WHEN fieldid=".$field50->id." THEN 8 WHEN fieldid=".$field1723->id." THEN 10 WHEN fieldid=".$field56->id." THEN 12 WHEN fieldid=".$field54->id." THEN 14 WHEN fieldid=".$field42->id." THEN 16 WHEN fieldid=".$field751->id." THEN 18 WHEN fieldid=".$field1965->id." THEN 20 WHEN fieldid=".$field1970->id." THEN 1 WHEN fieldid=".$field1961->id." THEN 3 WHEN fieldid=".$field1968->id." THEN 5 WHEN fieldid=".$field1969->id." THEN 7 WHEN fieldid=".$field1792->id." THEN 9 WHEN fieldid=".$field1796->id." THEN 11 WHEN fieldid=".$field1791->id." THEN 13 WHEN fieldid=".$field1795->id." THEN 15 WHEN fieldid=".$field1813->id." THEN 17 WHEN fieldid=".$field1966->id." THEN 19 WHEN fieldid=".$field1725->id." THEN 21 WHEN fieldid=".$field1793->id." THEN 23 WHEN fieldid=".$field1960->id." THEN 2 WHEN fieldid=".$field39->id." THEN 4 WHEN fieldid=".$field709->id." THEN 6 WHEN fieldid=".$field1962->id." THEN 8 WHEN fieldid=".$field1850->id." THEN 10 WHEN fieldid=".$field47->id." THEN 12 WHEN fieldid=".$field1983->id." THEN 14 WHEN fieldid=".$field1967->id." THEN 16 WHEN fieldid=".$field1794->id." THEN 18 WHEN fieldid=".$field1984->id." THEN 20 WHEN fieldid=".$field1971->id." THEN 22 WHEN fieldid=".$field1963->id." THEN 1 WHEN fieldid=".$field59->id." THEN 3 WHEN fieldid=".$field61->id." THEN 5 WHEN fieldid=".$field60->id." THEN 7 WHEN fieldid=".$field64->id." THEN 2 WHEN fieldid=".$field63->id." THEN 4 WHEN fieldid=".$field62->id." THEN 6 END, block=CASE WHEN fieldid=".$field38->id." THEN ".$block13->id." WHEN fieldid=".$field1847->id." THEN ".$block13->id." WHEN fieldid=".$field46->id." THEN ".$block13->id." WHEN fieldid=".$field1964->id." THEN ".$block13->id." WHEN fieldid=".$field1790->id." THEN ".$block13->id." WHEN fieldid=".$field727->id." THEN ".$block13->id." WHEN fieldid=".$field57->id." THEN ".$block13->id." WHEN fieldid=".$field1848->id." THEN ".$block13->id." WHEN fieldid=".$field1707->id." THEN ".$block13->id." WHEN fieldid=".$field1812->id." THEN ".$block13->id." WHEN fieldid=".$field41->id." THEN ".$block13->id." WHEN fieldid=".$field40->id." THEN ".$block13->id." WHEN fieldid=".$field55->id." THEN ".$block13->id." WHEN fieldid=".$field50->id." THEN ".$block13->id." WHEN fieldid=".$field1723->id." THEN ".$block13->id." WHEN fieldid=".$field56->id." THEN ".$block13->id." WHEN fieldid=".$field54->id." THEN ".$block13->id." WHEN fieldid=".$field42->id." THEN ".$block13->id." WHEN fieldid=".$field751->id." THEN ".$block13->id." WHEN fieldid=".$field1965->id." THEN ".$block13->id." WHEN fieldid=".$field1970->id." THEN ".$block300->id." WHEN fieldid=".$field1961->id." THEN ".$block300->id." WHEN fieldid=".$field1968->id." THEN ".$block300->id." WHEN fieldid=".$field1969->id." THEN ".$block300->id." WHEN fieldid=".$field1792->id." THEN ".$block300->id." WHEN fieldid=".$field1796->id." THEN ".$block300->id." WHEN fieldid=".$field1791->id." THEN ".$block300->id." WHEN fieldid=".$field1795->id." THEN ".$block300->id." WHEN fieldid=".$field1813->id." THEN ".$block300->id." WHEN fieldid=".$field1966->id." THEN ".$block300->id." WHEN fieldid=".$field1725->id." THEN ".$block300->id." WHEN fieldid=".$field1793->id." THEN ".$block300->id." WHEN fieldid=".$field1960->id." THEN ".$block300->id." WHEN fieldid=".$field39->id." THEN ".$block300->id." WHEN fieldid=".$field709->id." THEN ".$block300->id." WHEN fieldid=".$field1962->id." THEN ".$block300->id." WHEN fieldid=".$field1850->id." THEN ".$block300->id." WHEN fieldid=".$field47->id." THEN ".$block300->id." WHEN fieldid=".$field1983->id." THEN ".$block300->id." WHEN fieldid=".$field1967->id." THEN ".$block300->id." WHEN fieldid=".$field1794->id." THEN ".$block300->id." WHEN fieldid=".$field1984->id." THEN ".$block300->id." WHEN fieldid=".$field1971->id." THEN ".$block300->id." WHEN fieldid=".$field1963->id." THEN ".$block124->id." WHEN fieldid=".$field59->id." THEN ".$block124->id." WHEN fieldid=".$field61->id." THEN ".$block124->id." WHEN fieldid=".$field60->id." THEN ".$block124->id." WHEN fieldid=".$field64->id." THEN ".$block124->id." WHEN fieldid=".$field63->id." THEN ".$block124->id." WHEN fieldid=".$field62->id." THEN ".$block124->id." END WHERE fieldid IN (".$field38->id.",".$field1847->id.",".$field46->id.",".$field1964->id.",".$field1790->id.",".$field727->id.",".$field57->id.",".$field1848->id.",".$field1707->id.",".$field1812->id.",".$field41->id.",".$field40->id.",".$field55->id.",".$field50->id.",".$field1723->id.",".$field56->id.",".$field54->id.",".$field42->id.",".$field751->id.",".$field1965->id.",".$field1970->id.",".$field1961->id.",".$field1968->id.",".$field1969->id.",".$field1792->id.",".$field1796->id.",".$field1791->id.",".$field1795->id.",".$field1813->id.",".$field1966->id.",".$field1725->id.",".$field1793->id.",".$field1960->id.",".$field39->id.",".$field709->id.",".$field1962->id.",".$field1850->id.",".$field47->id.",".$field1983->id.",".$field1967->id.",".$field1794->id.",".$field1984->id.",".$field1971->id.",".$field1963->id.",".$field59->id.",".$field61->id.",".$field60->id.",".$field64->id.",".$field63->id.",".$field62->id.")");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field1812->id);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";