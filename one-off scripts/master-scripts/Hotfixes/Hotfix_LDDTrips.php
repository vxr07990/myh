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




$moduleInstance = Vtiger_Module::getInstance('Trips');

$blockInstance = Vtiger_Block::getInstance('LBL_TRIPS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_TRIPS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TRIPS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$field1 = Vtiger_Field::getInstance('trips_id', $moduleInstance);
$field2 = Vtiger_Field::getInstance('intransitzone', $moduleInstance);
$field3 = Vtiger_Field::getInstance('origin_zone', $moduleInstance);
$field4 = Vtiger_Field::getInstance('origin_state', $moduleInstance);
$field5 = Vtiger_Field::getInstance('empty_zone', $moduleInstance);
$field6 = Vtiger_Field::getInstance('empty_state', $moduleInstance);
$field7 = Vtiger_Field::getInstance('empty_date', $moduleInstance);
$field8 = Vtiger_Field::getInstance('agent_unit', $moduleInstance);
$field9 = Vtiger_Field::getInstance('planning_notes', $moduleInstance);
$field10 = Vtiger_Field::getInstance('dispatch_notes', $moduleInstance);
$field11 = Vtiger_Field::getInstance('driver_id', $moduleInstance);
$field12 = Vtiger_Field::getInstance('total_line_haul', $moduleInstance);
$field13 = Vtiger_Field::getInstance('total_weight', $moduleInstance);
$field15 = Vtiger_Field::getInstance('currentzone', $moduleInstance);
$field155 = Vtiger_Field::getInstance('unitnumber', $moduleInstance);
$field16 = Vtiger_Field::getInstance('trips_status', $moduleInstance);






$field14 = Vtiger_Field::getInstance('trips_firstload', $moduleInstance);
if ($field14) {
    echo "<li> the total_weight already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_TRIPS_FIRSTLOAD';
    $field14->name = 'trips_firstload';
    $field14->table = 'vtiger_trips';
    $field14->column = 'trips_firstload';
    $field14->columntype = 'DATE';
    $field14->uitype = 5;
    $field14->typeofdata = 'D~O';

    $blockInstance->addField($field14);
}


$field17 = Vtiger_Field::getInstance('trips_shipmentcount', $moduleInstance);
if ($field17) {
    echo "<li>the status already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_TRIPS_SHIPMENT_COUNT';
    $field17->name = 'trips_shipmentcount';
    $field17->table = 'vtiger_trips';
    $field17->column = 'trips_shipmentcount';
    $field17->columntype = 'VARCHAR(10)';
    $field17->uitype = 2;
    $field17->typeofdata = 'V~O';
    
    $blockInstance->addField($field17);
}


$blockInstance3 = Vtiger_Block::getInstance('LBL_TRIPS_DRIVER', $moduleInstance);
if ($blockInstance3) {
    echo "<h3>The LBL_TRIPS_DRIVER block already exists</h3><br> \n";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_TRIPS_DRIVER';
    $moduleInstance->addBlock($blockInstance3);
}

echo "<ul>";
$field18 = Vtiger_Field::getInstance('trips_driverlastname', $moduleInstance);
if ($field18) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_TRIPS_DRIVER_LAST';
    $field18->name = 'trips_driverlastname';
    $field18->table = 'vtiger_trips';
    $field18->column = 'trips_driverlastname';
    $field18->columntype = 'VARCHAR(50)';
    $field18->uitype = 2;
    $field18->typeofdata = 'V~O';

    $blockInstance3->addField($field18);
}

echo "<ul>";
$field19 = Vtiger_Field::getInstance('trips_driverfirstname', $moduleInstance);
if ($field19) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_TRIPS_DRIVER_FIRST';
    $field19->name = 'trips_driverfirstname';
    $field19->table = 'vtiger_trips';
    $field19->column = 'trips_driverfirstname';
    $field19->columntype = 'VARCHAR(50)';
    $field19->uitype = 2;
    $field19->typeofdata = 'V~O';

    $blockInstance3->addField($field19);
}

echo "<ul>";
$field20 = Vtiger_Field::getInstance('trips_driverno', $moduleInstance);
if ($field20) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_TRIPS_DRIVER_NO';
    $field20->name = 'trips_driverno';
    $field20->table = 'vtiger_trips';
    $field20->column = 'trips_driverno';
    $field20->columntype = 'VARCHAR(15)';
    $field20->uitype = 2;
    $field20->typeofdata = 'V~O';

    $blockInstance3->addField($field20);
}



echo "<ul>";
$field21 = Vtiger_Field::getInstance('trips_drivercellphone', $moduleInstance);
if ($field21) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_TRIPS_DRIVERCELL';
    $field21->name = 'trips_drivercellphone';
    $field21->table = 'vtiger_trips';
    $field21->column = 'trips_drivercellphone';
    $field21->columntype = 'VARCHAR(25)';
    $field21->uitype = 2;
    $field21->typeofdata = 'V~O';

    $blockInstance3->addField($field21);
}


