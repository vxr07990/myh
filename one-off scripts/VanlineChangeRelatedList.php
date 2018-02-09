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
require_once('includes/main/WebUI.php');

echo "<h1>starting script</h1>";

$vanlineInstance = Vtiger_Module::getInstance('VanlineManager');

echo "<h1>vanline instance aquired</h1>";

$usersInstance = Vtiger_Module::getInstance('Users');

echo "<h1>users instance aquired</h1>";

$relationLabel = 'Users';

echo "<h1>relation label set</h1>";

$vanlineInstance->setRelatedList($usersInstance, $relationLabel, array('Select'), 'get_users');

echo "<h1>script complete!</h1>";
