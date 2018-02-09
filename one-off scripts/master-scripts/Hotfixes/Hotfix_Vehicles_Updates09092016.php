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



$moduleInstance = Vtiger_Module::getInstance('Vehicles');

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INSURANCE', $moduleInstance);
if ($block1) {
    echo "<h3>The LBL_VEHICLES_INSURANCE block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_INSURANCE';
    $moduleInstance->addBlock($block1);
}

$field1 = Vtiger_Field::getInstance('vehicle_insurancetype', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VEHICLES_INSURANCETYPE';
    $field1->name = 'vehicle_insurancetype';
    $field1->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field1->column = 'vehicle_insurancetype';   //  This will be the columnname in your database for the new field.
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 33; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $block1->addField($field1);
    $field1->setPicklistValues(array('Liability Insurance', 'Collision Coverage', 'Comprehensive Coverage', 'Personal Injury Protection', 'Uninsured'));
}

$field2 = Vtiger_Field::getInstance('vehicles_insurancedate', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_VEHICLES_INSURANCEDATE';
    $field2->name = 'vehicles_insurancedate';
    $field2->table = 'vtiger_vehicles';
    $field2->column = 'vehicles_insurancedate';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';
    $block1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('vehicles_insurancesponsors', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VEHICLES_INSURANCESPONSORS';
    $field3->name = 'vehicles_insurancesponsors';
    $field3->table = 'vtiger_vehicles';
    $field3->column = 'vehicles_insurancesponsors';
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('vehicles_insurancecarrier', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLES_INSURANCECARRIER';
    $field4->name = 'vehicles_insurancecarrier';
    $field4->table = 'vtiger_vehicles';
    $field4->column = 'vehicles_insurancecarrier';
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 2;
    $field4->typeofdata = 'V~O';
    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('vehicles_insuranceexpdate', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_VEHICLES_INSURANCEEXPDATE';
    $field5->name = 'vehicles_insuranceexpdate';
    $field5->table = 'vtiger_vehicles';
    $field5->column = 'vehicles_insuranceexpdate';
    $field5->columntype = 'DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';
    $block1->addField($field5);
}

$block2 = Vtiger_Block::getInstance('LBL_VEHICLES_CARBCOMPLIANCE', $moduleInstance);
if ($block2) {
    echo "<h3>The LBL_VEHICLES_CARBCOMPLIANCE block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_VEHICLES_CARBCOMPLIANCE';
    $moduleInstance->addBlock($block2);
}

Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET block=$block2->id WHERE fieldname='vehicle_carb'");



$field6 = Vtiger_Field::getInstance('vehicle_carbexpdate', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_VEHICLES_CARBEXPDATE';
    $field6->name = 'vehicle_carbexpdate';
    $field6->table = 'vtiger_vehicles';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'vehicle_carbexpdate';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'DATE';
    $field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $block2->addField($field6);
}

$field7 = Vtiger_Field::getInstance('vehicle_carbeninemanufactureyear', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_VEHICLES_CARBENGINEMANUFACTUREYEAR';
    $field7->name = 'vehicle_carbeninemanufactureyear';
    $field7->table = 'vtiger_vehicles';
    $field7->column = 'vehicle_carbeninemanufactureyear';
    $field7->columntype = 'VARCHAR(100)';
    $field7->uitype = 15;
    $field7->typeofdata = 'V~O';
    $block2->addField($field7);
    $field7->setPicklistValues(array('1980', '1981', '1982', '1983', '1984', '1985', '1986', '1987', '1988', '1989', '1990', '1991', '1992', '1993', '1994', '1995', '1996', '1997', '1998', '1999', '2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";