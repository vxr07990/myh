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



// Make sure to give your file a descriptive name and place in the root of your installation.  Then access the appropriate URL in a browser.

// Turn on debugging level
$Vtiger_Utils_Log = true;
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


// To use a pre-existing block
 $module = Vtiger_Module::getInstance('TimeOff'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_TIMEOFF_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

$field9 = new Vtiger_Field();
$field9->label = 'LBL_TIMEOFF_EMPLOYEES';
$field9->name = 'timeoff_employees';
$field9->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'timeoff_employees';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'VARCHAR(100)';
$field9->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);
$field9->setRelatedModules(array('Employees'));

//START Add navigation link in module
$module = Vtiger_Module::getInstance('Employees');
$module->setRelatedList(Vtiger_Module::getInstance('TimeOff'), 'Time Off', array('ADD', 'SELECT'), 'get_related_list');
//END Add navigation link in module
;
