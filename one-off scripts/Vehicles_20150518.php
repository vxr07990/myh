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
//required for modcomments
include_once('modules/ModComments/ModComments.php');
//required for updates tracker
include_once('modules/ModTracker/ModTracker.php');
$vehiclesIsNew = false;

$module1 = Vtiger_Module::getInstance('Vehicles'); // The module1 your blocks and fields will be in.
if ($module1) {
    echo "<h2>Updating Vehicles Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Vehicles';
    $module1->save();
    echo "<h2>Creating Module Vehicles and Updating Fields</h2><br>";
    $module1->initTables();
}
//start block1 : LBL_VEHICLES_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $module1);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_INFORMATION';
    $module1->addBlock($block1);
    $vehiclesIsNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('name', $module1);
if ($field1) {
    echo "<li>The name field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VEHICLES_NAME';
    $field1->name = 'name';
    $field1->table = 'vtiger_vehicles';
    $field1->column = 'name';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~O';
    
    $block1->addField($field1);
}

$field2 = Vtiger_Field::getInstance('vehicle_number', $module1);
if ($field2) {
    echo "<li>The vehicle_number field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_VEHICLES_VNUMBER';
    $field2->name = 'vehicle_number';
    $field2->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field2->column = 'vehicle_number';   //  This will be the columnname in your database for the new field.
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('vehicle_type', $module1);
if ($field3) {
    echo "<li>The vehicle_type field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VEHICLES_TYPE';
    $field3->name = 'vehicle_type';
    $field3->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field3->column = 'vehicle_type';   //  This will be the columnname in your database for the new field.
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field3);
    $field3->setPicklistValues(array('Straight Truck', 'Pack Van', 'Tractor', '48 Trailer', '53 Trailer', 'Vault Trailer', 'Flatbed Trailer'));
}
$field4 = Vtiger_Field::getInstance('vehicle_status', $module1);
if ($field4) {
    echo "<li>The vehicle_status field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLES_STATUS';
    $field4->name = 'vehicle_status';
    $field4->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field4->column = 'vehicle_status';   //  This will be the columnname in your database for the new field.
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field4);
    $field4->setPicklistValues(array('Active', 'Disposed', 'Out of Service'));
}
$field5 = Vtiger_Field::getInstance('vehicle_milesnum', $module1);
if ($field5) {
    echo "<li>The vehicle_milesnum field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_VEHICLES_MILESNUM';
    $field5->name = 'vehicle_milesnum';
    $field5->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field5->column = 'vehicle_milesnum';   //  This will be the columnname in your database for the new field.
    $field5->columntype = 'INT(20)';
    $field5->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('vehicle_milesdate', $module1);
if ($field6) {
    echo "<li>The vehicle_milesdate field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_VEHICLES_MILESDATE';
    $field6->name = 'vehicle_milesdate';
    $field6->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'vehicle_milesdate';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'DATE';
    $field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
}
//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_VEHICLES_INFORMATION

//start block2 : LBL_VEHICLES_SPECS
$block2 = Vtiger_Block::getInstance('LBL_VEHICLES_SPECS', $module1);
if ($block2) {
    echo "<h3>The LBL_VEHICLES_SPECS block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_VEHICLES_SPECS';
    $module1->addBlock($block2);
}
echo "<ul>";
//start block2 fields

$field7 = Vtiger_Field::getInstance('vehicle_cubec', $module1);
if ($field7) {
    echo "<li>The vehicle_cubec field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_VEHICLES_CUBECAPACITY';
    $field7->name = 'vehicle_cubec';
    $field7->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'vehicle_cubec';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'INT(20)';
    $field7->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field7);
}
$field8 = Vtiger_Field::getInstance('vehicle_vin', $module1);
if ($field8) {
    echo "<li>The vehicle_vin field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VEHICLES_VIN';
    $field8->name = 'vehicle_vin';
    $field8->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field8->column = 'vehicle_vin';   //  This will be the columnname in your database for the new field.
    $field8->columntype = 'VARCHAR(100)';
    $field8->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field8);
}
$field9 = Vtiger_Field::getInstance('vehicle_length', $module1);
if ($field9) {
    echo "<li>The vehicle_length field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_VEHICLES_LENGTH';
    $field9->name = 'vehicle_length';
    $field9->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field9->column = 'vehicle_length';   //  This will be the columnname in your database for the new field.
    $field9->columntype = 'INT(20)';
    $field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field9);
}
$field10 = Vtiger_Field::getInstance('vehicle_year', $module1);
if ($field10) {
    echo "<li>The vehicle_year field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_VEHICLES_YEAR';
    $field10->name = 'vehicle_year';
    $field10->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'vehicle_year';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'INT(20)';
    $field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field10);
}
$field11 = Vtiger_Field::getInstance('vehicle_weight', $module1);
if ($field11) {
    echo "<li>The vehicle_weight field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_VEHICLES_WEIGHT';
    $field11->name = 'vehicle_weight';
    $field11->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'vehicle_weight';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'INT(20)';
    $field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field11);
}
$field12 = Vtiger_Field::getInstance('vehicle_model', $module1);
if ($field12) {
    echo "<li>The vehicle_model field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_VEHICLES_MODEL';
    $field12->name = 'vehicle_model';
    $field12->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field12->column = 'vehicle_model';   //  This will be the columnname in your database for the new field.
    $field12->columntype = 'VARCHAR(100)';
    $field12->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field12);
}
$field13 = Vtiger_Field::getInstance('vehicle_height', $module1);
if ($field13) {
    echo "<li>The vehicle_height field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_VEHICLES_HEIGHT';
    $field13->name = 'vehicle_height';
    $field13->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field13->column = 'vehicle_height';   //  This will be the columnname in your database for the new field.
    $field13->columntype = 'INT(20)';
    $field13->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field13);
}
$field14 = Vtiger_Field::getInstance('vehicle_adate', $module1);
if ($field14) {
    echo "<li>The vehicle_adate field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_VEHICLES_ADATE';
    $field14->name = 'vehicle_adate';
    $field14->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field14->column = 'vehicle_adate';   //  This will be the columnname in your database for the new field.
    $field14->columntype = 'DATE';
    $field14->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field14->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field14);
}
$field15 = Vtiger_Field::getInstance('vehicle_ddate', $module1);
if ($field15) {
    echo "<li>The vehicle_ddate field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_VEHICLES_DDATE';
    $field15->name = 'vehicle_ddate';
    $field15->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field15->column = 'vehicle_ddate';   //  This will be the columnname in your database for the new field.
    $field15->columntype = 'DATE';
    $field15->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field15);
}
//end block2 fields
echo "</ul>";
$block2->save($module1);
//end block2 : LBL_VEHICLES_SPECS

