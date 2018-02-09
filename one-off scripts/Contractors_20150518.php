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
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//required for modcomments
include_once('modules/ModComments/ModComments.php');
//required for updates tracker
include_once('modules/ModTracker/ModTracker.php');
$contractorsIsNew = false;

$module1 = Vtiger_Module::getInstance('Contractors');
if ($module1) {
    echo "<h2>Updating Contractors Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Contractors';
    $module1->save();
    echo "<h2>Creating Module Contractors and Updating Fields</h2><br>";
    $module1->initTables();
}
//start of block1 : LBL_CONTRACTORS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $module1);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_CONTRACTORS_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_CONTRACTORS_INFORMATION';
    $module1->addBlock($block1);
    $contractorsIsNew = true;
}
//start of block1 fields
echo "<ul>";

$field1 = Vtiger_Field::getInstance('name', $module1);
if ($field1) {
    echo "<li>The name field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CONTRACTORS_NAME';
    $field1->name = 'name';
    $field1->table = 'vtiger_contractors';
    $field1->column = 'name';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $block1->addField($field1);
    $module1->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('contractor_lname', $module1);
if ($field2) {
    echo "<li>The contractor_lname field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CONTRACTORS_LNAME';
    $field2->name = 'contractor_lname';
    $field2->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field2->column = 'contractor_lname';   //  This will be the columnname in your database for the new field.
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('contractor_address1', $module1);
if ($field3) {
    echo "<li>The contractor_address1 field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CONTRACTORS_ADDRESS1';
    $field3->name = 'contractor_address1';
    $field3->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field3->column = 'contractor_address1';   //  This will be the columnname in your database for the new field.
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('contractor_address2', $module1);
if ($field4) {
    echo "<li>The contractor_address2 field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CONTRACTORS_ADDRESS2';
    $field4->name = 'contractor_address2';
    $field4->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field4->column = 'contractor_address2';   //  This will be the columnname in your database for the new field.
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('contractor_city', $module1);
if ($field5) {
    echo "<li>The contractor_city field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CONTRACTORS_CITY';
    $field5->name = 'contractor_city';
    $field5->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field5->column = 'contractor_city';   //  This will be the columnname in your database for the new field.
    $field5->columntype = 'VARCHAR(100)';
    $field5->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('contractor_state', $module1);
if ($field6) {
    echo "<li>The contractor_state field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CONTRACTORS_STATE';
    $field6->name = 'contractor_state';
    $field6->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'contractor_state';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(2)';
    $field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('contractor_zip', $module1);
if ($field7) {
    echo "<li>The contractor_zip field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CONTRACTORS_ZIP';
    $field7->name = 'contractor_zip';
    $field7->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'contractor_zip';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'INT(20)';
    $field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('contractor_country', $module1);
if ($field8) {
    echo "<li>The contractor_country field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CONTRACTORS_COUNTRY';
    $field8->name = 'contractor_country';
    $field8->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field8->column = 'contractor_country';   //  This will be the columnname in your database for the new field.
    $field8->columntype = 'VARCHAR(100)';
    $field8->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('contractor_p1', $module1);
if ($field9) {
    echo "<li>The contractor_p1 field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CONTRACTORS_P1';
    $field9->name = 'contractor_p1';
    $field9->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field9->column = 'contractor_p1';   //  This will be the columnname in your database for the new field.
    $field9->columntype = 'INT(20)';
    $field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('contractor_p2', $module1);
if ($field10) {
    echo "<li>The contractor_p2 field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CONTRACTORS_P2';
    $field10->name = 'contractor_p2';
    $field10->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'contractor_p2';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'INT(20)';
    $field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field10);
}
$field11 = Vtiger_Field::getInstance('contractor_email', $module1);
if ($field11) {
    echo "<li>The contractor_email field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CONTRACTORS_EMAIL';
    $field11->name = 'contractor_email';
    $field11->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'contractor_email';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'VARCHAR(100)';
    $field11->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field11);
}
echo "</ul>";
$block1->save($module1);
//end of block1 : LBL_CONTRACTORS_INFORMATION

//start of block2 : LBL_CONTRACTORS_DETAILINFO
$block2 = Vtiger_Block::getInstance('LBL_CONTRACTORS_DETAILINFO', $module1);
if ($block2) {
    echo "<h3>The LBL_CONTRACTORS_DETAILINFO block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CONTRACTORS_DETAILINFO';
    $module1->addBlock($block2);
}
//start of block2 fields
$field12 = Vtiger_Field::getInstance('contractor_enum', $module1);
if ($field12) {
    echo "<li>The contractor_enum field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CONTRACTORS_ENUM';
    $field12->name = 'contractor_enum';
    $field12->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field12->column = 'contractor_enum';   //  This will be the columnname in your database for the new field.
    $field12->columntype = 'INT(50)';
    $field12->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field12);
}
$field13 = Vtiger_Field::getInstance('contractor_prole', $module1);
if ($field13) {
    echo "<li>The contractor_prole field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_CONTRACTORS_PROLE';
    $field13->name = 'contractor_prole';
    $field13->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field13->column = 'contractor_prole';   //  This will be the columnname in your database for the new field.
    $field13->columntype = 'VARCHAR(100)';
    $field13->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field13);
    $field13->setPicklistValues(array('Driver', 'Packer', 'Warehouse'));
}
$field14 = Vtiger_Field::getInstance('contractor_status', $module1);
if ($field14) {
    echo "<li>The contractor_status field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_CONTRACTORS_STATUS';
    $field14->name = 'contractor_status';
    $field14->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field14->column = 'contractor_status';   //  This will be the columnname in your database for the new field.
    $field14->columntype = 'VARCHAR(100)';
    $field14->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field14->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field14);
    $field14->setPicklistValues(array('Active', 'Terminated', 'Suspended'));
}
$field15 = Vtiger_Field::getInstance('contractor_status', $module1);
if ($field15) {
    echo "<li>The contractor_bdate field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_CONTRACTORS_BDATE';
    $field15->name = 'contractor_bdate';
    $field15->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field15->column = 'contractor_bdate';   //  This will be the columnname in your database for the new field.
    $field15->columntype = 'DATE';
    $field15->uitype =5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field15);
}
$field16 = Vtiger_Field::getInstance('contractor_hdate', $module1);
if ($field16) {
    echo "<li>The contractor_hdate field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_CONTRACTORS_HDATE';
    $field16->name = 'contractor_hdate';
    $field16->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field16->column = 'contractor_hdate';   //  This will be the columnname in your database for the new field.
    $field16->columntype = 'DATE';
    $field16->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field16->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field16);
}
$field17 = Vtiger_Field::getInstance('contractor_cedate', $module1);
if ($field17) {
    echo "<li>The contractor_cedate field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_CONTRACTORS_CEDATE';
    $field17->name = 'contractor_cedate';
    $field17->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field17->column = 'contractor_cedate';   //  This will be the columnname in your database for the new field.
    $field17->columntype = 'DATE';
    $field17->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field17->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field17);
}
$field18 = Vtiger_Field::getInstance('contractor_rdate', $module1);
if ($field18) {
    echo "<li>The contractor_rdate field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_CONTRACTORS_RDATE';
    $field18->name = 'contractor_rdate';
    $field18->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field18->column = 'contractor_rdate';   //  This will be the columnname in your database for the new field.
    $field18->columntype = 'DATE';
    $field18->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field18->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field18);
}
$field19 = Vtiger_Field::getInstance('contractor_tdate', $module1);
if ($field19) {
    echo "<li>The contractor_tdate field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_CONTRACTORS_TDATE';
    $field19->name = 'contractor_tdate';
    $field19->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field19->column = 'contractor_tdate';   //  This will be the columnname in your database for the new field.
    $field19->columntype = 'DATE';
    $field19->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field19->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field19);
}
$field20 = Vtiger_Field::getInstance('contractor_trailernumber', $module1);
if ($field20) {
    echo "<li>The contractor_trailernumber field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_CONTRACTORS_TRAILERNUMBER';
    $field20->name = 'contractor_trailernumber';
    $field20->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field20->column = 'contractor_trailernumber';   //  This will be the columnname in your database for the new field.
    $field20->columntype = 'VARCHAR(100)';
    $field20->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field20->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field20);
}
$field21 = Vtiger_Field::getInstance('contractor_trucknumber', $module1);
if ($field21) {
    echo "<li>The contractor_trucknumber field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_CONTRACTORS_TRUCKNUMBER';
    $field21->name = 'contractor_trucknumber';
    $field21->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field21->column = 'contractor_trucknumber';   //  This will be the columnname in your database for the new field.
    $field21->columntype = 'VARCHAR(100)';
    $field21->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field21);
    //$field14->setRelatedModules(Array('Project'));
}

