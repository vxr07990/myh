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
//Where you want to add the realted module.
//Source Module Name
$module = Vtiger_Module::getInstance('Agents');
if ($module) {
    // Delete from system
$module->unsetRelatedList(Vtiger_Module::getInstance('Contacts'), 'Contacts');
    echo "Module Contacts tab deleted!";
} else {
    echo "Module was not found and could not be deleted!";
}