echo "<ul>";
$field23 = Vtiger_Field::getInstance('trips_driversemail', $moduleInstance);
if ($field23) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_TRIPS_DRIVEREMAIL';
    $field23->name = 'trips_driversemail';
    $field23->table = 'vtiger_trips';
    $field23->column = 'trips_driversemail';
    $field23->columntype = 'VARCHAR(75)';
    $field23->uitype = 13;
    $field23->typeofdata = 'V~O';

    $blockInstance3->addField($field23);
}


echo "<ul>";
$field24 = Vtiger_Field::getInstance('checkin_notes', $moduleInstance);
if ($field24) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_TRIPS_CHECKINNOTES';
    $field24->name = 'checkin_notes';
    $field24->table = 'vtiger_trips';
    $field24->column = 'checkin_notes';
    $field24->columntype = 'TEXT';
    $field24->uitype = 19;
    $field24->typeofdata = 'V~O';

    $blockInstance3->addField($field24);
}

$field25 = Vtiger_Field::getInstance('checkin', $moduleInstance);
if ($field25) {
    echo "<li>The checkin field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_TRIPS_CHECKIN';
    $field25->name = 'checkin';
    $field25->table = 'vtiger_trips';
    $field25->column = 'checkin';
    $field25->columntype = 'VARCHAR(3)';
    $field25->uitype = 56;
    $field25->typeofdata = 'C~O';

    $blockInstance3->addField($field25);
}


//New Fields LDD

$field26 = Vtiger_Field::getInstance('trips_totalmiles', $moduleInstance);
if (!$field26) {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_TRIPS_TOTAL_MILES';
    $field26->name = 'trips_totalmiles';
    $field26->table = 'vtiger_trips';
    $field26->column = 'trips_totalmiles';
    $field26->columntype = 'DECIMAL(10,2)';
    $field26->uitype = 7;
    $field26->typeofdata = 'NN~O';
    
    $blockInstance->addField($field26);
}

$field27 = Vtiger_Field::getInstance('trips_totalcube', $moduleInstance);
if (!$field27) {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_TRIPS_TOTAL_CUBE';
    $field27->name = 'trips_totalcube';
    $field27->table = 'vtiger_trips';
    $field27->column = 'trips_totalcube';
    $field27->columntype = 'DECIMAL(10,2)';
    $field27->uitype = 7;
    $field27->typeofdata = 'NN~O';
    
    $blockInstance->addField($field27);
}

$field28 = Vtiger_Field::getInstance('trips_cubeavailable', $moduleInstance);
if (!$field28) {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_TRIPS_CUBE_AVAILABLE';
    $field28->name = 'trips_cubeavailable';
    $field28->table = 'vtiger_trips';
    $field28->column = 'trips_cubeavailable';
    $field28->columntype = 'DECIMAL(10,2)';
    $field28->uitype = 7;
    $field28->typeofdata = 'NN~O';
    
    $blockInstance->addField($field28);
}

$field29 = Vtiger_Field::getInstance('trips_numberautos', $moduleInstance);
if (!$field29) {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_TRIPS_NUMBER_AUTOS';
    $field29->name = 'trips_numberautos';
    $field29->table = 'vtiger_trips';
    $field29->column = 'trips_numberautos';
    $field29->columntype = 'DECIMAL(10,2)';
    $field29->uitype = 7;
    $field29->typeofdata = 'NN~O';
    
    $blockInstance->addField($field29);
}

$field30 = Vtiger_Field::getInstance('trips_days', $moduleInstance);
if (!$field30) {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_TRIPS_DAYS';
    $field30->name = 'trips_days';
    $field30->table = 'vtiger_trips';
    $field30->column = 'trips_days';
    $field30->columntype = 'INT(5)';
    $field30->uitype = 7;
    $field30->typeofdata = 'I~O';
    
    $blockInstance->addField($field30);
}

$field31 = Vtiger_Field::getInstance('trips_dailyrate', $moduleInstance);
if (!$field31) {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_TRIPS_RATE_DAY';
    $field31->name = 'trips_dailyrate';
    $field31->table = 'vtiger_trips';
    $field31->column = 'trips_dailyrate';
    $field31->columntype = 'DECIMAL(10,2)';
    $field31->uitype = 7;
    $field31->typeofdata = 'NN~O';
    
    $blockInstance->addField($field31);
}

$field32 = Vtiger_Field::getInstance('trips_milerate', $moduleInstance);
if (!$field32) {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_TRIPS_RATE_MILE';
    $field32->name = 'trips_milerate';
    $field32->table = 'vtiger_trips';
    $field32->column = 'trips_milerate';
    $field32->columntype = 'DECIMAL(10,2)';
    $field32->uitype = 7;
    $field32->typeofdata = 'NN~O';
    
    $blockInstance->addField($field32);
}