//end of block2 fields
echo "</ul>";
$block2->save($module1);
//end of block2 : LBL_CONTRACTORS_DETAILINFO

//start of block3 : LBL_CONTRACTORS_LICENSEINFORMATION
$block3 = Vtiger_Block::getInstance('LBL_CONTRACTORS_LICENSEINFORMATION', $module1);
if ($block3) {
    echo "<h3>The LBL_CONTRACTORS_LICENSEINFORMATION block already exists</h3><br>";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_CONTRACTORS_LICENSEINFORMATION';
    $module1->addBlock($block3);
}
//start of block3 fields
echo "<ul>";

$field22 = Vtiger_Field::getInstance('contractor_dlnumber', $module1);
if ($field22) {
    echo "<li>The contractor_dlnumber field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_CONTRACTORS_DLNUMBER';
    $field22->name = 'contractor_dlnumber';
    $field22->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field22->column = 'contractor_dlnumber';   //  This will be the columnname in your database for the new field.
    $field22->columntype = 'VARCHAR(100)';
    $field22->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field22->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field22);
}
$field23 = Vtiger_Field::getInstance('contractor_dlstate', $module1);
if ($field23) {
    echo "<li>The contractor_dlstate field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_CONTRACTORS_DLSTATE';
    $field23->name = 'contractor_dlstate';
    $field23->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field23->column = 'contractor_dlstate';   //  This will be the columnname in your database for the new field.
    $field23->columntype = 'VARCHAR(2)';
    $field23->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field23->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field23);
}
$field24 = Vtiger_Field::getInstance('contractor_dledate', $module1);
if ($field24) {
    echo "<li>The contractor_dledate field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_CONTRACTORS_DLEDATE';
    $field24->name = 'contractor_dledate';
    $field24->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field24->column = 'contractor_dledate';   //  This will be the columnname in your database for the new field.
    $field24->columntype = 'DATE';
    $field24->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field24->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field24);
}
$field25 = Vtiger_Field::getInstance('contractor_dlclass', $module1);
if ($field25) {
    echo "<li>The contractor_dlclass field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CONTRACTORS_DLCLASS';
    $field25->name = 'contractor_dlclass';
    $field25->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field25->column = 'contractor_dlclass';   //  This will be the columnname in your database for the new field.
    $field25->columntype = 'VARCHAR(100)';
    $field25->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field25->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field25);
}
//end of block3 fields
echo "</ul>";
$block3->save($module1);
//end of block3 : LBL_CONTRACTORS_LICENSEINFORMATION

