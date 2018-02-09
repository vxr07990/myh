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
 $module = Vtiger_Module::getInstance('Safety'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_SAFETY_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

$field9 = new Vtiger_Field();
$field9->label = 'LBL_SAFETY_CONTRACTORS';
$field9->name = 'safety_contractors';
$field9->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'safety_contractors';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'VARCHAR(100)';
$field9->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);
$field9->setRelatedModules(array('Contractors'));

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_SAFETY_DLNUMBER';
$field1->name = 'safety_dlnumber';
$field1->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'safety_dlnumber';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

//start adding
$field2 = new Vtiger_Field();
$field2->label = 'LBL_SAFETY_DLSTATE';
$field2->name = 'safety_dlstate';
$field2->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'safety_dlstate';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(2)';
$field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);


$field3 = new Vtiger_Field();
$field3->label = 'LBL_SAFETY_DLEDATE';
$field3->name = 'safety_dlestate';
$field3->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'safety_dlestate';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
 

$field4 = new Vtiger_Field();
$field4->label = 'LBL_SAFETY_CLASS';
$field4->name = 'safety_class';
$field4->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'safety_class';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(100)';
$field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$block1->save($module);

// END Add new field
// Or to create a new block
$module = Vtiger_Module::getInstance('Safety'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_SAFETY_DETAILS';
$module->addBlock($block2);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_SAFETY_LPDATE';
$field5->name = 'safety_lpdate';
$field5->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'safety_lpdate';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_SAFETY_NPDATE';
$field6->name = 'safety_npdate';
$field6->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'safety_npdate';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_SAFETY_LBDATE';
$field7->name = 'safety_lbdate';
$field7->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'safety_lbdate';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'DATE';
$field7->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field7);


$field8 = new Vtiger_Field();
$field8->label = 'LBL_SAFETY_NBDATE';
$field8->name = 'safety_nbdate';
$field8->table = 'vtiger_safety';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'safety_nbdate';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'DATE';
$field8->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field8);

$block2->save($module);


//START Add navigation link in module
$module = Vtiger_Module::getInstance('Contractors');
$module->setRelatedList(Vtiger_Module::getInstance('Safety'), 'Safety', array('ADD', 'SELECT'), 'get_dependents_list');
//END Add navigation link in module
;
