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

$block120 = Vtiger_Block::getInstance('LBL_LEADS_DATES', $moduleLeads);
$field772 = Vtiger_Field::getInstance('pack', $moduleLeads);
$field774 = Vtiger_Field::getInstance('preferred_ppdate', $moduleLeads);
$field776 = Vtiger_Field::getInstance('load_to', $moduleLeads);
$field778 = Vtiger_Field::getInstance('deliver', $moduleLeads);
$field780 = Vtiger_Field::getInstance('preferred_pddate', $moduleLeads);
$field2034 = Vtiger_Field::getInstance('days_to_move', $moduleLeads);
$field2140 = Vtiger_Field::getInstance('flexible_on_days', $moduleLeads);
$field773 = Vtiger_Field::getInstance('pack_to', $moduleLeads);
$field775 = Vtiger_Field::getInstance('load_from', $moduleLeads);
$field777 = Vtiger_Field::getInstance('preferred_pldate', $moduleLeads);
$field779 = Vtiger_Field::getInstance('deliver_to', $moduleLeads);
$field782 = Vtiger_Field::getInstance('decision', $moduleLeads);
$field781 = Vtiger_Field::getInstance('follow_up', $moduleLeads);
$field2141 = Vtiger_Field::getInstance('fulfillment_date', $moduleLeads);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field772->id." THEN 1 WHEN fieldid=".$field774->id." THEN 3 WHEN fieldid=".$field776->id." THEN 5 WHEN fieldid=".$field778->id." THEN 7 WHEN fieldid=".$field780->id." THEN 9 WHEN fieldid=".$field2034->id." THEN 11 WHEN fieldid=".$field2140->id." THEN 13 WHEN fieldid=".$field773->id." THEN 2 WHEN fieldid=".$field775->id." THEN 4 WHEN fieldid=".$field777->id." THEN 6 WHEN fieldid=".$field779->id." THEN 8 WHEN fieldid=".$field782->id." THEN 10 WHEN fieldid=".$field781->id." THEN 12 WHEN fieldid=".$field2141->id." THEN 14 END, block=CASE WHEN fieldid=".$field772->id." THEN ".$block120->id." WHEN fieldid=".$field774->id." THEN ".$block120->id." WHEN fieldid=".$field776->id." THEN ".$block120->id." WHEN fieldid=".$field778->id." THEN ".$block120->id." WHEN fieldid=".$field780->id." THEN ".$block120->id." WHEN fieldid=".$field2034->id." THEN ".$block120->id." WHEN fieldid=".$field2140->id." THEN ".$block120->id." WHEN fieldid=".$field773->id." THEN ".$block120->id." WHEN fieldid=".$field775->id." THEN ".$block120->id." WHEN fieldid=".$field777->id." THEN ".$block120->id." WHEN fieldid=".$field779->id." THEN ".$block120->id." WHEN fieldid=".$field782->id." THEN ".$block120->id." WHEN fieldid=".$field781->id." THEN ".$block120->id." WHEN fieldid=".$field2141->id." THEN ".$block120->id." END WHERE fieldid IN (".$field772->id.",".$field774->id.",".$field776->id.",".$field778->id.",".$field780->id.",".$field2034->id.",".$field2140->id.",".$field773->id.",".$field775->id.",".$field777->id.",".$field779->id.",".$field782->id.",".$field781->id.",".$field2141->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";