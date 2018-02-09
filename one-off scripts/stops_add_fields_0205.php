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
 $module = Vtiger_Module::getInstance('Stops'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_STOPS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.



// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_STOPS_TYPE';
$field1->name = 'stop_type';
$field1->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'stop_type';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
$field1->setPicklistValues(array('Destination', 'Extra Delivery', 'Extra Pickup', 'Origin'));


$field3 = new Vtiger_Field();
$field3->label = 'LBL_STOPS_ADDRESS2';
$field3->name = 'stop_address2';
$field3->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'stop_address2';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
 

$field6 = new Vtiger_Field();
$field6->label = 'LBL_STOPS_COUNTRY';
$field6->name = 'stop_country';
$field6->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'stop_country';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(100)';
$field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_STOPS_DESCRIPTION';
$field7->name = 'stop_description';
$field7->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'stop_description';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'VARCHAR(100)';
$field7->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);
$field7->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));


$field9 = new Vtiger_Field();
$field9->label = 'LBL_STOPS_P2';
$field9->name = 'stop_p2';
$field9->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'stop_p2';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(20)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_STOPS_PTYPE1';
$field10->name = 'stop_ptype1';
$field10->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'stop_ptype1';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(100)';
$field10->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);
$field10->setPicklistValues(array('Business', 'Home', 'Mobile', 'Work', 'Other'));

$field11 = new Vtiger_Field();
$field11->label = 'LBL_STOPS_PTYPE2';
$field11->name = 'stop_ptype2';
$field11->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'stop_ptype2';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(100)';
$field11->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field11);
$field11->setPicklistValues(array('Business', 'Home', 'Mobile', 'Work', 'Other'));


$field15 = new Vtiger_Field();
$field15->label = 'LBL_STOPS_STSEQ';
$field15->name = 'stop_sequence';
$field15->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field15->column = 'stop_sequence';   //  This will be the columnname in your database for the new field.
$field15->columntype = 'VARCHAR(100)';
$field15->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field15);
$field15->setPicklistValues(array('1', '2', '3', '4', '5', '6', '7', '8', '9'));

$field16 = new Vtiger_Field();
$field16->label = 'LBL_STOPS_DATEFROM';
$field16->name = 'stop_datefrom';
$field16->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'stop_datefrom';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'DATE';
$field16->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field16);

$field17 = new Vtiger_Field();
$field17->label = 'LBL_STOPS_DATETO';
$field17->name = 'stop_dateto';
$field17->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'stop_dateto';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'DATE';
$field17->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_STOPS_WEIGHT';
$field18->name = 'stop_weight';
$field18->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'stop_weight';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'INT(20)';
$field18->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field18);


$block1->save($module);
// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Stops'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_STOPS_RECORDUPDATE';
$module->addBlock($block2);

$block2->save($module);
