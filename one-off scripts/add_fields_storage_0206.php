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


// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Storage'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_STORAGE_SITDETAILS';
$module->addBlock($block1);


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_STORAGE_SITAUTHORIZATION';
$field1->name = 'storage_authorization';
$field1->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'storage_authorization';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'INT(100)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_STORAGE_ADAYS';
$field3->name = 'storage_adays';
$field3->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'storage_adays';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'INT(25)';
$field3->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);
 

$field6 = new Vtiger_Field();
$field6->label = 'LBL_STORAGE_CPSDATE';
$field6->name = 'storage_cpsdate';
$field6->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'storage_cpsdate';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);


$block1->save($module);


$module = Vtiger_Module::getInstance('Storage'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_STORAGE_AUTHORIZATION';
$module->addBlock($block2);
