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
$module = Vtiger_Module::getInstance('Vanlines'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_VANLINES_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.



$field1 = new Vtiger_Field();
$field1->label = 'LBL_VANLINES_ADDRESS1';
$field1->name = 'vanline_address1';                                // Must be the same as column.
$field1->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'vanline_address1';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_VANLINES_ADDRESS2';
$field2->name = 'vanline_address2';                                // Must be the same as column.
$field2->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field2->column = 'vanline_address2';                            //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(50)';
$field2->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);
// Use only if this field is being added to relate to another module.
//$field1->setRelatedModules(Array('Potentials'));  			// Make sure to change to the name of the module your blocks and fields will be in.
$field3 = new Vtiger_Field();
$field3->label = 'LBL_VANLINES_CITY';
$field3->name = 'vanline_city';                                // Must be the same as column.
$field3->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field3->column = 'vanline_city';                            //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(50)';
$field3->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_VANLINES_STATE';
$field4->name = 'vanline_state';                                // Must be the same as column.
$field4->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field4->column = 'vanline_state';                            //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(50)';
$field4->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_VANLINES_ZIP';
$field5->name = 'vanline_zip';                                // Must be the same as column.
$field5->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field5->column = 'vanline_zip';                            //  This will be the columnname in your database for the new field.
$field5->columntype = 'INT(10)';
$field5->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_VANLINES_COUNTRY';
$field6->name = 'vanline_country';                                // Must be the same as column.
$field6->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field6->column = 'vanline_country';                            //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(50)';
$field6->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_VANLINES_PHONE';
$field7->name = 'vanline_phone';                                // Must be the same as column.
$field7->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field7->column = 'vanline_phone';                            //  This will be the columnname in your database for the new field.
$field7->columntype = 'INT(10)';
$field7->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_VANLINES_FAX';
$field8->name = 'vanline_fax';                                // Must be the same as column.
$field8->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field8->column = 'vanline_fax';                            //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(10)';
$field8->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_VANLINES_EMAIL';
$field9->name = 'vanline_email';                                // Must be the same as column.
$field9->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field9->column = 'vanline_email';                            //  This will be the columnname in your database for the new field.
$field9->columntype = 'VARCHAR(50)';
$field9->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_VANLINES_CONTACT';
$field10->name = 'vanline_contact';                                // Must be the same as column.
$field10->table = 'vtiger_vanlines';                        // This is the tablename from your database that the new field will be added to.
$field10->column = 'vanline_contact';                            //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(50)';
$field10->uitype = 10;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);
$field10->setRelatedModules(array('Contacts'));  // Make sure to change to the name of the module your blocks and fields will be in.

$block1->save($module);
// END Add new field


// To use a pre-existing block
// $module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
// $block1 = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION',$module);  // Must be the actual instance name, not just what appears in the browser.

// Or to create a new block
$module = Vtiger_Module::getInstance('Vanlines'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_VANLINES_RECORDUPDATE';
$module->addBlock($block2);

$block2->save($module);
// END Add new block
;
