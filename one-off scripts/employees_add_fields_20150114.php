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
$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_EMPLOYEES_LASTNAME';
$field1->name = 'employee_lastname';                                // Must be the same as column.
$field1->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'employee_lastname';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_EMPLOYEES_EMAIL';
$field2->name = 'employee_email';                                // Must be the same as column.
$field2->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field2->column = 'employee_email';                            //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(50)';
$field2->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_EMPLOYEES_ADDRESS1';
$field3->name = 'employee_address1';                                // Must be the same as column.
$field3->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field3->column = 'employee_address1';                            //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(50)';
$field3->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);
// Use only if this field is being added to relate to another module.
//$field1->setRelatedModules(Array('Potentials'));  			// Make sure to change to the name of the module your blocks and fields will be in.
$field4 = new Vtiger_Field();
$field4->label = 'LBL_EMPLOYEES_CITY';
$field4->name = 'employee_city';                                // Must be the same as column.
$field4->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field4->column = 'employee_city';                            //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(50)';
$field4->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_EMPLOYEES_STATE';
$field5->name = 'employee_state';                                // Must be the same as column.
$field5->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field5->column = 'employee_state';                            //  This will be the columnname in your database for the new field.
$field5->columntype = 'VARCHAR(50)';
$field5->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_EMPLOYEES_ZIP';
$field6->name = 'employee_zip';                                // Must be the same as column.
$field6->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field6->column = 'employee_zip';                            //  This will be the columnname in your database for the new field.
$field6->columntype = 'INT(10)';
$field6->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_EMPLOYEES_COUNTRY';
$field7->name = 'employee_country';                                // Must be the same as column.
$field7->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field7->column = 'employee_country';                            //  This will be the columnname in your database for the new field.
$field7->columntype = 'VARCHAR(50)';
$field7->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_EMPLOYEES_MPHONE';
$field8->name = 'employee_mphone';                                // Must be the same as column.
$field8->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field8->column = 'employee_mphone';                            //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(20)';
$field8->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_EMPLOYEES_HPHONE';
$field9->name = 'employee_hphone';                                // Must be the same as column.
$field9->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field9->column = 'employee_hphone';                            //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(20)';
$field9->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_EMPLOYEES_STATUS';
$field10->name = 'employee_status';                                // Must be the same as column.
$field10->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field10->column = 'employee_status';                            //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(100)';
$field10->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);
$field10->setPicklistValues(array('Active', 'Suspended', 'Terminated'));

$block1->save($module);
// END Add new field

//New Module
// Or to create a new block
$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_EMPLOYEES_DETAILINFO';
$module->addBlock($block2);

//Adding fields to Block2

$field11 = new Vtiger_Field();
$field11->label = 'LBL_EMPLOYEES_PROLE';
$field11->name = 'employee_prole';                                // Must be the same as column.
$field11->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field11->column = 'employee_prole';                            //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(100)';
$field11->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field11);
$field11->setPicklistValues(array('Driver', 'Helper', 'Office', 'Packer', 'Sales', 'Warehouse'));


$field12 = new Vtiger_Field();
$field12->label = 'LBL_EMPLOYEES_SROLE';
$field12->name = 'employee_srole';                                // Must be the same as column.
$field12->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field12->column = 'employee_srole';                            //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(100)';
$field12->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field12);
$field12->setPicklistValues(array('Driver', 'Helper', 'Office', 'Packer', 'Sales', 'Warehouse'));


$field13 = new Vtiger_Field();
$field13->label = 'LBL_EMPLOYEES_HDATE';
$field13->name = 'employee_hdate';                                // Must be the same as column.
$field13->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field13->column = 'employee_hdate';                            //  This will be the columnname in your database for the new field.
$field13->columntype = 'DATE';
$field13->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field13);


$field14 = new Vtiger_Field();
$field14->label = 'LBL_EMPLOYEES_TDATE';
$field14->name = 'employee_tdate';                                // Must be the same as column.
$field14->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field14->column = 'employee_tdate';                            //  This will be the columnname in your database for the new field.
$field14->columntype = 'DATE';
$field14->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field14);