//start block3: LVL_VEHICLES_RECORDUPDATE
$block3 = Vtiger_Block::getInstance('LBL_VEHICLES_RECORDUPDATE', $module1);
if ($block3) {
    echo "<h3>The LBL_VEHICLES_RECORDUPDATE block already exists</h3><br>";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_VEHICLES_RECORDUPDATE';
    $module1->addBlock($block3);
}
//start block3 fields
echo "<ul>";
$field16 = Vtiger_Field::getInstance('CreatedTime', $module1);
if ($field16) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_VEHICLES_CREATEDTIME';
    $field16->name = 'createdtime';
    $field16->table = 'vtiger_crmentity';
    $field16->column = 'createdtime';
    $field16->columntype = 'datetime';
    $field16->uitype = 70;
    $field16->typeofdata = 'T~O';
    
    $block3->addField($field16);
}
$field17 = Vtiger_Field::getInstance('ModifiedTime', $module1);
if ($field17) {
    echo "<li>The ModifiedTime field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_VEHICLES_MODIFIEDTIME';
    $field17->name = 'modifiedtime';
    $field17->table = 'vtiger_crmentity';
    $field17->column = 'modifiedtime';
    $field17->columntype = 'datetime';
    $field17->uitype = 70;
    $field17->typeofdata = 'T~O';
    
    $block3->addField($field17);
}
//end block3 fields
echo "</ul>";
$block3->save($module1);
//end block3 : LBL_VEHICLES_RECORDUPDATE

//add ModComments Widget
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Vehicles'));
ModComments::removeWidgetFrom('Vehicles');
ModComments::addWidgetTo('Vehicles');
//end ModComments Widget
//add ModTracker Widget
ModTracker::enableTrackingForModule($module1->id);

if ($vehiclesIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field2)->addField($field21, 1)->addField($field3, 2)->addField($field4, 3);

    $module1->setDefaultSharing();
    $module1->initWebservice();
}
