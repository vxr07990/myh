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
    echo "Module was not found and could not be deleted!";
}
