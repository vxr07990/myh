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
 $module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.



// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTRACTORS_LNAME';
$field1->name = 'contractor_lname';
$field1->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'contractor_lname';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_CONTRACTORS_ADDRESS1';
$field2->name = 'contractor_address1';
$field2->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'contractor_address1';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);


$field3 = new Vtiger_Field();
$field3->label = 'LBL_CONTRACTORS_ADDRESS2';
$field3->name = 'contractor_address2';
$field3->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'contractor_address2';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
 

$field4 = new Vtiger_Field();
$field4->label = 'LBL_CONTRACTORS_CITY';
$field4->name = 'contractor_city';
$field4->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'contractor_city';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(100)';
$field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_CONTRACTORS_STATE';
$field24->name = 'contractor_state';
$field24->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field24->column = 'contractor_state';   //  This will be the columnname in your database for the new field.
$field24->columntype = 'VARCHAR(2)';
$field24->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field24);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_CONTRACTORS_ZIP';
$field5->name = 'contractor_zip';
$field5->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'contractor_zip';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'INT(20)';
$field5->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_CONTRACTORS_COUNTRY';
$field6->name = 'contractor_country';
$field6->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'contractor_country';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(100)';
$field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_CONTRACTORS_EMAIL';
$field7->name = 'contractor_email';
$field7->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'contractor_email';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'VARCHAR(100)';
$field7->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_CONTRACTORS_P1';
$field8->name = 'contractor_p1';
$field8->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'contractor_p1';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(20)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_CONTRACTORS_P2';
$field9->name = 'contractor_p2';
$field9->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'contractor_p2';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(20)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_CONTRACTORS_BDATE';
$field10->name = 'contractor_bdate';
$field10->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'contractor_bdate';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'DATE';
$field10->uitype =5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);

$block1->save($module);


// Or to create a new block
$module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_CONTRACTORS_DETAILINFO';
$module->addBlock($block2);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_CONTRACTORS_ENUM';
$field11->name = 'contractor_enum';
$field11->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'contractor_enum';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(50)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_CONTRACTORS_PROLE';
$field12->name = 'contractor_prole';
$field12->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'contractor_prole';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(100)';
$field12->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field12);
$field12->setPicklistValues(array('Driver', 'Packer', 'Warehouse'));

$field13 = new Vtiger_Field();
$field13->label = 'LBL_CONTRACTORS_HDATE';
$field13->name = 'contractor_hdate';
$field13->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'contractor_hdate';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'DATE';
$field13->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_CONTRACTORS_TDATE';
$field14->name = 'contractor_tdate';
$field14->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'contractor_tdate';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'DATE';
$field14->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field14);
//$field14->setRelatedModules(Array('Project'));

$field15 = new Vtiger_Field();
$field15->label = 'LBL_CONTRACTORS_STATUS';
$field15->name = 'contractor_status';
$field15->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field15->column = 'contractor_status';   //  This will be the columnname in your database for the new field.
$field15->columntype = 'VARCHAR(100)';
$field15->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field15);
$field15->setPicklistValues(array('Active', 'Terminated', 'Suspended'));

$field16 = new Vtiger_Field();
$field16->label = 'LBL_CONTRACTORS_RDATE';
$field16->name = 'contractor_rdate';
$field16->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'contractor_rdate';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'DATE';
$field16->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field16);

$field17 = new Vtiger_Field();
$field17->label = 'LBL_CONTRACTORS_CEDATE';
$field17->name = 'contractor_cedate';
$field17->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'contractor_cedate';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'DATE';
$field17->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field17);

$block2->save($module);


// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_CONTRACTORS_ECINFO';
$module->addBlock($block3);

// START Add new field
$field18 = new Vtiger_Field();
$field18->label = 'LBL_CONTRACTORS_EFNAME';
$field18->name = 'contractor_efname';
$field18->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'contractor_efname';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(100)';
$field18->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_CONTRACTORS_ELNAME';
$field19->name = 'contractor_elname';
$field19->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field19->column = 'contractor_elname';   //  This will be the columnname in your database for the new field.
$field19->columntype = 'VARCHAR(100)';
$field19->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_CONTRACTORS_RELATION';
$field20->name = 'contractor_relation';
$field20->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field20->column = 'contractor_relation';   //  This will be the columnname in your database for the new field.
$field20->columntype = 'VARCHAR(100)';
$field20->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field20);
$field20->setPicklistValues(array('Family', 'Friend', 'Spouse'));

$field21 = new Vtiger_Field();
$field21->label = 'LBL_CONTRACTORS_EEMAIL';
$field21->name = 'contractor_eemail';
$field21->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field21->column = 'contractor_eemail';   //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(100)';
$field21->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_CONTRACTORS_EP1';
$field22->name = 'contractor_ep1';
$field22->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field22->column = 'contractor_ep1';   //  This will be the columnname in your database for the new field.
$field22->columntype = 'INT(20)';
$field22->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_CONTRACTORS_EP2';
$field23->name = 'contractor_ep2';
$field23->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field23->column = 'contractor_ep2';   //  This will be the columnname in your database for the new field.
$field23->columntype = 'INT(20)';
$field23->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field23->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field23);


// Or to create a new block
$module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_CONTRACTORS_RECORDUPDATE';
$module->addBlock($block4);

/*
//START Add navigation link in module
$module = Vtiger_Module::getInstance('Project');
$module->setRelatedList(Vtiger_Module::getInstance('Stops'), 'Stops',Array('ADD','SELECT'),'get_dependents_list');
//END Add navigation link in module

 */;
