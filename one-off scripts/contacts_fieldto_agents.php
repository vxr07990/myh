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
$module = Vtiger_Module::getInstance('Contacts'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTACT_AGENTS';
$field1->name = 'agents';                                // Must be the same as column.
$field1->table = 'vtiger_contactdetails';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'agents';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 10;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
// Use only if this field is being added to relate to another module.
$field1->setRelatedModules(array('Agents'));            // Make sure to change to the name of the module your blocks and fields will be in.

$block1->save($module);
// END Add new field
;
