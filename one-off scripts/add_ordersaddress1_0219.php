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

/*
// To use a pre-existing block
 $module = Vtiger_Module::getInstance('Orders'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_ORDERS_DATES',$module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_ORDERS_PDATE';
$field1->name = 'orders_pdate';
$field1->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'orders_pdate';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_ORDERS_LDATE';
$field2->name = 'orders_ldate';
$field2->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'orders_ldate';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'DATE';
$field2->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_ORDERS_DDATE';
$field3->name = 'orders_ddate';
$field3->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'orders_ddate';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);


$field5 = new Vtiger_Field();
$field5->label = 'LBL_ORDERS_PTDATE';
$field5->name = 'orders_ptdate';
$field5->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'orders_ptdate';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_ORDERS_LTDATE';
$field6->name = 'orders_ltdate';
$field6->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'orders_ltdate';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_ORDERS_DTDATE';
$field7->name = 'orders_dtdate';
$field7->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'orders_dtdate';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'DATE';
$field7->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_ORDERS_SURVEYD';
$field8->name = 'orders_surveyd';
$field8->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'orders_surveyd';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'DATE';
$field8->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_ORDERS_SURVEYT';
$field9->name = 'orders_surveyt';
$field9->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'orders_surveyt';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'TIME';
$field9->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);

$block1->save($module);
*/
$module = Vtiger_Module::getInstance('Orders'); // The module your blocks and fields will be in.
$block2 = new Vtiger_Block();
$block2->label = 'LBL_ORDERS_INVOICE';
$module->addBlock($block2);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_ORDERS_PRICINGTYPE';
$field10->name = 'pricing_type';
$field10->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'pricing_type';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(220)';
$field10->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field10);
$field10->setPicklistValues(array('Non Peak', 'Peak'));

$field11 = new Vtiger_Field();
$field11->label = 'LBL_ORDERS_BILLWEIGHT';
$field11->name = 'bill_weight';
$field11->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'bill_weight';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(50)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_ORDERS_ESTIMATETYPE';
$field12->name = 'estimate_type';
$field12->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'estimate_type';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(220)';
$field12->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field12);
$field12->setPicklistValues(array('Binding', 'Non-Binding', 'Not to Exceed'));

$field13 = new Vtiger_Field();
$field13->label = 'LBL_ORDERS_PRICINGMODE';
$field13->name = 'pricing_mode';
$field13->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'pricing_mode';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'VARCHAR(220)';
$field13->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_ORDERS_PAYTYPE';
$field14->name = 'payment_type';
$field14->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'payment_type';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'VARCHAR(220)';
$field14->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field14);
$field14->setPicklistValues(array('Check', 'Electronic Transfer', 'Credit', 'Cash'));

$field15 = new Vtiger_Field();
$field15->label = 'LBL_ORDERS_INVOICESTATUS';
$field15->name = 'invoice_status';
$field15->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field15->column = 'invoice_status';   //  This will be the columnname in your database for the new field.
$field15->columntype = 'VARCHAR(220)';
$field15->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block2->addField($field15);
$field15->setPicklistValues(array('Created', 'Cancel', 'Approved', 'Sent', 'Paid'));

$block2->save($module);

$module = Vtiger_Module::getInstance('Orders'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_ORDERS_ORIGINADDRESS';
$module->addBlock($block3);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_ORDERS_OADDRESS1';
$field16->name = 'origin_address1';
$field16->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'origin_address1';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'VARCHAR(220)';
$field16->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field16);

$field17 = new Vtiger_Field();
$field17->label = 'LBL_ORDERS_ODESCRIPTION';
$field17->name = 'origin_description';
$field17->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'origin_description';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(220)';
$field17->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field17);
$field17->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));

$field18 = new Vtiger_Field();
$field18->label = 'LBL_ORDERS_OADDRESS2';
$field18->name = 'origin_address2';
$field18->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'origin_address2';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(220)';
$field18->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_ORDERS_OCITY';
$field19->name = 'origin_city';
$field19->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field19->column = 'origin_city';   //  This will be the columnname in your database for the new field.
$field19->columntype = 'VARCHAR(220)';
$field19->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_ORDERS_OSTATE';
$field20->name = 'origin_state';
$field20->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field20->column = 'origin_state';   //  This will be the columnname in your database for the new field.
$field20->columntype = 'VARCHAR(220)';
$field20->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field20);

