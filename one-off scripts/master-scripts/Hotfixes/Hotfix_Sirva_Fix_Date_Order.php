<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = (SELECT `blockid` FROM `vtiger_blocks` WHERE `blocklabel` = 'LBL_ESTIMATES_DATES' AND `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Estimates')) WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Estimates') AND `fieldname` = 'load_date'");

$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$block332 = Vtiger_Block::getInstance('LBL_ESTIMATES_DATES', $moduleEstimates);
$field2435 = Vtiger_Field::getInstance('pack_date', $moduleEstimates);
$field2450 = Vtiger_Field::getInstance('preffered_ppdate', $moduleEstimates);
$field2438 = Vtiger_Field::getInstance('load_to_date', $moduleEstimates);
$field2440 = Vtiger_Field::getInstance('deliver_date', $moduleEstimates);
$field2452 = Vtiger_Field::getInstance('preferred_pddate', $moduleEstimates);
$field2444 = Vtiger_Field::getInstance('survey_time', $moduleEstimates);
$field2446 = Vtiger_Field::getInstance('decision_date', $moduleEstimates);
$field2436 = Vtiger_Field::getInstance('pack_to_date', $moduleEstimates);
$field1711 = Vtiger_Field::getInstance('load_date', $moduleEstimates);
$field2451 = Vtiger_Field::getInstance('preferred_pldate', $moduleEstimates);
$field2441 = Vtiger_Field::getInstance('deliver_to_date', $moduleEstimates);
$field2443 = Vtiger_Field::getInstance('survey_date', $moduleEstimates);
$field2445 = Vtiger_Field::getInstance('followup_date', $moduleEstimates);
$field2456 = Vtiger_Field::getInstance('days_to_move', $moduleEstimates);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field2435->id." THEN 1 WHEN fieldid=".$field2450->id." THEN 3 WHEN fieldid=".$field2438->id." THEN 5 WHEN fieldid=".$field2440->id." THEN 7 WHEN fieldid=".$field2452->id." THEN 9 WHEN fieldid=".$field2444->id." THEN 11 WHEN fieldid=".$field2446->id." THEN 13 WHEN fieldid=".$field2436->id." THEN 2 WHEN fieldid=".$field1711->id." THEN 4 WHEN fieldid=".$field2451->id." THEN 6 WHEN fieldid=".$field2441->id." THEN 8 WHEN fieldid=".$field2443->id." THEN 10 WHEN fieldid=".$field2445->id." THEN 12 WHEN fieldid=".$field2456->id." THEN 14 END, block=CASE WHEN fieldid=".$field2435->id." THEN ".$block332->id." WHEN fieldid=".$field2450->id." THEN ".$block332->id." WHEN fieldid=".$field2438->id." THEN ".$block332->id." WHEN fieldid=".$field2440->id." THEN ".$block332->id." WHEN fieldid=".$field2452->id." THEN ".$block332->id." WHEN fieldid=".$field2444->id." THEN ".$block332->id." WHEN fieldid=".$field2446->id." THEN ".$block332->id." WHEN fieldid=".$field2436->id." THEN ".$block332->id." WHEN fieldid=".$field1711->id." THEN ".$block332->id." WHEN fieldid=".$field2451->id." THEN ".$block332->id." WHEN fieldid=".$field2441->id." THEN ".$block332->id." WHEN fieldid=".$field2443->id." THEN ".$block332->id." WHEN fieldid=".$field2445->id." THEN ".$block332->id." WHEN fieldid=".$field2456->id." THEN ".$block332->id." END WHERE fieldid IN (".$field2435->id.",".$field2450->id.",".$field2438->id.",".$field2440->id.",".$field2452->id.",".$field2444->id.",".$field2446->id.",".$field2436->id.",".$field1711->id.",".$field2451->id.",".$field2441->id.",".$field2443->id.",".$field2445->id.",".$field2456->id.")");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET `sequence` = 1 WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Estimates') AND `blocklabel` = 'LBL_ESTIMATES_DATES'");

$moduleLeads = Vtiger_Module::getInstance('Leads');

$block120 = Vtiger_Block::getInstance('LBL_LEADS_DATES', $moduleLeads);
$field772 = Vtiger_Field::getInstance('pack', $moduleLeads);
$field774 = Vtiger_Field::getInstance('preferred_ppdate', $moduleLeads);
$field776 = Vtiger_Field::getInstance('load_to', $moduleLeads);
$field778 = Vtiger_Field::getInstance('deliver', $moduleLeads);
$field780 = Vtiger_Field::getInstance('preferred_pddate', $moduleLeads);
$field782 = Vtiger_Field::getInstance('decision', $moduleLeads);
$field2189 = Vtiger_Field::getInstance('flexible_on_days', $moduleLeads);
$field773 = Vtiger_Field::getInstance('pack_to', $moduleLeads);
$field775 = Vtiger_Field::getInstance('load_from', $moduleLeads);
$field777 = Vtiger_Field::getInstance('preferred_pldate', $moduleLeads);
$field779 = Vtiger_Field::getInstance('deliver_to', $moduleLeads);
$field781 = Vtiger_Field::getInstance('follow_up', $moduleLeads);
$field2083 = Vtiger_Field::getInstance('days_to_move', $moduleLeads);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field772->id." THEN 1 WHEN fieldid=".$field774->id." THEN 3 WHEN fieldid=".$field776->id." THEN 5 WHEN fieldid=".$field778->id." THEN 7 WHEN fieldid=".$field780->id." THEN 9 WHEN fieldid=".$field782->id." THEN 11 WHEN fieldid=".$field2189->id." THEN 13 WHEN fieldid=".$field773->id." THEN 2 WHEN fieldid=".$field775->id." THEN 4 WHEN fieldid=".$field777->id." THEN 6 WHEN fieldid=".$field779->id." THEN 8 WHEN fieldid=".$field781->id." THEN 10 WHEN fieldid=".$field2083->id." THEN 12 END, block=CASE WHEN fieldid=".$field772->id." THEN ".$block120->id." WHEN fieldid=".$field774->id." THEN ".$block120->id." WHEN fieldid=".$field776->id." THEN ".$block120->id." WHEN fieldid=".$field778->id." THEN ".$block120->id." WHEN fieldid=".$field780->id." THEN ".$block120->id." WHEN fieldid=".$field782->id." THEN ".$block120->id." WHEN fieldid=".$field2189->id." THEN ".$block120->id." WHEN fieldid=".$field773->id." THEN ".$block120->id." WHEN fieldid=".$field775->id." THEN ".$block120->id." WHEN fieldid=".$field777->id." THEN ".$block120->id." WHEN fieldid=".$field779->id." THEN ".$block120->id." WHEN fieldid=".$field781->id." THEN ".$block120->id." WHEN fieldid=".$field2083->id." THEN ".$block120->id." END WHERE fieldid IN (".$field772->id.",".$field774->id.",".$field776->id.",".$field778->id.",".$field780->id.",".$field782->id.",".$field2189->id.",".$field773->id.",".$field775->id.",".$field777->id.",".$field779->id.",".$field781->id.",".$field2083->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";