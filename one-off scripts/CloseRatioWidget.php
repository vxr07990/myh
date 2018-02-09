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



include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Link.php');

$moduleInstance = Vtiger_Module::getInstance('Home');
$moduleInstance->addLink('DASHBOARDWIDGET', 'Opportunity Win/Loss Ratios', 'index.php?module=Potentials&view=ShowWidget&name=CloseRatio');
