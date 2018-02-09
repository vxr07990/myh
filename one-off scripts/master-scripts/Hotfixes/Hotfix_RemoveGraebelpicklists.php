<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br><h1>Starting Hotfix Remove Graebel picklist options from Core</h1><br>\n";

Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_sponsor_type` WHERE sponsor_type LIKE '%Graebel%'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_titleowner_type` WHERE titleowner_type LIKE '%Graebel%'");

echo "<br><h1>Finished Hotfix Removing Grabel Picklist Options from Core</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";