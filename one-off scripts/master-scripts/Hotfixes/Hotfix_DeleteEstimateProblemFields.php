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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br><h1>Begin Hotfix to Delete Estimate Problem Fields</h1><br>";

$db = PearDatabase::getInstance();

$sql = 'DELETE FROM `vtiger_field` WHERE columnname = "estimate_type" AND tablename = "vtiger_potentials"';
$result = $db->query($sql);

$sql = 'DELETE FROM `vtiger_field` WHERE columnname = "quotation_type" AND tablename = "vtiger_potentials"';
$result = $db->query($sql);

echo "<br><h1>End Hotfix to Delete Estimate Problem Fields</h1><br;>";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";