$field21 = new Vtiger_Field();
$field21->label = 'LBL_ORDERS_OZIP';
$field21->name = 'origin_zip';
$field21->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field21->column = 'origin_zip';   //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(220)';
$field21->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_ORDERS_OCOUNTRY';
$field22->name = 'origin_country';
$field22->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field22->column = 'origin_country';   //  This will be the columnname in your database for the new field.
$field22->columntype = 'VARCHAR(220)';
$field22->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_ORDERS_OPHONE1';
$field23->name = 'origin_phone1';
$field23->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field23->column = 'origin_phone1';   //  This will be the columnname in your database for the new field.
$field23->columntype = 'INT(50)';
$field23->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field23);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_ORDERS_OPHONE2';
$field24->name = 'origin_phone2';
$field24->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field24->column = 'origin_phone2';   //  This will be the columnname in your database for the new field.
$field24->columntype = 'INT(50)';
$field24->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field24);

$block3->save($module);


$module = Vtiger_Module::getInstance('Orders'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_ORDERS_DESTINATIONADDRESS';
$module->addBlock($block4);

$field25 = new Vtiger_Field();
$field25->label = 'LBL_ORDERS_DADDRESS1';
$field25->name = 'destination_address1';
$field25->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field25->column = 'destination_address1';   //  This will be the columnname in your database for the new field.
$field25->columntype = 'VARCHAR(220)';
$field25->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field25->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field25);

$field26 = new Vtiger_Field();
$field26->label = 'LBL_ORDERS_DDESCRIPTION';
$field26->name = 'destination_description';
$field26->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field26->column = 'destination_description';   //  This will be the columnname in your database for the new field.
$field26->columntype = 'VARCHAR(220)';
$field26->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field26->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field26);
$field26->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));

$field27 = new Vtiger_Field();
$field27->label = 'LBL_ORDERS_DADDRESS2';
$field27->name = 'destination_address2';
$field27->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field27->column = 'destination_address2';   //  This will be the columnname in your database for the new field.
$field27->columntype = 'VARCHAR(220)';
$field27->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field27->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field27);

$field28 = new Vtiger_Field();
$field28->label = 'LBL_ORDERS_DCITY';
$field28->name = 'destination_city';
$field28->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field28->column = 'destination_city';   //  This will be the columnname in your database for the new field.
$field28->columntype = 'VARCHAR(220)';
$field28->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field28->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'LBL_ORDERS_DSTATE';
$field29->name = 'destination_state';
$field29->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field29->column = 'destination_state';   //  This will be the columnname in your database for the new field.
$field29->columntype = 'VARCHAR(220)';
$field29->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field29->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'LBL_ORDERS_DZIP';
$field30->name = 'destination_zip';
$field30->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field30->column = 'destination_zip';   //  This will be the columnname in your database for the new field.
$field30->columntype = 'VARCHAR(220)';
$field30->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field30->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field30);

$field31 = new Vtiger_Field();
$field31->label = 'LBL_ORDERS_DCOUNTRY';
$field31->name = 'destination_country';
$field31->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field31->column = 'destination_country';   //  This will be the columnname in your database for the new field.
$field31->columntype = 'VARCHAR(220)';
$field31->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field31->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field31);

$field32 = new Vtiger_Field();
$field32->label = 'LBL_ORDERS_DPHONE1';
$field32->name = 'destination_phone1';
$field32->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field32->column = 'destination_phone1';   //  This will be the columnname in your database for the new field.
$field32->columntype = 'INT(50)';
$field32->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field32->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field32);

$field33 = new Vtiger_Field();
$field33->label = 'LBL_ORDERS_DPHONE2';
$field33->name = 'destination_phone2';
$field33->table = 'vtiger_orders';  // This is the tablename from your database that the new field will be added to.
$field33->column = 'destination_phone2';   //  This will be the columnname in your database for the new field.
$field33->columntype = 'INT(50)';
$field33->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field33->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field33);

$block4->save($module);
