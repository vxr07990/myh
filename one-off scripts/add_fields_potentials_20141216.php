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
$module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY';
$field1->name = 'originaddress_country';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'originaddress_country';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_ORIGINADDRESSDESCRIPTION';
$field1->name = 'originaddress_description';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'originaddress_description';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_ORIGINADDRESSFLIGHTSOFSTAIRS';
$field1->name = 'originaddress_flightsofstairs';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'originaddress_flightsofstairs';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(2,0)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY';
$field1->name = 'destinationaddress_country';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_country';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDESCRIPTION';
$field1->name = 'destinationaddress_description';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_description';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_DESTINATIONADDRESSFLIGHTSOFSTAIRS';
$field1->name = 'destinationaddress_flightsofstairs';
$field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'destinationaddress_flightsofstairs';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(2,0)';
$field1->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);


$block1->save($module);
// END Add new field
;
