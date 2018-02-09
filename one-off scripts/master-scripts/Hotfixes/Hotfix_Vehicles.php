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



$moduleInstance = Vtiger_Module::getInstance('Vehicles'); // The module1 your blocks and fields will be in.

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_INFORMATION';
    $moduleInstance->addBlock($block1);
    $vehiclesIsNew = true;
}

$field00 = Vtiger_Field::getInstance('vechiles_no', $moduleInstance);
if (!$field00) {
    $field00 = new Vtiger_Field();
    $field00->label = 'LBL_VEHICLES_NUMBER';
    $field00->name = 'vechiles_no';
    $field00->table = 'vtiger_vehicles';
    $field00->column = 'vechiles_no';
    $field00->columntype = 'VARCHAR(50)';
    $field00->uitype = 4;
    $field00->typeofdata = 'V~0';

    $block1->addField($field00);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Vehicles', 'VEH', 1, 1, 1));
}


$field01 = Vtiger_Field::getInstance('vechiles_unit', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_VEHICLES_UNIT';
    $field01->name = 'vechiles_unit';
    $field01->table = 'vtiger_vehicles';
    $field01->column = 'vechiles_unit';
    $field01->columntype = 'VARCHAR(50)';
    $field01->uitype = 2;
    $field01->typeofdata = 'V~0';

    $block1->addField($field01);
}

$field02 = Vtiger_Field::getInstance('vehicles_reg_date', $moduleInstance);
if (!$field02) {
    $field02 = new Vtiger_Field();
    $field02->label = 'LBL_VEHICLES_REG_DATE';
    $field02->name = 'vehicles_reg_date';
    $field02->table = 'vtiger_vehicles';
    $field02->column = 'vehicles_reg_date';
    $field02->columntype = 'DATE';
    $field02->uitype = 5;
    $field02->typeofdata = 'D~O';

    $block1->addField($field02);
}

$field03 = Vtiger_Field::getInstance('vehicles_agent_no', $moduleInstance);
if (!$field03) {
    $field03 = new Vtiger_Field();
    $field03->label = 'LBL_VEHICLES_AGENT_NO';
    $field03->name = 'vehicles_agent_no';
    $field03->table = 'vtiger_vehicles';
    $field03->column = 'vehicles_agent_no';
    $field03->columntype = 'VARCHAR(50)';
    $field03->uitype = 2;
    $field03->typeofdata = 'V~O';

    $block1->addField($field03);
}

$field04 = Vtiger_Field::getInstance('vechiles_datequalify', $moduleInstance);
if (!$field04) {
    $field04 = new Vtiger_Field();
    $field04->label = 'LBL_VEHICLES_DATE_QUALIFY';
    $field04->name = 'vechiles_datequalify';
    $field04->table = 'vtiger_vehicles';
    $field04->column = 'vechiles_datequalify';
    $field04->columntype = 'DATE';
    $field04->uitype = 5;
    $field04->typeofdata = 'D~O';

    $block1->addField($field04);
}

$field2 = Vtiger_Field::getInstance('vehicle_number', $moduleInstance);
if (!$field2) {
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

$moduleInstance->setEntityIdentifier($field2);


$field3 = Vtiger_Field::getInstance('vehicle_type', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VEHICLES_TYPE';
    $field3->name = 'vehicle_type';
    $field3->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field3->column = 'vehicle_type';   //  This will be the columnname in your database for the new field.
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field3);
    $field3->setPicklistValues(array('Tractor', 'Trailer - Drop Frame', 'Trailer - Freight', 'Trailer - Pup', 'Trailer - Vault', 'Straight Truck', 'Refrigerated Straight Truck', 'Cube Van', 'Pack Van', 'Passenger Van', 'Pick-up Truck'));
} else {
    global $adb;
    $adb->pquery("DELETE FROM vtiger_vehicle_type");
    $field3->setNoRolePicklistValues(array('Tractor', 'Trailer - Drop Frame', 'Trailer - Freight', 'Trailer - Pup', 'Trailer - Vault', 'Straight Truck', 'Refrigerated Straight Truck', 'Cube Van', 'Pack Van', 'Passenger Van', 'Pick-up Truck'));
}
$field4 = Vtiger_Field::getInstance('vehicle_status', $moduleInstance);

