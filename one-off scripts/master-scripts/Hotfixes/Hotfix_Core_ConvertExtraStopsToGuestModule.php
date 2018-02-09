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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/30/2016
 * Time: 2:41 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$db = PearDatabase::getInstance();

$moduleNames = ['Opportunities','Orders','Estimates','Actuals'];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $module->setGuestBlocks('ExtraStops', ['LBL_EXTRASTOPS_INFORMATION']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";