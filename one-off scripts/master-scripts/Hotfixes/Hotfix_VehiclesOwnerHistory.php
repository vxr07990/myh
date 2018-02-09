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



//VehicleOwnerHistory.php
include_once 'vtlib/Vtiger/Module.php';
$vehiclesTransfIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleOwnerHistory'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleOwnerHistory';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vehiclesTransfIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_OWNER_HISTORY_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_OWNER_HISTORY_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_OWNER_HISTORY_INFORMATION';
    $moduleInstance->addBlock($block1);
}

//start block1 fields

$field01 = Vtiger_Field::getInstance('ownerhistory_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_OWNER_HISTORY_NO';
    $field01->name = 'ownerhistory_number';
    $field01->table = 'vtiger_vehicleownerhistory';
    $field01->column = 'ownerhistory_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleOwnerHistory', 'VOH', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('ownerhistory_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_OWNER_HISTORY_VEHICLE_NO';
    $field0->name = 'ownerhistory_vehicle';
    $field0->table = 'vtiger_vehicleownerhistory';
    $field0->column = 'ownerhistory_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);
    $field0->setRelatedModules(array('Vehicles'));
}

$field2 = Vtiger_Field::getInstance('vehicle_sponsor_agent', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_SPONSOR_AGENT_NO';
    $field2->name = 'vehicle_sponsor_agent';
    $field2->table = 'vtiger_vehicleownerhistory';
    $field2->column = 'vehicle_sponsor_agent';
    $field2->columntype = 'INT(10)';
    $field2->uitype = 10;
    $field2->typeofdata = 'I~M';

    $block1->addField($field2);
    $field2->setRelatedModules(array('Agents'));
}

$field3 = Vtiger_Field::getInstance('vehicle_titleowner_agent', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TITLE_OWNER_AGENT_NO';
    $field3->name = 'vehicle_titleowner_agent';
    $field3->table = 'vtiger_vehicleownerhistory';
    $field3->column = 'vehicle_titleowner_agent';
    $field3->columntype = 'INT(10)';
    $field3->uitype = 10;
    $field3->typeofdata = 'I~M';

    $block1->addField($field3);
    $field3->setRelatedModules(array('Agents'));
}

$field1 = Vtiger_Field::getInstance('sponsor_type', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_SPONSOR_TYPE';
    $field1->name = 'sponsor_type';
    $field1->table = 'vtiger_vehicleownerhistory';
    $field1->column = 'sponsor_type';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Graebel Agent', 'Graebel Branch', 'Contractor - I/C', 'Contractor - TSC'));
}

$field111 = Vtiger_Field::getInstance('titleowner_type', $moduleInstance);
if (!$field111) {
    $field111 = new Vtiger_Field();
    $field111->label = 'LBL_TITLE_OWNER_TYPE';
    $field111->name = 'titleowner_type';
    $field111->table = 'vtiger_vehicleownerhistory';
    $field111->column = 'titleowner_type';
    $field111->columntype = 'VARCHAR(150)';
    $field111->uitype = 16;
    $field111->typeofdata = 'V~O';

    $block1->addField($field111);
    $field111->setPicklistValues(array('3rd Party', 'Graebel Branch', 'Contractor - I/C', 'Contractor - TSC'));
}

$field4 = Vtiger_Field::getInstance('purchase_date', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_PURCHASE_DATE';
    $field4->name = 'purchase_date';
    $field4->table = 'vtiger_vehicleownerhistory';
    $field4->column = 'purchase_date';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';

    $block1->addField($field4);
}

$field411 = Vtiger_Field::getInstance('term_date', $moduleInstance);
if (!$field411) {
    $field411 = new Vtiger_Field();
    $field411->label = 'LBL_TERM_DATE';
    $field411->name = 'term_date';
    $field411->table = 'vtiger_vehicleownerhistory';
    $field411->column = 'term_date';
    $field411->columntype = 'DATE';
    $field411->uitype = 5;
    $field411->typeofdata = 'D~O';

    $block1->addField($field411);
}

$field412 = Vtiger_Field::getInstance('early_term_date', $moduleInstance);
if (!$field412) {
    $field412 = new Vtiger_Field();
    $field412->label = 'LBL_EARLY_TERM_DATE';
    $field412->name = 'early_term_date';
    $field412->table = 'vtiger_vehicleownerhistory';
    $field412->column = 'early_term_date';
    $field412->columntype = 'DATE';
    $field412->uitype = 5;
    $field412->typeofdata = 'D~O';

    $block1->addField($field412);
}

$field5 = Vtiger_Field::getInstance('ownerhistory_address', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OWNER_HISTORY_ADDRESS';
    $field5->name = 'ownerhistory_address';
    $field5->table = 'vtiger_vehicleownerhistory';
    $field5->column = 'ownerhistory_address';
    $field5->columntype = 'VARCHAR(50)';
    $field5->uitype = 2;
    $field5->typeofdata = 'V~O';

    $block1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('ownerhistory_leinholder', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_LEINHOLDER';
    $field6->name = 'ownerhistory_leinholder';
    $field6->table = 'vtiger_vehicleownerhistory';
    $field6->column = 'ownerhistory_leinholder';
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 2;
    $field6->typeofdata = 'V~O';

    $block1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('ownerhistory_phone', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_OWNER_HISTORY_PHONE';
    $field7->name = 'ownerhistory_phone';
    $field7->table = 'vtiger_vehicleownerhistory';
    $field7->column = 'ownerhistory_phone';
    $field7->columntype = 'VARCHAR(30)';
    $field7->uitype = 11;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('ownerhistory_email', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_OWNER_HISTORY_EMAIL';
    $field8->name = 'ownerhistory_email';
    $field8->table = 'vtiger_vehicleownerhistory';
    $field8->column = 'ownerhistory_email';
    $field8->columntype = 'VARCHAR(50)';
    $field8->uitype = 13;
    $field8->typeofdata = 'E~O';

    $block1->addField($field8);
}

$field91 = Vtiger_Field::getInstance('ownerhistory_purchaseprice', $moduleInstance);
if (!$field91) {
    $field91 = new Vtiger_Field();
    $field91->label = 'LBL_PURCHASE_PRICE';
    $field91->name = 'ownerhistory_purchaseprice';
    $field91->table = 'vtiger_vehicleownerhistory';
    $field91->column = 'ownerhistory_purchaseprice';
    $field91->columntype = 'decimal(7,2)';
    $field91->uitype = 7;
    $field91->typeofdata = 'I~O';

    $block1->addField($field91);
}

$field911 = Vtiger_Field::getInstance('ownerhistory_mileage', $moduleInstance);
if (!$field911) {
    $field911 = new Vtiger_Field();
    $field911->label = 'LBL_MILEAGE';
    $field911->name = 'ownerhistory_mileage';
    $field911->table = 'vtiger_vehicleownerhistory';
    $field911->column = 'ownerhistory_mileage';
    $field911->columntype = 'decimal(10,2)';
    $field911->uitype = 7;
    $field911->typeofdata = 'I~O';

    $block1->addField($field911);
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

$agentField = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$agentField) {
    $agentField = new Vtiger_Field();
    $agentField->label = 'Owner Agent';
    $agentField->name = 'agentid';
    $agentField->table = 'vtiger_crmentity';
    $agentField->column = 'agentid';
    $agentField->columntype = 'INT(10)';
    $agentField->uitype = 1002;
    $agentField->typeofdata = 'I~O';

    $block1->addField($agentField);
}

$block1->save($module);

if ($vehiclesTransfIsNew) {
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
            ->addField($field4, 5);
}

// Add documents related list
if ($vehiclesTransfIsNew) {
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Owner History', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleOwnerHistory'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";