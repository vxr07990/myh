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

$moduleAgents = Vtiger_Module::getInstance('Agents');

$block150 = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $moduleAgents);
$block151 = Vtiger_Block::getInstance('LBL_AGENTS_RECORDUPDATE', $moduleAgents);
$block424 = Vtiger_Block::getInstance('LBL_AGENTS_FIELDS_TO_REMOVE', $moduleAgents);
$field956 = Vtiger_Field::getInstance('agentname', $moduleAgents);
$field2833 = Vtiger_Field::getInstance('agents_status', $moduleAgents);
$field2835 = Vtiger_Field::getInstance('agents_custnum', $moduleAgents);
$field945 = Vtiger_Field::getInstance('agent_address1', $moduleAgents);
$field947 = Vtiger_Field::getInstance('agent_city', $moduleAgents);
$field949 = Vtiger_Field::getInstance('agent_zip', $moduleAgents);
$field951 = Vtiger_Field::getInstance('agent_phone', $moduleAgents);
$field953 = Vtiger_Field::getInstance('agent_email', $moduleAgents);
$field954 = Vtiger_Field::getInstance('agent_puc', $moduleAgents);
$field1942 = Vtiger_Field::getInstance('agentmanager_id', $moduleAgents);
$field944 = Vtiger_Field::getInstance('agent_number', $moduleAgents);
$field2834 = Vtiger_Field::getInstance('agents_grade', $moduleAgents);
$field2836 = Vtiger_Field::getInstance('agents_vendornum', $moduleAgents);
$field946 = Vtiger_Field::getInstance('agent_address2', $moduleAgents);
$field948 = Vtiger_Field::getInstance('agent_state', $moduleAgents);
$field950 = Vtiger_Field::getInstance('agent_country', $moduleAgents);
$field952 = Vtiger_Field::getInstance('agent_fax', $moduleAgents);
$field2837 = Vtiger_Field::getInstance('agents_website', $moduleAgents);
$field2838 = Vtiger_Field::getInstance('agents_servradius', $moduleAgents);
$field955 = Vtiger_Field::getInstance('agent_vanline', $moduleAgents);
$field957 = Vtiger_Field::getInstance('createdtime', $moduleAgents);
$field2856 = Vtiger_Field::getInstance('createdby', $moduleAgents);
$field958 = Vtiger_Field::getInstance('modifiedtime', $moduleAgents);
$field942 = Vtiger_Field::getInstance('assigned_user_id', $moduleAgents);
$field1956 = Vtiger_Field::getInstance('agent_agentmanagerid', $moduleAgents);
$field943 = Vtiger_Field::getInstance('agent_contacts', $moduleAgents);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field956->id." THEN 1 WHEN fieldid=".$field2833->id." THEN 3 WHEN fieldid=".$field2835->id." THEN 5 WHEN fieldid=".$field945->id." THEN 7 WHEN fieldid=".$field947->id." THEN 9 WHEN fieldid=".$field949->id." THEN 11 WHEN fieldid=".$field951->id." THEN 13 WHEN fieldid=".$field953->id." THEN 15 WHEN fieldid=".$field954->id." THEN 17 WHEN fieldid=".$field1942->id." THEN 19 WHEN fieldid=".$field944->id." THEN 2 WHEN fieldid=".$field2834->id." THEN 4 WHEN fieldid=".$field2836->id." THEN 6 WHEN fieldid=".$field946->id." THEN 8 WHEN fieldid=".$field948->id." THEN 10 WHEN fieldid=".$field950->id." THEN 12 WHEN fieldid=".$field952->id." THEN 14 WHEN fieldid=".$field2837->id." THEN 16 WHEN fieldid=".$field2838->id." THEN 18 WHEN fieldid=".$field955->id." THEN 20 WHEN fieldid=".$field957->id." THEN 1 WHEN fieldid=".$field2856->id." THEN 3 WHEN fieldid=".$field958->id." THEN 2 WHEN fieldid=".$field942->id." THEN 4 WHEN fieldid=".$field1956->id." THEN 1 WHEN fieldid=".$field943->id." THEN 2 END, block=CASE WHEN fieldid=".$field956->id." THEN ".$block150->id." WHEN fieldid=".$field2833->id." THEN ".$block150->id." WHEN fieldid=".$field2835->id." THEN ".$block150->id." WHEN fieldid=".$field945->id." THEN ".$block150->id." WHEN fieldid=".$field947->id." THEN ".$block150->id." WHEN fieldid=".$field949->id." THEN ".$block150->id." WHEN fieldid=".$field951->id." THEN ".$block150->id." WHEN fieldid=".$field953->id." THEN ".$block150->id." WHEN fieldid=".$field954->id." THEN ".$block150->id." WHEN fieldid=".$field1942->id." THEN ".$block150->id." WHEN fieldid=".$field944->id." THEN ".$block150->id." WHEN fieldid=".$field2834->id." THEN ".$block150->id." WHEN fieldid=".$field2836->id." THEN ".$block150->id." WHEN fieldid=".$field946->id." THEN ".$block150->id." WHEN fieldid=".$field948->id." THEN ".$block150->id." WHEN fieldid=".$field950->id." THEN ".$block150->id." WHEN fieldid=".$field952->id." THEN ".$block150->id." WHEN fieldid=".$field2837->id." THEN ".$block150->id." WHEN fieldid=".$field2838->id." THEN ".$block150->id." WHEN fieldid=".$field955->id." THEN ".$block150->id." WHEN fieldid=".$field957->id." THEN ".$block151->id." WHEN fieldid=".$field2856->id." THEN ".$block151->id." WHEN fieldid=".$field958->id." THEN ".$block151->id." WHEN fieldid=".$field942->id." THEN ".$block151->id." WHEN fieldid=".$field1956->id." THEN ".$block424->id." WHEN fieldid=".$field943->id." THEN ".$block424->id." END WHERE fieldid IN (".$field956->id.",".$field2833->id.",".$field2835->id.",".$field945->id.",".$field947->id.",".$field949->id.",".$field951->id.",".$field953->id.",".$field954->id.",".$field1942->id.",".$field944->id.",".$field2834->id.",".$field2836->id.",".$field946->id.",".$field948->id.",".$field950->id.",".$field952->id.",".$field2837->id.",".$field2838->id.",".$field955->id.",".$field957->id.",".$field2856->id.",".$field958->id.",".$field942->id.",".$field1956->id.",".$field943->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";