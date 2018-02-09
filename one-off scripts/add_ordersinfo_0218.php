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
 $module = Vtiger_Module::getInstance('Orders'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_ORDERS_FNAME';
$field1->name = 'orders_fname';
$field1->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'orders_fname';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_ORDERS_TRANSFEREES';
$field2->name = 'orders_transferees';
$field2->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'orders_transferees';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(220)';
$field2->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);
$field2->setRelatedModules(array('Transferees'));

$field3 = new Vtiger_Field();
$field3->label = 'LBL_ORDERS_ACCOUNT';
$field3->name = 'orders_account';
$field3->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'orders_account';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(220)';
$field3->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);
$field3->setRelatedModules(array('Accounts'));

$field4 = new Vtiger_Field();
$field4->label = 'LBL_ORDERS_ACCOUNTTYPE';
$field4->name = 'orders_accounttype';
$field4->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'orders_accounttype';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(220)';
$field4->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);
$field4->setPicklistValues(array('National Account', 'COD', 'Third Party Relocation'));

$field5 = new Vtiger_Field();
$field5->label = 'LBL_ORDERS_VANLINEREGNUM';
$field5->name = 'orders_vanlineregnum';
$field5->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'orders_vanlineregnum';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'INT(100)';
$field5->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_ORDERS_BOLNUMBER';
$field6->name = 'orders_bolnumber';
$field6->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'orders_bolnumber';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'INT(100)';
$field6->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_ORDERS_GBLNUMBER';
$field7->name = 'orders_gblnumber';
$field7->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'orders_gblnumber';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'INT(100)';
$field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_ORDERS_PONUMBER';
$field8->name = 'orders_ponumber';
$field8->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'orders_ponumber';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'INT(100)';
$field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_ORDERS_COMMODITY';
$field9->name = 'orders_commodity';
$field9->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'orders_commodity';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'VARCHAR(255)';
$field9->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);
$field9->setPicklistValues(array('Household Goods', 'Commercial', 'Military HHG', 'Military Commercial', 'Government Commercial'));

$field10 = new Vtiger_Field();
$field10->label = 'LBL_ORDERS_TARIFF';
$field10->name = 'orders_tariff';
$field10->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'orders_tariff';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(220)';
$field10->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);
$field10->setRelatedModules(array('Tariffs'));

$field11 = new Vtiger_Field();
$field11->label = 'LBL_ORDERS_ELINEHAUL';
$field11->name = 'orders_elinehaul';
$field11->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'orders_elinehaul';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(100)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_ORDERS_ETOTAL';
$field12->name = 'orders_etotal';
$field12->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'orders_etotal';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'INT(100)';
$field12->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field12);

$field13 = new Vtiger_Field();
$field13->label = 'LBL_ORDERS_ETYPE';
$field13->name = 'orders_etype';
$field13->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'orders_etype';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'VARCHAR(255)';
$field13->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field13);
$field13->setPicklistValues(array('Binding', 'Non Binding', 'Not to Exceed'));

$field14 = new Vtiger_Field();
$field14->label = 'LBL_ORDERS_DISCOUNT';
$field14->name = 'orders_discount';
$field14->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'orders_discount';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'INT(100)';
$field14->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field14);

$field15 = new Vtiger_Field();
$field15->label = 'LBL_ORDERS_MILES';
$field15->name = 'orders_miles';
$field15->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field15->column = 'orders_miles';   //  This will be the columnname in your database for the new field.
$field15->columntype = 'INT(100)';
$field15->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field15);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_ORDERS_POTENTIALS';
$field16->name = 'orders_potentials';
$field16->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'orders_potentials';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'VARCHAR(220)';
$field16->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field16);
$field16->setRelatedModules(array('Potentials'));

$field17 = new Vtiger_Field();
$field17->label = 'LBL_ORDERS_RELATED';
$field17->name = 'orders_relatedorders';
$field17->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'orders_relatedorders';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(220)';
$field17->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field17);
$field17->setRelatedModules(array('Orders'));

$block1->save($module);
