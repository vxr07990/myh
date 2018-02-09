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
 $module = Vtiger_Module::getInstance('Vehicles'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_VEHICLES_VNUMBER';
$field1->name = 'vehicle_number';
$field1->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'vehicle_number';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'INT(50)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

//start adding
$field2 = new Vtiger_Field();
$field2->label = 'LBL_VEHICLES_TYPE';
$field2->name = 'vehicle_type';
$field2->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'vehicle_type';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);
$field2->setPicklistValues(array('Straight Truck', 'Pack Van', 'Tractor', '48 Trailer', '53 Trailer', 'Vault Trailer', 'Flatbed Trailer'));

$field3 = new Vtiger_Field();
$field3->label = 'LBL_VEHICLES_STATUS';
$field3->name = 'vehicle_status';
$field3->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'vehicle_status';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);
$field3->setPicklistValues(array('Active', 'Disposed', 'Out of Service'));


$field4 = new Vtiger_Field();
$field4->label = 'LBL_VEHICLES_MILESNUM';
$field4->name = 'vehicle_milesnum';
$field4->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'vehicle_milesnum';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'INT(20)';
$field4->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_VEHICLES_MILESDATE';
$field5->name = 'vehicle_milesdate';
$field5->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'vehicle_milesdate';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$block1->save($module);

// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Vehicles'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_VEHICLES_SPECS';
$module->addBlock($block2);


$field6 = new Vtiger_Field();
$field6->label = 'LBL_VEHICLES_VIN';
$field6->name = 'vehicle_vin';
$field6->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'vehicle_vin';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(100)';
$field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_VEHICLES_CUBECAPACITY';
$field7->name = 'vehicle_cubec';
$field7->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'vehicle_cubec';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'INT(20)';
$field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field7);


$field8 = new Vtiger_Field();
$field8->label = 'LBL_VEHICLES_WEIGHT';
$field8->name = 'vehicle_weight';
$field8->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'vehicle_weight';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(20)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_VEHICLES_LENGTH';
$field9->name = 'vehicle_length';
$field9->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'vehicle_length';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(20)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_VEHICLES_HEIGHT';
$field10->name = 'vehicle_height';
$field10->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'vehicle_height';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'INT(20)';
$field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_VEHICLES_YEAR';
$field11->name = 'vehicle_year';
$field11->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'vehicle_year';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(20)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_VEHICLES_MODEL';
$field12->name = 'vehicle_model';
$field12->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'vehicle_model';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(100)';
$field12->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field12);

$field13 = new Vtiger_Field();
$field13->label = 'LBL_VEHICLES_ADATE';
$field13->name = 'vehicle_adate';
$field13->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'vehicle_adate';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'DATE';
$field13->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_VEHICLES_DDATE';
$field14->name = 'vehicle_ddate';
$field14->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'vehicle_ddate';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'DATE';
$field14->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field14);

$block2->save($module);

// Or to create a new block
$module = Vtiger_Module::getInstance('Vehicles'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_VEHICLES_RECORDUPDATE';
$module->addBlock($block3);

/*
//START Add navigation link in module
$module = Vtiger_Module::getInstance('Contractors');
$module->setRelatedList(Vtiger_Module::getInstance('Safety'), 'Safety',Array('ADD','SELECT'),'get_dependents_list');
//END Add navigation link in module
 */;
