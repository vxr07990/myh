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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$StopsIsNew = false;  //flag for filters at the end

//Start Stops Module
$module1 = Vtiger_Module::getInstance('Stops');
if ($module1) {
    echo "<h2>Updating Stops Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Stops';
    $module1->save();
    echo "<h2>Creating Module Stops and Updating Fields</h2><br>";
    $module1->initTables();
}

//start block1 : LBL_STOPS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_STOPS_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_STOPS_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_STOPS_INFORMATION';
    $module1->addBlock($block1);
    $StopsIsNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('createdtime', $module1);
    if ($field1) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'Created Time';
        $field1->name = 'createdtime';
        $field1->table = 'vtiger_crmentity';
        $field1->column = 'createdtime';
        $field1->uitype = 70;
        $field1->typeofdata = 'T~O';
        $field1->displaytype = 2;

        $block1->addField($field1);
    }

$field2 = Vtiger_Field::getInstance('modifiedtime', $module1);
    if ($field2) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Modified Time';
        $field2->name = 'modifiedtime';
        $field2->table = 'vtiger_crmentity';
        $field2->column = 'modifiedtime';
        $field2->uitype = 70;
        $field2->typeofdata = 'T~O';
        $field2->displaytype = 2;

        $block1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('assigned_user_id', $module1);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Assigned To';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';

    $block1->addField($field3);
}

$field10 = Vtiger_Field::getInstance('stops_address1', $module1);
if ($field10) {
    echo "<li>The stops_address1 field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_STOPS_ADDRESS1';
    $field10->name = 'stops_address1';
    $field10->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'stops_address1';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'VARCHAR(255)';
    $field10->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field10);
}

$field6 = Vtiger_Field::getInstance('stops_city', $module1);
if ($field6) {
    echo "<li>The stops_city field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_STOPS_CITY';
    $field6->name = 'stops_city';
    $field6->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'stops_city';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('stops_state', $module1);
if ($field7) {
    echo "<li>The stops_state field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_STOPS_STATE';
    $field7->name = 'stops_state';
    $field7->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'stops_state';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field7);
}

$field11 = Vtiger_Field::getInstance('stop_p1', $module1);
if ($field11) {
    echo "<li>The stop_p1 field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_STOPS_P1';
    $field11->name = 'stop_p1';
    $field11->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'stop_p1';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'VARCHAR(50)';
    $field11->uitype = 11; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field11);
}

