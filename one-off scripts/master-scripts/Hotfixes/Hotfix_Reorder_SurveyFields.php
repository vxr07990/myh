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

$moduleSurveys = Vtiger_Module::getInstance('Surveys');

$block208 = Vtiger_Block::getInstance('LBL_SURVEYS_INFORMATION', $moduleSurveys);
$field1241 = Vtiger_Field::getInstance('survey_date', $moduleSurveys);
$field1243 = Vtiger_Field::getInstance('survey_status', $moduleSurveys);
$field1249 = Vtiger_Field::getInstance('survey_time', $moduleSurveys);
$field1252 = Vtiger_Field::getInstance('address1', $moduleSurveys);
$field1254 = Vtiger_Field::getInstance('city', $moduleSurveys);
$field1256 = Vtiger_Field::getInstance('zip', $moduleSurveys);
$field1258 = Vtiger_Field::getInstance('phone1', $moduleSurveys);
$field1260 = Vtiger_Field::getInstance('address_desc', $moduleSurveys);
$field1245 = Vtiger_Field::getInstance('contact_id', $moduleSurveys);
$field1244 = Vtiger_Field::getInstance('account_id', $moduleSurveys);
$field1242 = Vtiger_Field::getInstance('assigned_user_id', $moduleSurveys);
$field1261 = Vtiger_Field::getInstance('comm_res', $moduleSurveys);
$field1262 = Vtiger_Field::getInstance('survey_end_time', $moduleSurveys);
$field1253 = Vtiger_Field::getInstance('address2', $moduleSurveys);
$field1255 = Vtiger_Field::getInstance('state', $moduleSurveys);
$field1257 = Vtiger_Field::getInstance('country', $moduleSurveys);
$field1259 = Vtiger_Field::getInstance('phone2', $moduleSurveys);
$field1246 = Vtiger_Field::getInstance('potential_id', $moduleSurveys);
$field1251 = Vtiger_Field::getInstance('order_id', $moduleSurveys);
$field1263 = Vtiger_Field::getInstance('survey_notes', $moduleSurveys);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1241->id." THEN 1 WHEN fieldid=".$field1243->id." THEN 3 WHEN fieldid=".$field1249->id." THEN 5 WHEN fieldid=".$field1252->id." THEN 7 WHEN fieldid=".$field1254->id." THEN 9 WHEN fieldid=".$field1256->id." THEN 11 WHEN fieldid=".$field1258->id." THEN 13 WHEN fieldid=".$field1260->id." THEN 15 WHEN fieldid=".$field1245->id." THEN 17 WHEN fieldid=".$field1244->id." THEN 19 WHEN fieldid=".$field1242->id." THEN 2 WHEN fieldid=".$field1261->id." THEN 4 WHEN fieldid=".$field1262->id." THEN 6 WHEN fieldid=".$field1253->id." THEN 8 WHEN fieldid=".$field1255->id." THEN 10 WHEN fieldid=".$field1257->id." THEN 12 WHEN fieldid=".$field1259->id." THEN 14 WHEN fieldid=".$field1246->id." THEN 16 WHEN fieldid=".$field1251->id." THEN 18 WHEN fieldid=".$field1263->id." THEN 20 END, block=CASE WHEN fieldid=".$field1241->id." THEN ".$block208->id." WHEN fieldid=".$field1243->id." THEN ".$block208->id." WHEN fieldid=".$field1249->id." THEN ".$block208->id." WHEN fieldid=".$field1252->id." THEN ".$block208->id." WHEN fieldid=".$field1254->id." THEN ".$block208->id." WHEN fieldid=".$field1256->id." THEN ".$block208->id." WHEN fieldid=".$field1258->id." THEN ".$block208->id." WHEN fieldid=".$field1260->id." THEN ".$block208->id." WHEN fieldid=".$field1245->id." THEN ".$block208->id." WHEN fieldid=".$field1244->id." THEN ".$block208->id." WHEN fieldid=".$field1242->id." THEN ".$block208->id." WHEN fieldid=".$field1261->id." THEN ".$block208->id." WHEN fieldid=".$field1262->id." THEN ".$block208->id." WHEN fieldid=".$field1253->id." THEN ".$block208->id." WHEN fieldid=".$field1255->id." THEN ".$block208->id." WHEN fieldid=".$field1257->id." THEN ".$block208->id." WHEN fieldid=".$field1259->id." THEN ".$block208->id." WHEN fieldid=".$field1246->id." THEN ".$block208->id." WHEN fieldid=".$field1251->id." THEN ".$block208->id." WHEN fieldid=".$field1263->id." THEN ".$block208->id." END WHERE fieldid IN (".$field1241->id.",".$field1243->id.",".$field1249->id.",".$field1252->id.",".$field1254->id.",".$field1256->id.",".$field1258->id.",".$field1260->id.",".$field1245->id.",".$field1244->id.",".$field1242->id.",".$field1261->id.",".$field1262->id.",".$field1253->id.",".$field1255->id.",".$field1257->id.",".$field1259->id.",".$field1246->id.",".$field1251->id.",".$field1263->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";