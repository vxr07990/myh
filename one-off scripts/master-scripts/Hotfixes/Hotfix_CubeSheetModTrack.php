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


//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');

echo "<br> Begin Hotfix. Adding modTracker for Cubesheets";

$cubeInst = Vtiger_Module::getInstance('Cubesheets');
if ($cubeInst) {
    ModTracker::enableTrackingForModule($cubeInst->id);
} else {
    echo "<br><h1 style='color:orange'>WARNING: module 'Cubesheets' does not exist.</h1><br>";
}

echo "<br> hotFix complete, enabled modTracker for cubesheets";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";