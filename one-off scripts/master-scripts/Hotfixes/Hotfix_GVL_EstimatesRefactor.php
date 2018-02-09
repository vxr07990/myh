<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/14/2016
 * Time: 12:26 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();
$moduleNames = ['Estimates', 'Actuals'];

foreach($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";