if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLES_STATUS';
    $field4->name = 'vehicle_status';
    $field4->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field4->column = 'vehicle_status';   //  This will be the columnname in your database for the new field.
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field4);
    $field4->setPicklistValues(array('Active', 'Disposed', 'Out of Service'));
}

$field6 = Vtiger_Field::getInstance('vehicle_milesdate', $moduleInstance);
if (!$field6) {
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
$block1->save($moduleInstance);

$block2 = Vtiger_Block::getInstance('LBL_VEHICLES_SPECS', $moduleInstance);
if ($block2) {
    echo "<h3>The LBL_VEHICLES_SPECS block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_VEHICLES_SPECS';
    $moduleInstance->addBlock($block2);
}

$field7 = Vtiger_Field::getInstance('vehicle_cubec', $moduleInstance);
if (!$field7) {
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

$field8 = Vtiger_Field::getInstance('vehicle_vin', $moduleInstance);
if (!$field8) {
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

$field9 = Vtiger_Field::getInstance('vehicle_length', $moduleInstance);
if (!$field9) {
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

$field10 = Vtiger_Field::getInstance('vehicle_year', $moduleInstance);
if (!$field10) {
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

$field101 = Vtiger_Field::getInstance('vehicle_maker', $moduleInstance);
if (!$field101) {
    $field101 = new Vtiger_Field();
    $field101->label = 'LBL_VEHICLES_MAKER';
    $field101->name = 'vehicle_maker';
    $field101->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field101->column = 'vehicle_maker';   //  This will be the columnname in your database for the new field.
    $field101->columntype = 'VARCHAR(60)';
    $field101->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field101->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field101);
}

$field102 = Vtiger_Field::getInstance('vehicle_model', $moduleInstance);
if (!$field102) {
    $field102 = new Vtiger_Field();
    $field102->label = 'LBL_VEHICLES_MODEL';
    $field102->name = 'vehicle_model';
    $field102->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field102->column = 'vehicle_model';   //  This will be the columnname in your database for the new field.
    $field102->columntype = 'VARCHAR(60)';
    $field102->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field102->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field102);
}

$field103 = Vtiger_Field::getInstance('vehicle_cabstyle', $moduleInstance);
if (!$field103) {
    $field103 = new Vtiger_Field();
    $field103->label = 'LBL_VEHICLES_CAB_STYLE';
    $field103->name = 'vehicle_cabstyle';
    $field103->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field103->column = 'vehicle_cabstyle';   //  This will be the columnname in your database for the new field.
    $field103->columntype = 'VARCHAR(60)';
    $field103->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field103->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field103);
}

$field104 = Vtiger_Field::getInstance('vehicle_axles', $moduleInstance);
if (!$field104) {
    $field104 = new Vtiger_Field();
    $field104->label = 'LBL_VEHICLES_AXLES';
    $field104->name = 'vehicle_axles';
    $field104->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field104->column = 'vehicle_axles';   //  This will be the columnname in your database for the new field.
    $field104->columntype = 'INT(10)';
    $field104->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field104->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field104);
}

$field105 = Vtiger_Field::getInstance('vehicle_wheels', $moduleInstance);
if (!$field105) {
    $field105 = new Vtiger_Field();
    $field105->label = 'LBL_VEHICLES_WHEELS';
    $field105->name = 'vehicle_wheels';
    $field105->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field105->column = 'vehicle_wheels';   //  This will be the columnname in your database for the new field.
    $field105->columntype = 'INT(10)';
    $field105->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field105->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field105);
}

$field106 = Vtiger_Field::getInstance('vehicle_cdltype', $moduleInstance);
if (!$field106) {
    $field106 = new Vtiger_Field();
    $field106->label = 'LBL_VEHICLES_CDL_TYPE';
    $field106->name = 'vehicle_cdltype';
    $field106->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field106->column = 'vehicle_cdltype';   //  This will be the columnname in your database for the new field.
    $field106->columntype = 'VARCHAR(50)';
    $field106->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field106->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field106);

    $field106->setPicklistValues(array('Class A', 'Class B', 'Class C', 'None'));
}


