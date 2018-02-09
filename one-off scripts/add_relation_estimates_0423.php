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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$moduleInstance = Vtiger_Module::getInstance('Estimates');

$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', array('add'), 'get_activities');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('add', 'select'), 'get_attachments');
