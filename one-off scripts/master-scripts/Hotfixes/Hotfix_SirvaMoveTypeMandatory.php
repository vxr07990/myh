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

echo "<br>Set all move type fields to mandatory<br><br>";

$oppsModule = Vtiger_Module::getInstance('Opportunities');
$leadsModule = Vtiger_Module::getInstance('Leads');

$oppsInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $oppsModule);
if (!$oppsInfo) {
    $oppsInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
}

$leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);

Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET typeofdata = "V~M" WHERE fieldname = "move_type"');

echo "<br>Mandatoryfication complete<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";