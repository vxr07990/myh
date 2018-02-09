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

// OT5469 - Modules in List View - "Actions" drop down button - Add Import and Export options

$modulesList = [
    'Employees' , //Personnel
    'Vehicles' , 
    'Equipment' ,
    'Carriers' , //Military Carriers
    'MilitaryBases' , 
    'Agents' , //Agent Roster
    'Vanlines', //Vanline Roster
    'Vendors', //Service Providers' 
    'ContainerTypes' , 
];
foreach ($modulesList as $moduleName) {
    // Get a handle to the module
    $module = Vtiger_Module::getInstance($moduleName);

    // Enable import/export tools
    $module->enableTools(Array('Import', 'Export'));
}
