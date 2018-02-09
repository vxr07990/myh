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
$module = Vtiger_Module::getInstance('Surveys'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_BLOCK_GENERAL_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINADDRESS1';
$field1->name = 'origin_address1';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_address1';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINADDRESS2';
$field1->name = 'origin_address2';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_address2';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINCITY';
$field1->name = 'origin_city';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_city';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINSTATE';
$field1->name = 'origin_state';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_state';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINZIP';
$field1->name = 'origin_zip';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_zip';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(10)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINCOUNTRY';
$field1->name = 'origin_country';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_country';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINPHONE1';
$field1->name = 'origin_phone1';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_phone1';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(30)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINPHONE2';
$field1->name = 'origin_phone2';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_phone2';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(30)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_ORIGINDESCRIPTION';
$field1->name = 'origin_description';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'origin_description';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_COMMERCIALORRESIDENTIAL';
$field1->name = 'comm_res';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'comm_res';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~M';
$field1->setPicklistValues(array('Commercial', 'Residential'));


$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_PACKING';
$field1->name = 'include_packing';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'include_packing';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_SURVEYENDTIME';
$field1->name = 'survey_end_time';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'survey_end_time';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'TIME';
$field1->uitype = 70; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'T~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_NOTES';
$field1->name = 'survey_notes';
$field1->table = 'vtiger_surveyscf';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'survey_notes';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);


$block1->save($module);
// END Add new field


 
?>