$field11 = Vtiger_Field::getInstance('vehicle_weight', $moduleInstance);
if (!$field11) {
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

$field13 = Vtiger_Field::getInstance('vehicle_height', $moduleInstance);
if (!$field13) {
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

$field14 = Vtiger_Field::getInstance('vehicle_adate', $moduleInstance);
if (!$field14) {
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

$field15 = Vtiger_Field::getInstance('vehicle_ddate', $moduleInstance);
if (!$field15) {
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

$block2->save($moduleInstance);

$block201 = Vtiger_Block::getInstance('LBL_VEHICLES_LICENSE', $moduleInstance);

if ($block201) {
    echo "<h3>The LBL_VEHICLES_RECORDUPDATE block already exists</h3><br>";
} else {
    $block201 = new Vtiger_Block();
    $block201->label = 'LBL_VEHICLES_LICENSE';
    $moduleInstance->addBlock($block201);
}

$field151 = Vtiger_Field::getInstance('vehicle_plateno', $moduleInstance);
if (!$field151) {
    $field151 = new Vtiger_Field();
    $field151->label = 'LBL_VEHICLES_PLATE_NO';
    $field151->name = 'vehicle_plateno';
    $field151->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field151->column = 'vehicle_plateno';   //  This will be the columnname in your database for the new field.
    $field151->columntype = 'VARCHAR(50)';
    $field151->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field151->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block201->addField($field151);
}

$field152 = Vtiger_Field::getInstance('vehicle_platestate', $moduleInstance);
if (!$field152) {
    $field152 = new Vtiger_Field();
    $field152->label = 'LBL_VEHICLES_PLATE_STATE';
    $field152->name = 'vehicle_platestate';
    $field152->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field152->column = 'vehicle_platestate';   //  This will be the columnname in your database for the new field.
    $field152->columntype = 'VARCHAR(50)';
    $field152->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field152->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block201->addField($field152);
}

$field153 = Vtiger_Field::getInstance('vehicle_platecountry', $moduleInstance);
if (!$field153) {
    $field153 = new Vtiger_Field();
    $field153->label = 'LBL_VEHICLES_PLATE_COUNTRY';
    $field153->name = 'vehicle_platecountry';
    $field153->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field153->column = 'vehicle_platecountry';   //  This will be the columnname in your database for the new field.
    $field153->columntype = 'VARCHAR(50)';
    $field153->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field153->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block201->addField($field153);
}

$field154 = Vtiger_Field::getInstance('vehicle_plateexp', $moduleInstance);
if (!$field154) {
    $field154 = new Vtiger_Field();
    $field154->label = 'LBL_VEHICLES_PLATE_EXPIRATION';
    $field154->name = 'vehicle_plateexp';
    $field154->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field154->column = 'vehicle_plateexp';   //  This will be the columnname in your database for the new field.
    $field154->columntype = 'DATE';
    $field154->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field154->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block201->addField($field154);
}

$field155 = Vtiger_Field::getInstance('vehicle_platetype', $moduleInstance);
if (!$field155) {
    $field155 = new Vtiger_Field();
    $field155->label = 'LBL_VEHICLES_PLATE_TYPE';
    $field155->name = 'vehicle_platetype';
    $field155->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field155->column = 'vehicle_platetype';   //  This will be the columnname in your database for the new field.
    $field155->columntype = 'VARCHAR(100)';
    $field155->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field155->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block201->addField($field155);

    $field155->setPicklistValues(array('Base', 'IRP'));
}


//Delete Fields

$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('time_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_in', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('time_in', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('vquantity', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('name', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('vehicle_milesnum', $moduleInstance);
if ($field) {
    $field->delete();
}
$field = Vtiger_Field::getInstance('date_out', $moduleInstance);
if ($field) {
    $field->delete();
}



// Add documents related list
$moduleInstance->unsetRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', 'get_attachments');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD', 'SELECT'), 'get_attachments');

//Adding relationship between drivers and vehicles
$moduleInstanceEmployees = Vtiger_Module::getInstance('Employees');
$moduleInstanceEmployees->setRelatedList($moduleInstance, 'Equipment', array('SELECT'), 'get_related_list');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
