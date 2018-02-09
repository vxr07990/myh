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
$storageIsNew = false;

$module1 = Vtiger_Module::getInstance('Vehicles');
if ($module1) {
    echo "<h2>Updating Vehicles Fields</h2><br>";
}

$block2 = Vtiger_Block::getInstance('LBL_VEHICLES_SPECS', $module1);
if ($block2) {
    echo "<h3>The LBL_VEHICLES_SPECS block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_VEHICLES_SPECS';
    $module1->addBlock($block2);
    $storageIsNew = true;
}
 
echo "<ul>";

$field1 = Vtiger_Field::getInstance('vehicle_tareweight', $module1);
if ($field1) {
    echo "<li>The vehicle_tareweight field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VEHICLES_TAREWEIGHT';
    $field1->name = 'vehicle_tareweight';
    $field1->table = 'vtiger_vehicles';
    $field1->column = 'vehicle_tareweight';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $block2->addField($field1);
    $module1->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('vehicle_feetcapacity', $module1);
if ($field2) {
    echo "<li>The vehicle_feetcapacity field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_VEHICLES_FEETCAPACITY';
    $field2->name = 'vehicle_feetcapacity';
    $field2->table = 'vtiger_vehicles';
    $field2->column = 'vehicle_feetcapacity';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $block2->addField($field2);
}

$field3 = Vtiger_Field::getInstance('vehicle_outsideheight', $module1);
if ($field3) {
    echo "<li>The vehicle_outsideheight field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VEHICLES_OUTSIDEHEIGHT';
    $field3->name = 'vehicle_outsideheight';
    $field3->table = 'vtiger_vehicles';
    $field3->column = 'vehicle_outsideheight';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $block2->addField($field3);
}

$field4     = Vtiger_Field::getInstance('vehicle_insideheight', $module1);
if ($field4) {
    echo "<li>The vehicle_insideheight field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLES_INSIDEHEIGHT';
    $field4->name = 'vehicle_insideheight';
    $field4->table = 'vtiger_vehicles';
    $field4->column = 'vehicle_insideheight';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';
    $block2->addField($field4);
}

$field5     = Vtiger_Field::getInstance('vehicle_dropboxcubes', $module1);
if ($field5) {
    echo "<li>The vehicle_insideheight field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_VEHICLES_DROPBOXCUBES';
    $field5->name = 'vehicle_dropboxcubes';
    $field5->table = 'vtiger_vehicles';
    $field5->column = 'vehicle_dropboxcubes';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $block2->addField($field5);
}

$field6 = Vtiger_Field::getInstance('vehicle_grossweight', $module1);
if ($field6) {
    echo "<li>The vehicle_grossweight field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_VEHICLES_GROSSWEIGHT';
    $field6->name = 'vehicle_grossweight';
    $field6->table = 'vtiger_vehicles';
    $field6->column = 'vehicle_grossweight';
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';
    $block2->addField($field6);
}

$field7 = Vtiger_Field::getInstance('vehicle_wheelbasslenth', $module1);
if ($field7) {
    echo "<li>The vehicle_wheelbasslenth field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_VEHICLES_WHEELBASELENGTH';
    $field7->name = 'vehicle_wheelbasslength';
    $field7->table = 'vtiger_vehicles';
    $field7->column = 'vehicle_wheelbasslength';
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $block2->addField($field7);
}

$field8 = Vtiger_Field::getInstance('vehicle_length', $module1);
if ($field8) {
    echo "<li>The vehicle_wheelbasslenth field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VEHICLES_LENGTH';
    $field8->name = 'vehicle_length';
    $field8->table = 'vtiger_vehicles';
    $field8->column = 'vehicle_length';
    $field8->columntype = 'VARCHAR(255)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    $block2->addField($field8);
}

$field8 = Vtiger_Field::getInstance('vehicle_suspensiontype', $module1);
if ($field8) {
    echo "<li>The vehicle_suspensiontype field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VEHICLES_SUSPENSIONTYPE';
    $field8->name = 'vehicle_suspensiontype';
    $field8->table = 'vtiger_vehicles';
    $field8->column = 'vehicle_suspensiontype';
    $field8->columntype = 'VARCHAR(255)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    $block2->addField($field8);
}


$field10 = Vtiger_Field::getInstance('vehicle_fuelcapacitytank', $module1);
if ($field10) {
    echo "<li>The vehicle_wheelbasslenth field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_VEHICLES_FUELTANKCAPACITY';
    $field10->name = 'vehicle_fuelcapacitytank';
    $field10->table = 'vtiger_vehicles';
    $field10->column = 'vehicle_fuelcapacitytank';
    $field10->columntype = 'VARCHAR(255)';
    $field10->uitype = 1;
    $field10->typeofdata = 'V~O';
    $block2->addField($field10);
}

$field11 = Vtiger_Field::getInstance('vehicle_fueltype', $module1);
if ($field11) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_VEHICLES_FUELTYPE';
    $field11->name = 'vehicle_fueltype';
    $field11->table = 'vtiger_vehicles';
    $field11->column = 'vehicle_fueltype';
    $field11->columntype = 'VARCHAR(255)';
    $field11->uitype = 16;
    $field11->typeofdata = 'V~O';
    $field11->setPicklistValues(array('Diesel', 'Unleaded'));
    $block2->addField($field11);
}

