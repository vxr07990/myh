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
$module = Vtiger_Module::getInstance('TariffManager'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_TARIFFMANAGER_TARIFFRATES';
$module->addBlock($block1);

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TARIFFTYPE';
$field1->name = 'tariff_type';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'tariff_type';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$field1->setPicklistValues(array('Local', 'Intra-state', 'Inter-state'));

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_CUFTPRICE';
$field1->name = 'cuft_price';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'cuft_price';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_WEIGHTPRICE';
$field1->name = 'weight_price';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'weight_price';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_MILEPRICE';
$field1->name = 'mile_price';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'mile_price';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_FUELCHARGE';
$field1->name = 'fuel_charge';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'fuel_charge';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_PERSTOP';
$field1->name = 'per_stop';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'per_stop';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_WALKINGPRICE';
$field1->name = 'walking_price';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'walking_price';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_FLIGHTOFSTAIRS';
$field1->name = 'flightof_stairs';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'flightof_stairs';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(8,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TAX';
$field1->name = 'tariff_tax';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'tariff_tax';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(8,2)';
$field1->uitype = 9;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$block1->save($module);
// END Add new field

// Or to create a new block
$module = Vtiger_Module::getInstance('TariffManager'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_TARIFFMANAGER_TARIFFMINIMUMS';
$module->addBlock($block1);

// START Add new field

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_MINQUOTEPRICE';
$field1->name = 'min_price';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'min_price';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_MINMILES';
$field1->name = 'min_miles';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'min_miles';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(4,1)';
$field1->uitype = 7;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_MINCREW';
$field1->name = 'mile_crew';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'mile_crew';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(4,1)';
$field1->uitype = 7;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$block1->save($module);
// END Add new field

// Or to create a new block
$module = Vtiger_Module::getInstance('TariffManager'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_TARIFFMANAGER_TARIFFDISCOUNTS';
$module->addBlock($block1);

// START Add new field

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_BOTTOMLINEDISCOUNT';
$field1->name = 'bottom_line';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'bottom_line';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 9;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_LOADONLYDISCOUNT';
$field1->name = 'load_only';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'load_only';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 9;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_UNLOADONLYDISCOUNT';
$field1->name = 'unload_only';
$field1->table = 'vtiger_tariffmanager';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'unload_only';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 9;
$field1->typeofdata = 'N~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$block1->save($module);
// END Add new field
;
