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
 $module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_POTENTIALS_DESTINATIONADDRESSDETAILS', $module);  // Must be the actual instance name, not just what appears in the browser.

 // START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIAL_ODESCRIPTION';
$field1->name = 'origin_description1';
$field1->table = 'vtiger_potentialscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_description1';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(200)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
$field1->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));

$field2 = new Vtiger_Field();
$field2->label = 'LBL_POTENTIAL_DDESCRIPTION';
$field2->name = 'destination_description';
$field2->table = 'vtiger_potentialscf';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'destination_description';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(200)';
$field2->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);
$field2->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));


$block1->save($module);

// To use a pre-existing block
 $module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
 $block2 = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field3 = new Vtiger_Field();
$field3->label = 'LBL_POTENTIAL_PPDATE';
$field3->name = 'preferred_ppdate';
$field3->table = 'vtiger_potentialscf';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'preferred_ppdate';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_POTENTIAL_PLDATE';
$field4->name = 'preferred_pldate';
$field4->table = 'vtiger_potentialscf';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'preferred_pldate';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'DATE';
$field4->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_POTENTIAL_PDDATE';
$field5->name = 'preferred_pddate';
$field5->table = 'vtiger_potentialscf';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'preferred_pddate';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field5);

$block2->save($module);