//start of block4 : LBL_CONTRACTORS_ECINFO
$block4 = Vtiger_Block::getInstance('LBL_CONTRACTORS_ECINFO', $module1);
if ($block4) {
    echo "<h3>The LBL_CONTRACTORS_ECINFO block already exists</h3><br>";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_CONTRACTORS_ECINFO';
    $module1->addBlock($block4);
}
$field26 = Vtiger_Field::getInstance('contractor_efname', $module1);
if ($field26) {
    echo "<li>The contractor_efname field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CONTRACTORS_EFNAME';
    $field26->name = 'contractor_efname';
    $field26->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field26->column = 'contractor_efname';   //  This will be the columnname in your database for the new field.
    $field26->columntype = 'VARCHAR(100)';
    $field26->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field26->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field26);
}
$field27 = Vtiger_Field::getInstance('contractor_elname', $module1);
if ($field27) {
    echo "<li>The contractor_elname field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_CONTRACTORS_ELNAME';
    $field27->name = 'contractor_elname';
    $field27->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field27->column = 'contractor_elname';   //  This will be the columnname in your database for the new field.
    $field27->columntype = 'VARCHAR(100)';
    $field27->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field27->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field27);
}
$field28 = Vtiger_Field::getInstance('contractor_relation', $module1);
if ($field28) {
    echo "<li>The contractor_relation field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CONTRACTORS_RELATION';
    $field28->name = 'contractor_relation';
    $field28->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field28->column = 'contractor_relation';   //  This will be the columnname in your database for the new field.
    $field28->columntype = 'VARCHAR(100)';
    $field28->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field28->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field28);
    $field28->setPicklistValues(array('Family', 'Friend', 'Spouse'));
}
$field29 = Vtiger_Field::getInstance('contractor_eemail', $module1);
if ($field29) {
    echo "<li>The contractor_eemail field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_CONTRACTORS_EEMAIL';
    $field29->name = 'contractor_eemail';
    $field29->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field29->column = 'contractor_eemail';   //  This will be the columnname in your database for the new field.
    $field29->columntype = 'VARCHAR(100)';
    $field29->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field29->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field29);
}
$field30 = Vtiger_Field::getInstance('contractor_ep1', $module1);
if ($field30) {
    echo "<li>The contractor_ep1 field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_CONTRACTORS_EP1';
    $field30->name = 'contractor_ep1';
    $field30->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field30->column = 'contractor_ep1';   //  This will be the columnname in your database for the new field.
    $field30->columntype = 'INT(20)';
    $field30->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field30->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field30);
}
$field31 = Vtiger_Field::getInstance('contractor_ep2', $module1);
if ($field31) {
    echo "<li>The contractor_ep2 field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_CONTRACTORS_EP2';
    $field31->name = 'contractor_ep2';
    $field31->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field31->column = 'contractor_ep2';   //  This will be the columnname in your database for the new field.
    $field31->columntype = 'INT(20)';
    $field31->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field31->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field31);
}
//end of block4 fields
echo "</ul>";
$block4->save($module1);

