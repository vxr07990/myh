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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$orderInstance = Vtiger_Module::getInstance('Orders');
$apptInstance = Vtiger_Module::getInstance('Surveys');
$docsInstance = Vtiger_Module::getInstance('Documents');

$relationLabel = 'Survey Appointments';
$orderInstance->setRelatedList($apptInstance, $relationLabel, array('Add'));

$relationLabel = 'Documents';
$orderInstance->setRelatedList($docsInstance, $relationLabel, array('Add', 'Select'));