//start from stops_add_Fields_0205.php
$field15 = Vtiger_Field::getInstance('stop_type', $module1);
if ($field15) {
    echo "<li>The stop_type field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_STOPS_TYPE';
    $field15->name = 'stop_type';
    $field15->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field15->column = 'stop_type';   //  This will be the columnname in your database for the new field.
    $field15->columntype = 'VARCHAR(100)';
    $field15->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field15);
    $field15->setPicklistValues(array('Destination', 'Extra Delivery', 'Extra Pickup', 'Origin'));
}
$field16 = Vtiger_Field::getInstance('stop_address2', $module1);
if ($field16) {
    echo "<li>The stop_address2 field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_STOPS_ADDRESS2';
    $field16->name = 'stop_address2';
    $field16->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field16->column = 'stop_address2';   //  This will be the columnname in your database for the new field.
    $field16->columntype = 'VARCHAR(100)';
    $field16->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field16->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field16);
}
$field17 = Vtiger_Field::getInstance('stop_country', $module1);
if ($field17) {
    echo "<li>The stop_country field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_STOPS_COUNTRY';
    $field17->name = 'stop_country';
    $field17->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field17->column = 'stop_country';   //  This will be the columnname in your database for the new field.
    $field17->columntype = 'VARCHAR(100)';
    $field17->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field17->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field17);
}
$field18 = Vtiger_Field::getInstance('stop_description', $module1);
if ($field18) {
    echo "<li>The stop_description field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_STOPS_DESCRIPTION';
    $field18->name = 'stop_description';
    $field18->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field18->column = 'stop_description';   //  This will be the columnname in your database for the new field.
    $field18->columntype = 'VARCHAR(100)';
    $field18->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field18->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field18);
    $field18->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));
}
$field19 = Vtiger_Field::getInstance('stop_p2', $module1);
if ($field19) {
    echo "<li>The stop_p2 field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_STOPS_P2';
    $field19->name = 'stop_p2';
    $field19->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field19->column = 'stop_p2';   //  This will be the columnname in your database for the new field.
    $field19->columntype = 'VARCHAR(50)';
    $field19->uitype = 11; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field19->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field19);
}
$field20 = Vtiger_Field::getInstance('stop_ptype1', $module1);
if ($field20) {
    echo "<li>The stop_ptype1 field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_STOPS_PTYPE1';
    $field20->name = 'stop_ptype1';
    $field20->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field20->column = 'stop_ptype1';   //  This will be the columnname in your database for the new field.
    $field20->columntype = 'VARCHAR(100)';
    $field20->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field20->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field20);
    $field20->setPicklistValues(array('Business', 'Home', 'Mobile', 'Work', 'Other'));
}
$field21 = Vtiger_Field::getInstance('stop_ptype2', $module1);
if ($field21) {
    echo "<li>The stop_ptype2 field already exists</li><br> \n";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_STOPS_PTYPE2';
    $field21->name = 'stop_ptype2';
    $field21->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field21->column = 'stop_ptype2';   //  This will be the columnname in your database for the new field.
    $field21->columntype = 'VARCHAR(100)';
    $field21->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field21);
    $field21->setPicklistValues(array('Business', 'Home', 'Mobile', 'Work', 'Other'));
}
$field22 = Vtiger_Field::getInstance('stop_sequence', $module1);
if ($field22) {
    echo "<li>The stop_sequence field already exists</li><br> \n";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_STOPS_STSEQ';
    $field22->name = 'stop_sequence';
    $field22->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field22->column = 'stop_sequence';   //  This will be the columnname in your database for the new field.
    $field22->columntype = 'VARCHAR(100)';
    $field22->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field22->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field22);
    $field22->setPicklistValues(array('1', '2', '3', '4', '5', '6', '7', '8', '9'));
}
$field23 = Vtiger_Field::getInstance('stop_datefrom', $module1);
if ($field23) {
    echo "<li>The stop_datefrom field already exists</li><br> \n";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STOPS_DATEFROM';
    $field23->name = 'stop_datefrom';
    $field23->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field23->column = 'stop_datefrom';   //  This will be the columnname in your database for the new field.
    $field23->columntype = 'DATE';
    $field23->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field23->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field23);
}
$field24 = Vtiger_Field::getInstance('stop_dateto', $module1);
if ($field24) {
    echo "<li>The stop_dateto field already exists</li><br> \n";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_STOPS_DATETO';
    $field24->name = 'stop_dateto';
    $field24->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field24->column = 'stop_dateto';   //  This will be the columnname in your database for the new field.
    $field24->columntype = 'DATE';
    $field24->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field24->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field24);
}
$field25 = Vtiger_Field::getInstance('stop_weight', $module1);
if ($field25) {
    echo "<li>The stop_weight field already exists</li><br> \n";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_STOPS_WEIGHT';
    $field25->name = 'stop_weight';
    $field25->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field25->column = 'stop_weight';   //  This will be the columnname in your database for the new field.
    $field25->columntype = 'INT(20)';
    $field25->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field25->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field25);
}
//end from stops_add_Fields_0205.php

//start from stops_add_fields_0210.php
$field26 = Vtiger_Field::getInstance('stop_zip', $module1);
if ($field26) {
    echo "<li>The stop_zip field already exists</li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_STOPS_ZIP';
    $field26->name = 'stop_zip';
    $field26->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field26->column = 'stop_zip';   //  This will be the columnname in your database for the new field.
    $field26->columntype = 'INT(200)';
    $field26->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field26->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field26);
}
//end from stops_add_fields_0210.php

//start from stops_add_fields_0223.php
$field37 = Vtiger_Field::getInstance('stop_order', $module1);
if ($field37) {
    echo "<li>The stop_order field already exists</li><br> \n";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_STOPS_ORDERS';
    $field37->name = 'stop_order';
    $field37->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field37->column = 'stop_order';   //  This will be the columnname in your database for the new field.
    $field37->columntype = 'VARCHAR(255)';
    $field37->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field37->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field37);
    $field37->setRelatedModules(array('Orders'));
}
//end from stops_add_fields_0223.php