//end of block4 : LBL_CONTRACTORS_ECINFO

//start of block5 : LBL_CONTRACTORS_SAFETYINFO
$block5 = Vtiger_Block::getInstance('LBL_CONTRACTORS_SAFETYDETAILS', $module1);
if ($block5) {
    echo "<h3>The LBL_CONTRACTORS_SAFETYDETAILS block already exists</h3><br>";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_CONTRACTORS_SAFETYDETAILS';
    $module1->addBlock($block5);
}
$field32 = Vtiger_Field::getInstance('contractor_nphysical', $module1);
if ($field32) {
    echo "<li>The contractor_nphysical field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_CONTRACTORS_NPHYSICAL';
    $field32->name = 'contractor_nphysical';
    $field32->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field32->column = 'contractor_nphysical';   //  This will be the columnname in your database for the new field.
    $field32->columntype = 'DATE';
    $field32->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field32->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field32);
}
$field33 = Vtiger_Field::getInstance('contractor_lphysical', $module1);
if ($field33) {
    echo "<li>The contractor_lphysical field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_CONTRACTORS_LPHYSICAL';
    $field33->name = 'contractor_lphysical';
    $field33->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field33->column = 'contractor_lphysical';   //  This will be the columnname in your database for the new field.
    $field33->columntype = 'DATE';
    $field33->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field33->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field33);
}
$field34 = Vtiger_Field::getInstance('contractor_lbackground', $module1);
if ($field34) {
    echo "<li>The contractor_lbackground field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_CONTRACTORS_LBACKGROUND';
    $field34->name = 'contractor_lbackground';
    $field34->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field34->column = 'contractor_lbackground';   //  This will be the columnname in your database for the new field.
    $field34->columntype = 'DATE';
    $field34->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field34->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field34);
}
$field35 = Vtiger_Field::getInstance('contractor_nbackground', $module1);
if ($field35) {
    echo "<li>The contractor_nbackground field already exists</li><br>";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_CONTRACTORS_NBACKGROUND';
    $field35->name = 'contractor_nbackground';
    $field35->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field35->column = 'contractor_nbackground';   //  This will be the columnname in your database for the new field.
    $field35->columntype = 'DATE';
    $field35->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field35->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field35);
}
//end block5 fields
echo "</ul>";
$block5->save($module1);
//end of block5 : LBL_CONTRACTORS_SAFETYINFO

