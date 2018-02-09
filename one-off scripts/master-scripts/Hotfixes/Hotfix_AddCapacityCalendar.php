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


$moduleName = 'CapacityCalendar';

$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance) {
    echo "Module already present - choose a different name.";
   
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $moduleName;
    $moduleInstance->parent = 'Tools';
    $moduleInstance->save();

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'OPERATIONS_TAB' WHERE name = 'CapacityCalendar'");
    echo "OK\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";