//start from stops_add_fields_20150115.p
/* might get added back in -ACS 20150514
$field50 = Vtiger_Field::getInstance('stop_contact',$module1);
if ($field50) {
    echo "<li>The stop_contact field already exists</li><br> \n";
}
else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_STOPS_CONTACT';
    $field50->name = 'stop_contact';
    $field50->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field50->column = 'stop_contact';   //  This will be the columnname in your database for the new field.
    $field50->columntype = 'VARCHAR(100)';
    $field50->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field50->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field50);
    $field50->setRelatedModules(Array('Contacts'));
}
*/


$field54 = Vtiger_Field::getInstance('stop_opp', $module1);
if ($field54) {
    echo "<li>The stop_opp field already exists</li><br> \n";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_STOPS_OPP';
    $field54->name = 'stop_opp';
    $field54->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field54->column = 'stop_opp';   //  This will be the columnname in your database for the new field.
    $field54->columntype = 'VARCHAR(255)';
    $field54->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field54->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field54);
    $field54->setRelatedModules(array('Opportunities'));
}


$field55 = Vtiger_Field::getInstance('stop_est', $module1);
if ($field55) {
    echo "<li>The stop_opp field already exists</li><br> \n";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'LBL_STOPS_EST';
    $field55->name = 'stop_est';
    $field55->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field55->column = 'stop_est';   //  This will be the columnname in your database for the new field.
    $field55->columntype = 'VARCHAR(255)';
    $field55->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field55->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field55);
    $field55->setRelatedModules(array('Estimates'));
}

$field56 = Vtiger_Field::getInstance('stops_isprimary', $module1);
if ($field56) {
    echo "<li>The stop_opp field already exists</li><br> \n";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_STOPS_ISPRIMARY';
    $field56->name = 'stops_isprimary';
    $field56->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field56->column = 'stops_isprimary';   //  This will be the columnname in your database for the new field.
    $field56->columntype = 'VARCHAR(3)';
    $field56->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field56->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field56);
}

$field57 = Vtiger_Field::getInstance('stops_name', $module1);
if ($field57) {
    echo "<li>The stops_name field already exists</li><br> \n";
} else {
    $field57 = new Vtiger_Field();
    $field57->label = 'LBL_STOPS_NAME';
    $field57->name = 'stops_name';
    $field57->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field57->column = 'stops_name';   //  This will be the columnname in your database for the new field.
    $field57->columntype = 'VARCHAR(100)';
    $field57->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field57->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field57);
    $module1->setEntityIdentifier($field57);
}
//end from stops_add_fields_20150115.php

//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_STOPS_INFORMATION
echo "save worked";
//start block2 : LBL_STOPS_RECORDUPDATE
$block2 = Vtiger_Block::getInstance('LBL_STOPS_RECORDUPDATE', $module1);
if ($block2) {
    echo "<h3>The LBL_STOPS_RECORDUPDATE block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_STOPS_RECORDUPDATE';
    $module1->addBlock($block2);
}
$block2->save($module1);
//end block2 : LBL_STOPS_RECORDUPDATE

if ($StopsIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);
    $filter1->addField($field10)->addField($field6, 1)->addField($field7, 2)->addField($field11, 3);

    $module1->setDefaultSharing();
    $module1->initWebservice();
    ModTracker::enableTrackingForModule($module1->id);

    //START Add navigation link in module opportunities to orders
$ordersInstance = Vtiger_Module::getInstance('Orders');
    $ordersInstance->setRelatedList(Vtiger_Module::getInstance('Stops'), 'Stops', array('ADD'), 'get_dependents_list');
//END Add navigation link in module

//START Add navigation link in module orders to orderstask
$ordersInstance = Vtiger_Module::getInstance('Orders');
    $ordersInstance->setRelatedList(Vtiger_Module::getInstance('Estimates'), 'Estimates', array('ADD'), 'get_related_list');
//END Add navigation link in module
}
//End Stops Module
echo "<br> <h1> SCRIPT COMPLETED </h1>";