//start of block6 : LBL_CONTRACTORS_AVAILABILITY
$block6 = Vtiger_Block::getInstance('LBL_CONTRACTORS_AVAILABILITY', $module1);
if ($block6) {
    echo "<h3>The LBL_CONTRACTORS_AVAILABILITY block already exists</h3><br>";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_CONTRACTORS_AVAILABILITY';
    $module1->addBlock($block6);
}
$field36 = Vtiger_Field::getInstance('contractor_sunday', $module1);
if ($field36) {
    echo "<li>The contractor_sunday field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_CONTRACTORS_SUNDAY';
    $field36->name = 'contractor_sunday';
    $field36->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field36->column = 'contractor_sunday';   //  This will be the columnname in your database for the new field.
    $field36->columntype = 'VARCHAR(3)';
    $field36->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field36->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field36);
}
$field37 = Vtiger_Field::getInstance('contractor_sundayall', $module1);
if ($field37) {
    echo "<li>The contractor_sundayall field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_CONTRACTORS_SUNDAYALL';
    $field37->name = 'contractor_sundayall';
    $field37->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field37->column = 'contractor_sundayall';   //  This will be the columnname in your database for the new field.
    $field37->columntype = 'VARCHAR(3)';
    $field37->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field37->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field37);
}
$field38 = Vtiger_Field::getInstance('contractor_sundaystart', $module1);
if ($field38) {
    echo "<li>The contractor_sundaystart field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_CONTRACTORS_SUNDAYSTART';
    $field38->name = 'contractor_sundaystart';
    $field38->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field38->column = 'contractor_sundaystart';   //  This will be the columnname in your database for the new field.
    $field38->columntype = 'TIME';
    $field38->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field38->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field38);
}
$field39 = Vtiger_Field::getInstance('contractor_sundayend', $module1);
if ($field39) {
    echo "<li>The contractor_sundayend field already exists</li><br>";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_CONTRACTORS_SUNDAYEND';
    $field39->name = 'contractor_sundayend';
    $field39->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field39->column = 'contractor_sundayend';   //  This will be the columnname in your database for the new field.
    $field39->columntype = 'TIME';
    $field39->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field39->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field39);
}
$field40 = Vtiger_Field::getInstance('contractor_monday', $module1);
if ($field40) {
    echo "<li>The contractor_monday field already exists</li><br>";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_CONTRACTORS_MONDAY';
    $field40->name = 'contractor_monday';
    $field40->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field40->column = 'contractor_monday';   //  This will be the columnname in your database for the new field.
    $field40->columntype = 'VARCHAR(3)';
    $field40->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field40->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field40);
}
$field41 = Vtiger_Field::getInstance('contractor_mondayall', $module1);
if ($field41) {
    echo "<li>The contractor_mondayall field already exists</li><br>";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_CONTRACTORS_MONDAYALL';
    $field41->name = 'contractor_mondayall';
    $field41->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field41->column = 'contractor_mondayall';   //  This will be the columnname in your database for the new field.
    $field41->columntype = 'VARCHAR(3)';
    $field41->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field41->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field41);
}
$field42 = Vtiger_Field::getInstance('contractor_mondaystart', $module1);
if ($field42) {
    echo "<li>The contractor_mondaystart field already exists</li><br>";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_CONTRACTORS_MONDAYSTART';
    $field42->name = 'contractor_mondaystart';
    $field42->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field42->column = 'contractor_mondaystart';   //  This will be the columnname in your database for the new field.
    $field42->columntype = 'TIME';
    $field42->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field42->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field42);
}
$field43 = Vtiger_Field::getInstance('contractor_mondayend', $module1);
if ($field43) {
    echo "<li>The contractor_mondayend field already exists</li><br>";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_CONTRACTORS_MONDAYEND';
    $field43->name = 'contractor_mondayend';
    $field43->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field43->column = 'contractor_mondayend';   //  This will be the columnname in your database for the new field.
    $field43->columntype = 'TIME';
    $field43->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field43->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field43);
}
$field44 = Vtiger_Field::getInstance('contractor_tuesday', $module1);
if ($field44) {
    echo "<li>The contractor_tuesday field already exists</li><br>";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_CONTRACTORS_TUESDAY';
    $field44->name = 'contractor_tuesday';
    $field44->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field44->column = 'contractor_tuesday';   //  This will be the columnname in your database for the new field.
    $field44->columntype = 'VARCHAR(3)';
    $field44->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field44->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field44);
}
$field45 = Vtiger_Field::getInstance('contractor_tuesdayall', $module1);
if ($field45) {
    echo "<li>The contractor_tuesdayall field already exists</li><br>";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_CONTRACTORS_TUESDAYALL';
    $field45->name = 'contractor_tuesdayall';
    $field45->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field45->column = 'contractor_tuesdayall';   //  This will be the columnname in your database for the new field.
    $field45->columntype = 'VARCHAR(3)';
    $field45->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field45->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field45);
}
$field46 = Vtiger_Field::getInstance('contractor_tuesdaystart', $module1);
if ($field46) {
    echo "<li>The contractor_tuesdaystart field already exists</li><br>";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_CONTRACTORS_TUESDAYSTART';
    $field46->name = 'contractor_tuesdaystart';
    $field46->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field46->column = 'contractor_tuesdaystart';   //  This will be the columnname in your database for the new field.
    $field46->columntype = 'TIME';
    $field46->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field46->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field46);
}
$field47 = Vtiger_Field::getInstance('contractor_tuesdayend', $module1);
if ($field47) {
    echo "<li>The contractor_tuesdayend field already exists</li><br>";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_CONTRACTORS_TUESDAYEND';
    $field47->name = 'contractor_tuesdayend';
    $field47->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field47->column = 'contractor_tuesdayend';   //  This will be the columnname in your database for the new field.
    $field47->columntype = 'TIME';
    $field47->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field47->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field47);
}
$field48 = Vtiger_Field::getInstance('contractor_wednesday', $module1);
if ($field48) {
    echo "<li>The contractor_wednesday field already exists</li><br>";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_CONTRACTORS_WEDNESDAY';
    $field48->name = 'contractor_wednesday';
    $field48->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field48->column = 'contractor_wednesday';   //  This will be the columnname in your database for the new field.
    $field48->columntype = 'VARCHAR(3)';
    $field48->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field48->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field48);
}
$field49 = Vtiger_Field::getInstance('contractor_wednesdayall', $module1);
if ($field49) {
    echo "<li>The contractor_wednesdayall field already exists</li><br>";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_CONTRACTORS_WEDNESDAYALL';
    $field49->name = 'contractor_wednesdayall';
    $field49->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field49->column = 'contractor_wednesdayall';   //  This will be the columnname in your database for the new field.
    $field49->columntype = 'VARCHAR(3)';
    $field49->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field49->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field49);
}
$field50 = Vtiger_Field::getInstance('contractor_wednesdaystart', $module1);
if ($field50) {
    echo "<li>The contractor_wednesdaystart field already exists</li><br>";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_CONTRACTORS_WEDNESDAYSTART';
    $field50->name = 'contractor_wednesdaystart';
    $field50->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field50->column = 'contractor_wednesdaystart';   //  This will be the columnname in your database for the new field.
    $field50->columntype = 'TIME';
    $field50->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field50->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field50);
}
$field51 = Vtiger_Field::getInstance('contractor_wednesdayend', $module1);
if ($field51) {
    echo "<li>The contractor_wednesdayend field already exists</li><br>";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_CONTRACTORS_WEDNESDAYEND';
    $field51->name = 'contractor_wednesdayend';
    $field51->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field51->column = 'contractor_wednesdayend';   //  This will be the columnname in your database for the new field.
    $field51->columntype = 'TIME';
    $field51->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field51->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field51);
}
$field52 = Vtiger_Field::getInstance('contractor_thursday', $module1);
if ($field52) {
    echo "<li>The contractor_thursday field already exists</li><br>";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_CONTRACTORS_THURSDAY';
    $field52->name = 'contractor_thursday';
    $field52->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field52->column = 'contractor_thursday';   //  This will be the columnname in your database for the new field.
    $field52->columntype = 'VARCHAR(3)';
    $field52->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field52->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field52);
}
$field53 = Vtiger_Field::getInstance('contractor_thursdayall', $module1);
if ($field53) {
    echo "<li>The contractor_thursdayall field already exists</li><br>";
} else {
    $field53 = new Vtiger_Field();
    $field53->label = 'LBL_CONTRACTORS_THURSDAYALL';
    $field53->name = 'contractor_thursdayall';
    $field53->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field53->column = 'contractor_thursdayall';   //  This will be the columnname in your database for the new field.
    $field53->columntype = 'VARCHAR(3)';
    $field53->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field53->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field53);
}
$field54 = Vtiger_Field::getInstance('contractor_thursdaystart', $module1);
if ($field54) {
    echo "<li>The contractor_thursdaystart field already exists</li><br>";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_CONTRACTORS_THURSDAYSTART';
    $field54->name = 'contractor_thursdaystart';
    $field54->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field54->column = 'contractor_thursdaystart';   //  This will be the columnname in your database for the new field.
    $field54->columntype = 'TIME';
    $field54->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field54->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field54);
}
$field55 = Vtiger_Field::getInstance('contractor_thursdayend', $module1);
if ($field55) {
    echo "<li>The contractor_thursdayend field already exists</li><br>";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'LBL_CONTRACTORS_THURSDAYEND';
    $field55->name = 'contractor_thursdayend';
    $field55->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field55->column = 'contractor_thursdayend';   //  This will be the columnname in your database for the new field.
    $field55->columntype = 'TIME';
    $field55->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field55->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field55);
}
$field56 = Vtiger_Field::getInstance('contractor_friday', $module1);
if ($field56) {
    echo "<li>The contractor_friday field already exists</li><br>";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_CONTRACTORS_FRIDAY';
    $field56->name = 'contractor_friday';
    $field56->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field56->column = 'contractor_friday';   //  This will be the columnname in your database for the new field.
    $field56->columntype = 'VARCHAR(3)';
    $field56->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field56->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field56);
}
$field57 = Vtiger_Field::getInstance('contractor_fridayall', $module1);
if ($field57) {
    echo "<li>The contractor_fridayall field already exists</li><br>";
} else {
    $field57 = new Vtiger_Field();
    $field57->label = 'LBL_CONTRACTORS_FRIDAYALL';
    $field57->name = 'contractor_fridayall';
    $field57->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field57->column = 'contractor_fridayall';   //  This will be the columnname in your database for the new field.
    $field57->columntype = 'VARCHAR(3)';
    $field57->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field57->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field57);
}
$field58 = Vtiger_Field::getInstance('contractor_fridaystart', $module1);
if ($field58) {
    echo "<li>The contractor_fridaystart field already exists</li><br>";
} else {
    $field58 = new Vtiger_Field();
    $field58->label = 'LBL_CONTRACTORS_FRIDAYSTART';
    $field58->name = 'contractor_fridaystart';
    $field58->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field58->column = 'contractor_fridaystart';   //  This will be the columnname in your database for the new field.
    $field58->columntype = 'TIME';
    $field58->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field58->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field58);
}
$field59 = Vtiger_Field::getInstance('contractor_fridayend', $module1);
if ($field59) {
    echo "<li>The contractor_fridayend field already exists</li><br>";
} else {
    $field59 = new Vtiger_Field();
    $field59->label = 'LBL_CONTRACTORS_FRIDAYEND';
    $field59->name = 'contractor_fridayend';
    $field59->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field59->column = 'contractor_fridayend';   //  This will be the columnname in your database for the new field.
    $field59->columntype = 'TIME';
    $field59->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field59->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field59);
}
$field60 = Vtiger_Field::getInstance('contractor_saturday', $module1);
if ($field60) {
    echo "<li>The contractor_saturday field already exists</li><br>";
} else {
    $field60 = new Vtiger_Field();
    $field60->label = 'LBL_CONTRACTORS_SATURDAY';
    $field60->name = 'contractor_saturday';
    $field60->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field60->column = 'contractor_saturday';   //  This will be the columnname in your database for the new field.
    $field60->columntype = 'VARCHAR(3)';
    $field60->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field60->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field60);
}
$field61 = Vtiger_Field::getInstance('contractor_saturdayall', $module1);
if ($field61) {
    echo "<li>The contractor_saturdayall field already exists</li><br>";
} else {
    $field61 = new Vtiger_Field();
    $field61->label = 'LBL_CONTRACTORS_SATURDAYALL';
    $field61->name = 'contractor_saturdayall';
    $field61->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field61->column = 'contractor_saturdayall';   //  This will be the columnname in your database for the new field.
    $field61->columntype = 'VARCHAR(3)';
    $field61->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field61->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field61);
}
$field62 = Vtiger_Field::getInstance('contractor_saturdaystart', $module1);
if ($field62) {
    echo "<li>The contractor_saturdaystart field already exists</li><br>";
} else {
    $field62 = new Vtiger_Field();
    $field62->label = 'LBL_CONTRACTORS_SATURDAYSTART';
    $field62->name = 'contractor_saturdaystart';
    $field62->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field62->column = 'contractor_saturdaystart';   //  This will be the columnname in your database for the new field.
    $field62->columntype = 'TIME';
    $field62->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field62->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field62);
}
$field63 = Vtiger_Field::getInstance('contractor_saturdayend', $module1);
if ($field63) {
    echo "<li>The contractor_saturdayend field already exists</li><br>";
} else {
    $field63 = new Vtiger_Field();
    $field63->label = 'LBL_CONTRACTORS_SATURDAYEND';
    $field63->name = 'contractor_saturdayend';
    $field63->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
    $field63->column = 'contractor_saturdayend';   //  This will be the columnname in your database for the new field.
    $field63->columntype = 'TIME';
    $field63->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field63->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field63);
}
//end of block6 fields
echo "</ul>";
$block6->save($module1);

