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
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Potentials');
$module->setRelatedList(Vtiger_Module::getInstance('Project'), 'Project', array('ADD'), 'get_dependents_list');
