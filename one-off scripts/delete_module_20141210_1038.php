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

/*
* Delete a module
*/
$module = Vtiger_Module::getInstance('Crews');
if ($module) {
    // Delete from system
$module->delete();
    echo "Module Crews deleted!";
} else {
    echo "Module Crews was not found and could not be deleted!";
}

/*
* Delete a module
*/
$module = Vtiger_Module::getInstance('Equiptment');
if ($module) {
    // Delete from system
$module->delete();
    echo "Module Equiptment deleted!";
} else {
    echo "Module Equiptment was not found and could not be deleted!";
}

/*
* Delete a module
*/
$module = Vtiger_Module::getInstance('Estimate');
if ($module) {
    // Delete from system
$module->delete();
    echo "Module Estimate deleted!";
} else {
    echo "Module Estimate was not found and could not be deleted!";
}

/*
* Delete a module
*/
$module = Vtiger_Module::getInstance('Survey');
if ($module) {
    // Delete from system
$module->delete();
    echo "Module Survey deleted!";
} else {
    echo "Module Survey was not found and could not be deleted!";
}

/*
* Delete a module
*/
$module = Vtiger_Module::getInstance('VehicleManager');
if ($module) {
    // Delete from system
$module->delete();
    echo "Module VehicleManager deleted!";
} else {
    echo "Module VehicleManager was not found and could not be deleted!";
}