$field33 = Vtiger_Field::getInstance('trips_fuelsurcharge', $moduleInstance);
if (!$field33) {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_TRIPS_FUEL_SURCHARGE';
    $field33->name = 'trips_fuelsurcharge';
    $field33->table = 'vtiger_trips';
    $field33->column = 'trips_fuelsurcharge';
    $field33->columntype = 'DECIMAL(10,2)';
    $field33->uitype = 7;
    $field33->typeofdata = 'NN~O';
    
    $blockInstance->addField($field33);
}

//New checking related fields

$field34 = Vtiger_Field::getInstance('trips_vehicle', $moduleInstance);
if (!$field34) {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_TRIPS_VEHICLE';
    $field34->name = 'trips_vehicle';
    $field34->table = 'vtiger_trips';
    $field34->column = 'trips_vehicle';
    $field34->columntype = 'INT(10)';
    $field34->uitype = 10;
    $field34->typeofdata = 'I~O';
    
    $blockInstance3->addField($field34);
    $field34->setRelatedModules(array('Vehicles'));
}

$field341 = Vtiger_Field::getInstance('trips_vehi_cube', $moduleInstance);
if (!$field341) {
    $field341 = new Vtiger_Field();
    $field341->label = 'LBL_TRIPS_VEHICLE_CUBE';
    $field341->name = 'trips_vehi_cube';
    $field341->table = 'vtiger_trips';
    $field341->column = 'trips_vehi_cube';
    $field341->columntype = 'VARCHAR(100)';
    $field341->uitype = 2;
    $field341->typeofdata = 'V~O';
    
    $blockInstance3->addField($field341);
}

$field342 = Vtiger_Field::getInstance('trips_vehi_length', $moduleInstance);
if (!$field342) {
    $field342 = new Vtiger_Field();
    $field342->label = 'LBL_TRIPS_VEHICLE_LENGTH';
    $field342->name = 'trips_vehi_length';
    $field342->table = 'vtiger_trips';
    $field342->column = 'trips_vehi_length';
    $field342->columntype = 'VARCHAR(100)';
    $field342->uitype = 2;
    $field342->typeofdata = 'V~O';
    
    $blockInstance3->addField($field342);
}



$field35 = Vtiger_Field::getInstance('agent_id', $moduleInstance);
if (!$field35) {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_TRIPS_AGENT';
    $field35->name = 'agent_id';
    $field35->table = 'vtiger_trips';
    $field35->column = 'agent_id';
    $field35->columntype = 'INT(10)';
    $field35->uitype = 10;
    $field35->typeofdata = 'I~O';

    $blockInstance3->addField($field35);

    $field35->setRelatedModules(array('Agents'));
}

$field36 = Vtiger_Field::getInstance('fleet_manager', $moduleInstance);
if (!$field36) {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_TRIPS_FLEET_MANAGER';
    $field36->name = 'fleet_manager';
    $field36->table = 'vtiger_trips';
    $field36->column = 'fleet_manager';
    $field36->columntype = 'VARCHAR(255)';
    $field36->uitype = 2;
    $field36->typeofdata = 'V~O';

    $blockInstance3->addField($field36);
}

$field37 = Vtiger_Field::getInstance('fleet_manager_email', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_TRIPS_FLEET_MANAGER_EMAIL';
    $field37->name = 'fleet_manager_email';
    $field37->table = 'vtiger_trips';
    $field37->column = 'fleet_manager_email';
    $field37->columntype = 'VARCHAR(100)';
    $field37->uitype = 13;
    $field37->typeofdata = 'V~O';

    $blockInstance3->addField($field37);
}

$field37 = Vtiger_Field::getInstance('fleet_manager_email', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_TRIPS_FLEET_MANAGER_EMAIL';
    $field37->name = 'fleet_manager_email';
    $field37->table = 'vtiger_trips';
    $field37->column = 'fleet_manager_email';
    $field37->columntype = 'VARCHAR(100)';
    $field37->uitype = 13;
    $field37->typeofdata = 'V~O';

    $blockInstance3->addField($field37);
}


$blockInstance->save($moduleInstance);


// Add relationship to orders

$moduleInstance->unsetRelatedList(Vtiger_Module::getInstance('OrdersTask'));
$moduleInstance->unsetRelatedList(Vtiger_Module::getInstance('Orders'));

$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders', array('SELECT'), 'get_related_list');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders', array('SELECT'), 'get_related_list');


$filter1 = new Vtiger_Filter();
$filter1->deleteForModule($moduleInstance);
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field17, 1)->addField($field18, 2)->addField($field19, 3)->addField($field20, 4)->addField($field15, 5)->addField($field2, 6)->addField($field5, 7)->addField($field7, 8)->addField($field6, 9)->addField($field155, 10)->addField($field14, 11)->addField($field21, 12)->addField($field23, 13);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";