$field15 = new Vtiger_Field();
$field15->label = 'LBL_EMPLOYEES_RDATE';
$field15->name = 'employee_rdate';                                // Must be the same as column.
$field15->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field15->column = 'employee_rdate';                            //  This will be the columnname in your database for the new field.
$field15->columntype = 'DATE';
$field15->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field15);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_EMPLOYEES_BDATE';
$field16->name = 'employee_bdate';                                // Must be the same as column.
$field16->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field16->column = 'employee_bdate';                            //  This will be the columnname in your database for the new field.
$field16->columntype = 'DATE';
$field16->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field16);

$block2->save($module);
// END Add new block

//New Module
// Or to create a new block
$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_EMPLOYEES_LICENSEINFO';
$module->addBlock($block3);

//Adding fields to Block3

$field17 = new Vtiger_Field();
$field17->label = 'LBL_EMPLOYEES_DLNUMBER';
$field17->name = 'employee_dlnumber';                                // Must be the same as column.
$field17->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field17->column = 'employee_dlnumber';                            //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(50)';
$field17->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_EMPLOYEES_DLSTATE';
$field18->name = 'employee_dlstate';                                // Must be the same as column.
$field18->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field18->column = 'employee_dlstate';                            //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(50)';
$field18->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_EMPLOYEES_DLEXPY';
$field19->name = 'employee_dlexpy';                                // Must be the same as column.
$field19->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field19->column = 'employee_dlexpy';                            //  This will be the columnname in your database for the new field.
$field19->columntype = 'DATE';
$field19->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field19);


$field20 = new Vtiger_Field();
$field20->label = 'LBL_EMPLOYEES_DLCLASS';
$field20->name = 'employee_dlclass';                                // Must be the same as column.
$field20->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field20->column = 'employee_dlclass';                            //  This will be the columnname in your database for the new field.
$field20->columntype = 'VARCHAR(20)';
$field20->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field20);

$block3->save($module);
// END Add new block


//New Module
// Or to create a new block 4
$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_EMPLOYEES_EMERGINFO';
$module->addBlock($block4);

//Adding fields to Block3

$field21 = new Vtiger_Field();
$field21->label = 'LBL_EMPLOYEES_EMGNAME';
$field21->name = 'employee_emgname';                                // Must be the same as column.
$field21->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field21->column = 'employee_emgname';                            //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(50)';
$field21->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_EMPLOYEES_EMGLAST';
$field22->name = 'employee_emglast';                                // Must be the same as column.
$field22->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field22->column = 'employee_emglast';                            //  This will be the columnname in your database for the new field.
$field22->columntype = 'VARCHAR(50)';
$field22->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_EMPLOYEES_RELATION';
$field23->name = 'employee_relation';                                // Must be the same as column.
$field23->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field23->column = 'employee_relation';                            //  This will be the columnname in your database for the new field.
$field23->columntype = 'VARCHAR(100)';
$field23->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field23->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field23);
$field23->setPicklistValues(array('Family', '', 'Friend', 'Spouse'));


$field24 = new Vtiger_Field();
$field24->label = 'LBL_EMPLOYEES_EMGEMAIL';
$field24->name = 'employee_emgemail';                                // Must be the same as column.
$field24->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field24->column = 'employee_emgemail';                            //  This will be the columnname in your database for the new field.
$field24->columntype = 'VARCHAR(100)';
$field24->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field24);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_EMPLOYEES_EMPHONE';
$field24->name = 'employee_emphone';                                // Must be the same as column.
$field24->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field24->column = 'employee_emphone';                            //  This will be the columnname in your database for the new field.
$field24->columntype = 'INT(20)';
$field24->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'I~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field24);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_EMPLOYEES_EHPHONE';
$field24->name = 'employee_ehphone';                                // Must be the same as column.
$field24->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field24->column = 'employee_ehphone';                            //  This will be the columnname in your database for the new field.
$field24->columntype = 'INT(20)';
$field24->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'I~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field24);

$block4->save($module);
// END Add new block
;
