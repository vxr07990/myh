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



$vehiclesInspIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleInspections'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleInspections';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vehiclesInspIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INSPECTION_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_INSPECTION_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_INSPECTION_INFORMATION';
    $moduleInstance->addBlock($block1);
}
//start block1 fields

$field01 = Vtiger_Field::getInstance('inspection_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_INSPECTIONS_NO';
    $field01->name = 'inspection_number';
    $field01->table = 'vtiger_vehicleinspections';
    $field01->column = 'inspection_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleInspections', 'INSP', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('inspection_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_INSPECTIONS_VEHICLE_NO';
    $field0->name = 'inspection_vehicle';
    $field0->table = 'vtiger_vehicleinspections';
    $field0->column = 'inspection_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);

    $field0->setRelatedModules(array('Vehicles'));
}


$field1 = Vtiger_Field::getInstance('inspection_type', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_INSPECTIONS_TYPE';
    $field1->name = 'inspection_type';
    $field1->table = 'vtiger_vehicleinspections';
    $field1->column = 'inspection_type';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Periodic', 'Annual', 'Semiannual'));
}

$field2 = Vtiger_Field::getInstance('inspection_duedate', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_INSPECTIONS_DUE';
    $field2->name = 'inspection_duedate';
    $field2->table = 'vtiger_vehicleinspections';
    $field2->column = 'inspection_duedate';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';

    $block1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('inspection_date', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_INSPECTIONS_DATE';
    $field3->name = 'inspection_date';
    $field3->table = 'vtiger_vehicleinspections';
    $field3->column = 'inspection_date';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~O';

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('inspection_photosdate', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_INSPECTIONS_PHOTOS_DATE';
    $field4->name = 'inspection_photosdate';
    $field4->table = 'vtiger_vehicleinspections';
    $field4->column = 'inspection_photosdate';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('inspection_formsreceive', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_INSPECTIONS_FORMS_DATE';
    $field5->name = 'inspection_formsreceive';
    $field5->table = 'vtiger_vehicleinspections';
    $field5->column = 'inspection_formsreceive';
    $field5->columntype = 'DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';

    $block1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('inspection_photosreceive', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_INSPECTIONS_PHOTOS_REC_DATE';
    $field6->name = 'inspection_photosreceive';
    $field6->table = 'vtiger_vehicleinspections';
    $field6->column = 'inspection_photosreceive';
    $field6->columntype = 'DATE';
    $field6->uitype = 5;
    $field6->typeofdata = 'D~O';

    $block1->addField($field6);
}


$field7 = Vtiger_Field::getInstance('inspection_odometer', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_INSPECTIONS_ODOMETER';
    $field7->name = 'inspection_odometer';
    $field7->table = 'vtiger_vehicleinspections';
    $field7->column = 'inspection_odometer';
    $field7->columntype = 'VARCHAR(25)';
    $field7->uitype = 2;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('inspection_sticker', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_INSPECTIONS_STICKER_NO';
    $field8->name = 'inspection_sticker';
    $field8->table = 'vtiger_vehicleinspections';
    $field8->column = 'inspection_sticker';
    $field8->columntype = 'VARCHAR(25)';
    $field8->uitype = 2;
    $field8->typeofdata = 'V~O';

    $block1->addField($field8);
}

//$field9 = Vtiger_Field::getInstance('inspection_shoplocation', $moduleInstance);
//if (!$field9) {
//
//    $field9 = new Vtiger_Field();
//    $field9->label = 'LBL_INSPECTIONS_SHOP_LOC';
//    $field9->name = 'inspection_shoplocation';
//    $field9->table = 'vtiger_vehicleinspections';
//    $field9->column = 'inspection_shoplocation';
//    $field9->columntype = 'VARCHAR(25)';
//    $field9->uitype = 2;
//    $field9->typeofdata = 'V~O';
//
//    $block1->addField($field9);
//}


$field10 = Vtiger_Field::getInstance('inspection_shopname', $moduleInstance);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_INSPECTIONS_SHOP_NAME';
    $field10->name = 'inspection_shopname';
    $field10->table = 'vtiger_vehicleinspections';
    $field10->column = 'inspection_shopname';
    $field10->columntype = 'INT(25)';
    $field10->uitype = 10;
    $field10->typeofdata = 'I~O';

    $block1->addField($field10);
    $field10->setRelatedModules(array('Vendors'));
}

$field11 = Vtiger_Field::getInstance('inspection_inspector', $moduleInstance);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_INSPECTIONS_INSP_NAME';
    $field11->name = 'inspection_inspector';
    $field11->table = 'vtiger_vehicleinspections';
    $field11->column = 'inspection_inspector';
    $field11->columntype = 'VARCHAR(25)';
    $field11->uitype = 2;
    $field11->typeofdata = 'V~O';

    $block1->addField($field11);
}

$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field36) {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $block1->addField($field36);
}

$field37 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $block1->addField($field37);
}

$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field38) {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field37->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $block1->addField($field38);
}

if ($vehiclesInspIsNew) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();

    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field01)
            ->addField($field0, 1)
            ->addField($field1, 2)
            ->addField($field2, 3)
            ->addField($field3, 4)
            ->addField($field4, 5)
            ->addField($field5, 6)
            ->addField($field6, 7)
            ->addField($field7, 8)
            ->addField($field8, 9)
            ->addField($field9, 10);
}

// Add documents related list
if ($vehiclesInspIsNew) {
    $moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD', 'SELECT'), 'get_attachments');
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Inspections', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleInspections'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";