<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



$VehicleMaintenanceIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleMaintenance');
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleMaintenance';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $VehicleMaintenanceIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_MAINTENANCE_INFORMATION', $moduleInstance);
if (!$block1) {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_MAINTENANCE_INFORMATION';
    $moduleInstance->addBlock($block1);
}
//start block1 fields

$field01 = Vtiger_Field::getInstance('maintenance_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_MAINTENANCE_NO';
    $field01->name = 'maintenance_number';
    $field01->table = 'vtiger_vehiclemaintenance';
    $field01->column = 'maintenance_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleMaintenance', 'MAINT', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('maintenance_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_MAINTENANCE_VEHICLE_NO';
    $field0->name = 'maintenance_vehicle';
    $field0->table = 'vtiger_vehiclemaintenance';
    $field0->column = 'maintenance_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);

    $field0->setRelatedModules(array('Vehicles'));
}


$field1 = Vtiger_Field::getInstance('maintenance_reason', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_MAINTENANCE_REASON';
    $field1->name = 'maintenance_reason';
    $field1->table = 'vtiger_vehiclemaintenance';
    $field1->column = 'maintenance_reason';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Preventive Maint.', 'Tires', 'Mechanical', 'Brakes', 'Electrical', 'Body damage', 'Misc.'));
}

$field2 = Vtiger_Field::getInstance('maintenance_date', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_MAINTENANCE_DATE';
    $field2->name = 'maintenance_date';
    $field2->table = 'vtiger_vehiclemaintenance';
    $field2->column = 'maintenance_date';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';

    $block1->addField($field2);
}

$field7 = Vtiger_Field::getInstance('maintenance_odometer', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_MAINTENANCE_ODOMETER';
    $field7->name = 'maintenance_odometer';
    $field7->table = 'vtiger_vehiclemaintenance';
    $field7->column = 'maintenance_odometer';
    $field7->columntype = 'VARCHAR(25)';
    $field7->uitype = 2;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('maintenance_cost', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_MAINTENANCE_COST';
    $field8->name = 'maintenance_cost';
    $field8->table = 'vtiger_vehiclemaintenance';
    $field8->column = 'maintenance_cost';
    $field8->columntype = 'DECIMAL(10,2)';
    $field8->uitype = 7;
    $field8->typeofdata = 'NN~O';

    $block1->addField($field8);
}

$field81 = Vtiger_Field::getInstance('maintenance_po', $moduleInstance);
if (!$field81) {
    $field81 = new Vtiger_Field();
    $field81->label = 'LBL_MAINTENANCE_PO';
    $field81->name = 'maintenance_po';
    $field81->table = 'vtiger_vehiclemaintenance';
    $field81->column = 'maintenance_po';
    $field81->columntype = 'VARCHAR(55)';
    $field81->uitype = 2;
    $field81->typeofdata = 'V~O';

    $block1->addField($field81);
}

$field10 = Vtiger_Field::getInstance('maintenance_shopname', $moduleInstance);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_MAINTENANCE_SHOP_NAME';
    $field10->name = 'maintenance_shop';
    $field10->table = 'vtiger_vehiclemaintenance';
    $field10->column = 'maintenance_shop';
    $field10->columntype = 'INT(15)';
    $field10->uitype = 10;
    $field10->typeofdata = 'I~O';

    $block1->addField($field10);

    $field10->setRelatedModules(array('Vendors'));
}



//$field9 = Vtiger_Field::getInstance('maintenance_shoplocation', $moduleInstance);
//if (!$field9) {
//
//    $field9 = new Vtiger_Field();
//    $field9->label = 'LBL_MAINTENANCE_SHOP_LOC';
//    $field9->name = 'maintenance_shoplocation';
//    $field9->table = 'vtiger_vehiclemaintenance';
//    $field9->column = 'maintenance_shoplocation';
//    $field9->columntype = 'VARCHAR(75)';
//    $field9->uitype = 2;
//    $field9->typeofdata = 'V~O';
//
//    $block1->addField($field9);
//}
//$field101 = Vtiger_Field::getInstance('maintenance_shopphone', $moduleInstance);
//if (!$field101) {
//
//    $field101 = new Vtiger_Field();
//    $field101->label = 'LBL_MAINTENANCE_SHOP_PHONE';
//    $field101->name = 'maintenance_shopphone';
//    $field101->table = 'vtiger_vehiclemaintenance';
//    $field101->column = 'maintenance_shopphone';
//    $field101->columntype = 'VARCHAR(35)';
//    $field101->uitype = 2;
//    $field101->typeofdata = 'V~O';
//
//    $block1->addField($field101);
//}

$field11 = Vtiger_Field::getInstance('maintenance_mechanic', $moduleInstance);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_MAINTENANCE_MEC_NAME';
    $field11->name = 'maintenance_mechanic';
    $field11->table = 'vtiger_vehiclemaintenance';
    $field11->column = 'maintenance_mechanic';
    $field11->columntype = 'VARCHAR(25)';
    $field11->uitype = 2;
    $field11->typeofdata = 'V~O';

    $block1->addField($field11);
}

$field12 = Vtiger_Field::getInstance('maintenance_comments', $moduleInstance);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_MAINTENANCE_COMMENTS'; // Repair made / Comments
    $field12->name = 'maintenance_comments';
    $field12->table = 'vtiger_vehiclemaintenance';
    $field12->column = 'maintenance_comments';
    $field12->columntype = 'VARCHAR(250)';
    $field12->uitype = 19;
    $field12->typeofdata = 'V~O';

    $block1->addField($field12);
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

$db = PearDatabase::getInstance();
$sql = 'SELECT * FROM vtiger_def_org_share INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_def_org_share.tabid WHERE name=?';
$result = $db->pquery($sql, ['VehicleMaintenance']);

if ($VehicleMaintenanceIsNew || ($db->num_rows($result) == 0)) {
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
            ->addField($field7, 4)
            ->addField($field8, 5)
            ->addField($field81, 6)
            ->addField($field10, 7)
            ->addField($field11, 8)
            ->addField($field12, 9);
}

// Add documents related list

if ($VehicleMaintenanceIsNew || ($db->num_rows($result) == 0)) {
    $moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD', 'SELECT'), 'get_attachments');
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Maintenance', array('ADD'), 'get_dependents_list');
}



//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleMaintenance'");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
