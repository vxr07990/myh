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


// Or to create a new block
$module = Vtiger_Module::getInstance('Project'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_PROJECT_DESTINATIONADDRESSDESCRIPTION';
$module->addBlock($block1);


// START Add new field

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGINCOUNTRY';
$field1->name = 'origin_country';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_country';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGINDESCRIPTION';
$field1->name = 'origin_description';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_description';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_COMMERCIALORRESIDENTIAL';
$field1->name = 'comm_res';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'comm_res';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~M';
$field1->setPicklistValues(array('Commercial', 'Residential'));

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_PACKING';
$field1->name = 'include_packing';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'include_packing';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGINADDRESSFLIGHTSOFSTAIRS';
$field1->name = 'originaddress_flightsofstairs';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'originaddress_flightsofstairs';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(2,0)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATIONADDRESSCOUNTRY';
$field1->name = 'destinationaddress_country';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_country';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATIONADDRESSDESCRIPTION';
$field1->name = 'destinationaddress_description';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_description';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATIONADDRESSFLIGHTSOFSTAIRS';
$field1->name = 'destinationaddress_flightsofstairs';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_flightsofstairs';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(2,0)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_PRICING';
$field1->name = 'pricing_type';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'pricing_type';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O';
$field1->setPicklistValues(array('Peak', 'Non Peak'));

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ESTIMATETYPE';
$field1->name = 'estimate_type';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'estimate_type';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O';
$field1->setPicklistValues(array('Binding', 'Non-Binding', 'Not to Exceed'));

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ISCONVERTEDFROMLEAD';
$field1->name = 'isconvertedfromlead';
$field1->table = 'vtiger_project';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'isconvertedfromlead';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$block1->save($module);
// END Add new field


 
?>

