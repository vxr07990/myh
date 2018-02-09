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


//start adding
$field2 = new Vtiger_Field();
$field2->label = 'LBL_STOPS_ADDRESS1';
$field2->name = 'stop_address1';
$field2->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'stop_address1';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);


$field3 = new Vtiger_Field();
$field3->label = 'LBL_STOPS_ADDRESS2';
$field3->name = 'stop_address2';
$field3->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'stop_address2';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
 

$field4 = new Vtiger_Field();
$field4->label = 'LBL_STOPS_CITY';
$field4->name = 'stop_city';
$field4->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'stop_city';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(100)';
$field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);


$field5 = new Vtiger_Field();
$field5->label = 'LBL_STOPS_ZIP';
$field5->name = 'stop_zip';
$field5->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'stop_zip';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'INT(20)';
$field5->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

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

$field8 = new Vtiger_Field();
$field8->label = 'LBL_STOPS_PHONE1';
$field8->name = 'stop_phone1';
$field8->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'stop_phone1';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(20)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_STOPS_PHONE2';
$field9->name = 'stop_phone2';
$field9->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'stop_phone2';   //  This will be the columnname in your database for the new field.
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


$field12 = new Vtiger_Field();
$field12->label = 'LBL_STOPS_CONTACT';
$field12->name = 'stop_contact';
$field12->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'stop_contact';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(100)';
$field12->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field12);
$field12->setRelatedModules(array('Contacts'));

$field13 = new Vtiger_Field();
$field13->label = 'LBL_STOPS_STATE';
$field13->name = 'stop_state';
$field13->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'stop_state';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'VARCHAR(2)';
$field13->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_STOPS_PROJECT';
$field14->name = 'stop_project';
$field14->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'stop_project';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'VARCHAR(100)';
$field14->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field14);
$field14->setRelatedModules(array('Project'));

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


$block1->save($module);
// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Stops'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_STOPS_RECORDUPDATE';
$module->addBlock($block2);

$block2->save($module);


//START Add navigation link in module
$module = Vtiger_Module::getInstance('Project');
$module->setRelatedList(Vtiger_Module::getInstance('Stops'), 'Stops', array('ADD', 'SELECT'), 'get_dependents_list');
//END Add navigation link in module
;
