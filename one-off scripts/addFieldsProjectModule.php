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
 $module = Vtiger_Module::getInstance('Project'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// Or to create a new block
//$module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
//$block1 = new Vtiger_Block();
//$block1->label = 'New Block Name';
//$module->addBlock($block1);

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'Carrier ID';
$field1->name = 'carrier_id';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'carrier_id';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'INT(10)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
//$field1->setRelatedModules(Array('Potentials'));  // Make sure to change to the name of the module your blocks and fields will be in.


//start adding
$field2 = new Vtiger_Field();
$field2->label = 'Load Type';
$field2->name = 'load_type';
$field2->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'load_type';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(50)';
$field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'Move Type';
$field3->name = 'move_type';
$field3->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'move_type';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
$field3->setPicklistValues(array('Local', 'Interstate', 'Commercial'));


$field4 = new Vtiger_Field();
$field4->label = 'Authority Type';
$field4->name = 'authority_type';
$field4->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'authority_type';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(50)';
$field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'Agent Pickup';
$field5->name = 'agent_pickup';
$field5->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'agent_pickup';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'VARCHAR(50)';
$field5->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);


$field6 = new Vtiger_Field();
$field6->label = 'Miles';
$field6->name = 'miles';
$field6->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'miles';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DECIMAL(10,3)';
$field6->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);


$field7 = new Vtiger_Field();
$field7->label = 'Estimated Weight';
$field7->name = 'estimated_weight';
$field7->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'estimated_weight';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'DECIMAL(10,3)';
$field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);


$field8 = new Vtiger_Field();
$field8->label = 'Actual Weight';
$field8->name = 'actual_weight';
$field8->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'actual_weight';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'DECIMAL(10,3)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);


$field9 = new Vtiger_Field();
$field9->label = 'Available Tonnage';
$field9->name = 'available_tonnage';
$field9->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'available_tonnage';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'DECIMAL(10,3)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'On Hold';
$field10->name = 'on_hold';
$field10->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'on_hold';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(3)';
$field10->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'GBL Number';
$field11->name = 'gbl_number';
$field11->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'gbl_number';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(50)';
$field11->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field11);


$block1->save($module);
// END Add new field



//Add New Block Invoice Details
$module = Vtiger_Module::getInstance('Project'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'Invoice Details';
$module->addBlock($block2);

// START Add new field  CONTACT NAME FIELD
$field1 = new Vtiger_Field();
$field1->label = 'Bill As Weight';
$field1->name = 'billas_weight';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'billas_weight';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field1);


//start adding
$field2 = new Vtiger_Field();
$field2->label = 'Payment Type';
$field2->name = 'payment_type';
$field2->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'payment_type';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field2);
$field2->setPicklistValues(array('Check', 'Electronic Transfer', 'Credit', 'Cash'));


$field3 = new Vtiger_Field();
$field3->label = 'Pricing Mode';
$field3->name = 'pricing_mode';
$field3->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'pricing_mode';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(50)';
$field3->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'Rating Data';
$field4->name = 'rating_data';
$field4->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'rating_data';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(50)';
$field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'Register By';
$field5->name = 'register_by';
$field5->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'register_by';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'VARCHAR(50)';
$field5->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field5);


$field6 = new Vtiger_Field();
$field6->label = 'Invoice Status';
$field6->name = 'invoice_status';
$field6->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'invoice_status';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(100)';
$field6->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field6);
$field6->setPicklistValues(array('Created', 'Cancel', 'Approved', 'Sent', 'Paid'));





$block2->save($module);
