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
 $block1 = Vtiger_Block::getInstance('LBL_PROJECT_DATES', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_PPDATE';
$field1->name = 'project_ppdate';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'project_ppdate';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_PROJECT_PLDATE';
$field2->name = 'project_pldate';
$field2->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'project_pldate';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'DATE';
$field2->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_PROJECT_PDDATE';
$field3->name = 'project_pddate';
$field3->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'project_pddate';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);

$block1->save($module);

$module = Vtiger_Module::getInstance('Project'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_PROJECT_WEIGHTS';
$module->addBlock($block2);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_PROJECT_EWEIGHT';
$field4->name = 'project_eweight';
$field4->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'project_eweight';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'INT(50)';
$field4->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_PROJECT_ECUBE';
$field5->name = 'project_ecube';
$field5->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'project_ecube';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'INT(50)';
$field5->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_PROJECT_PCOUNT';
$field6->name = 'project_pcount';
$field6->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'project_pcount';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'INT(50)';
$field6->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_PROJECT_AWEIGHT';
$field7->name = 'project_aweight';
$field7->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'project_aweight';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'INT(50)';
$field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_PROJECT_GWEIGHT';
$field8->name = 'project_gweight';
$field8->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'project_gweight';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(50)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_PROJECT_TWEIGHT';
$field9->name = 'project_tweight';
$field9->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'project_tweight';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(50)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field9);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_PROJECT_NETWEIGHT';
$field9->name = 'project_netweight';
$field9->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'project_netweight';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'INT(50)';
$field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_PROJECT_MINWEIGHT';
$field10->name = 'project_minweight';
$field10->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'project_minweight';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'INT(50)';
$field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_PROJECT_RGWEIGHT';
$field11->name = 'project_rgweight';
$field11->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'project_rgweight';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(50)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_PROJECT_RTWEIGHT';
$field12->name = 'project_rtweight';
$field12->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'project_rtweight';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'INT(50)';
$field12->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field12);

$field13 = new Vtiger_Field();
$field13->label = 'LBL_PROJECT_RNETWEIGHT';
$field13->name = 'project_rnetweight';
$field13->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'project_rnetweight';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'INT(50)';
$field13->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field13);

$block2->save($module);
