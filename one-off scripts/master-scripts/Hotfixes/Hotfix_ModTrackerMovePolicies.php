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



$moduleInstance = Vtiger_Module::getInstance('MovePolicies'); // The module1 your blocks and fields will be in.

//Enable modtracker for module

if ($moduleInstance) {
    ModTracker::enableTrackingForModule($moduleInstance->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";