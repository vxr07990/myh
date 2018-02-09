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
 $module = Vtiger_Module::getInstance('Estimates'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_QUOTES_ORDERS';
$field1->name = 'orders_id';
$field1->table = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'orders_id';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'INT(19)';
$field1->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
$field1->setRelatedModules(array('Orders'));

$block1->save($module);