//end of block6 : LBL_CONTRACTORS_AVAILABILITY

//start of block7 : LBL_CONTRACTORS_RECORDUPDATE
$block7 = Vtiger_Block::getInstance('LBL_CONTRACTORS_RECORDUPDATE', $module1);
if ($block7) {
    echo "<h3>The LBL_CONTRACTORS_RECORDUPDATE block already exists</h3><br>";
} else {
    $block7 = new Vtiger_Block();
    $block7->label = 'LBL_CONTRACTORS_RECORDUPDATE';
    $module1->addBlock($block7);
}
//start block7 fields
echo "<ul>";
$field64 = Vtiger_Field::getInstance('createdtime', $module1);
if ($field64) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field64 = new Vtiger_Field();
    $field64->label = 'LBL_CONTRACTORS_CREATEDTIME';
    $field64->name = 'createdtime';
    $field64->table = 'vtiger_crmentity';
    $field64->column = 'createdtime';
    $field64->columntype = 'datetime';
    $field64->uitype = 70;
    $field64->typeofdata = 'T~O';
    
    $block7->addField($field64);
}
$field65 = Vtiger_Field::getInstance('modifiedtime', $module1);
if ($field65) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_CONTRACTORS_MODIFIEDTIME';
    $field65->name = 'modifiedtime';
    $field65->table = 'vtiger_crmentity';
    $field65->column = 'modifiedtime';
    $field65->columntype = 'datetime';
    $field65->uitype = 70;
    $field65->typeofdata = 'T~O';
    
    $block7->addField($field65);
}
//end block7 fields
echo "</ul>";
$block7->save($module1);
//end of block7 : LBL_CONTRACTORS_RECORDUPDATE

//add ModComments Widget
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Contractors'));
ModComments::removeWidgetFrom('Contractors');
ModComments::addWidgetTo('Contractors');
//end ModComments Widget
//add ModTracker Widget
ModTracker::enableTrackingForModule($module1->id);

if ($contractorsIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field2)->addField($field1, 1)->addField($field12, 2);

    $module1->setDefaultSharing();
    $module1->initWebservice();
    
    $module2 = Vtiger_Module::getInstance('Accidents');
    if ($module2) {
        $module1->setRelatedList($module2, 'Accidents', array('ADD', 'SELECT'), 'get_related_list');
        echo "<h3>Set Related List of Contractors -> Accidents</h3>";
    } else {
        echo "<h3>Could not set Related List of Contractors -> Accidents, as Accidents does not exist</h3>";
    }
    $module3 = Vtiger_Module::getInstance('TimeOff');
    if ($module3) {
        $module1->setRelatedList($module3, 'Time Off', array('ADD', 'SELECT'), 'get_related_list');
        echo "<h3>Set Related List of Contractors -> TimeOff</h3>";
    } else {
        echo "<h3>Could not set Related List of Contractors -> TimeOff, as TimeOff does not exist</h3>";
    }
}
