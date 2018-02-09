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


/*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Add Summary Fields to Agent Manager</h1><br>\n";
$AgentManager = Vtiger_Module::getInstance('AgentManager');
$field1 = Vtiger_Field::getInstance('agency_code', $AgentManager);
$field2 = Vtiger_Field::getInstance('agency_name', $AgentManager);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield = '1' WHERE fieldid = ".$field1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield = '1' WHERE fieldid = ".$field2->id);

echo "<br><h1>Finished Hotfix Add Summary Fields to Agent Manager</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";