$field12 = Vtiger_Field::getInstance('vehicle_gvr', $module1);
if ($field12) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_VEHICLES_GVWR';
    $field12->name = 'vehicle_gvr';
    $field12->table = 'vtiger_vehicles';
    $field12->column = 'vehicle_gvr';
    $field12->columntype = 'VARCHAR(255)';
    $field12->uitype = 16;
    $field12->typeofdata = 'V~O';
    $field12->setPicklistValues(array('Less than 9500#', '9500# to 26000#', '26001# or more'));
    $block2->addField($field12);
}

$field13 = Vtiger_Field::getInstance('vehicle_inspectioncycle', $module1);
if ($field13) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_VEHICLES_INSPECTIONCYCLE';
    $field13->name = 'vehicle_inspectioncycle';
    $field13->table = 'vtiger_vehicles';
    $field13->column = 'vehicle_inspectioncycle';
    $field13->columntype = 'VARCHAR(255)';
    $field13->uitype = 16;
    $field13->typeofdata = 'V~O';
    $field13->setPicklistValues(array('Annual – Annual 4 wheel', 'CA Periodic – California Periodi', 'New Registration', ' Periodic'));
    $block2->addField($field13);
}

$field14 = Vtiger_Field::getInstance('vehicle_tiresize', $module1);
if ($field14) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_VEHICLES_TIRESIZE';
    $field14->name = 'vehicle_tiresize';
    $field14->table = 'vtiger_vehicles';
    $field14->column = 'vehicle_tiresize';
    $field14->columntype = 'VARCHAR(255)';
    $field14->uitype = 1;
    $field14->typeofdata = 'V~O';
    $block2->addField($field14);
}

$field15 = Vtiger_Field::getInstance('vehicle_carb', $module1);
if ($field15) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_VEHICLES_CARB';
    $field15->name = 'vehicle_carb';
    $field15->table = 'vtiger_vehicles';
    $field15->column = 'vehicle_carb';
    $field15->columntype = 'VARCHAR(255)';
    $field15->uitype = 16;
    $field15->typeofdata = 'V~O';
    $field15->setPicklistValues(array('Yes', 'No'));
    $block2->addField($field15);
}


$field17 = Vtiger_Field::getInstance('vehicle_purchaseprice', $module1);
if ($field17) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_VEHICLES_PURCHASEPRICE';
    $field17->name = 'vehicle_purchaseprice';
    $field17->table = 'vtiger_vehicles';
    $field17->column = 'vehicle_purchaseprice';
    $field17->columntype = 'VARCHAR(255)';
    $field17->uitype = 1;
    $field17->typeofdata = 'V~O';
    $block2->addField($field17);
}

$field18 = Vtiger_Field::getInstance('vehicle_airsuspension', $module1);
if ($field18) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_VEHICLES_AIRSUSPENSION';
    $field18->name = 'vehicle_airsuspension';
    $field18->table = 'vtiger_vehicles';
    $field18->column = 'vehicle_airsuspension';
    $field18->columntype = 'VARCHAR(255)';
    $field18->uitype = 16;
    $field18->typeofdata = 'V~O';
    $field18->setPicklistValues(array('Yes', 'No'));
    $block2->addField($field18);
}

$field19 = Vtiger_Field::getInstance('vehicle_platetype', $module1);
if ($field19) {
    echo "<li>The vehicle_fueltype field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_VEHICLES_PLATETYPE';
    $field19->name = 'vehicle_platetype';
    $field19->table = 'vtiger_vehicles';
    $field19->column = 'vehicle_platetype';
    $field19->columntype = 'VARCHAR(255)';
    $field19    ->uitype = 16;
    $field19->typeofdata = 'V~O';
    $field19->setPicklistValues(array('IRP', 'Base'));
    $block2->addField($field19);
}

$field1 = Vtiger_Field::getInstance('vechiles_unit', $module1);
if ($field1) {
    $module1->setEntityIdentifier($field1);
}


global $adb;
$adb->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldname=? AND tabid=?', array('vehicle_number', $module1->id));
$adb->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldname=? AND tabid=?', array('vehicle_milesdate', $module1->id));


$docsModuleInstance = Vtiger_Module::getInstance('Documents');

$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($module1->id, $docsModuleInstance->id));

if ($result && $adb->num_rows($result) == 0) {
    $module1->setRelatedList($docsModuleInstance, 'Documents', array('ADD'), 'get_attachments');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";