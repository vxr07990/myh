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



//VehicleTermination.php
include_once 'vtlib/Vtiger/Module.php';
$vehiclesTermIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleTerminations'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleTerminations';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vehiclesTermIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_TERMINATION_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_TERMINATION_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_TERMINATION_INFORMATION';
    $moduleInstance->addBlock($block1);
}

//start block1 fields

$field01 = Vtiger_Field::getInstance('termination_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_TERMINATION_NO';
    $field01->name = 'termination_number';
    $field01->table = 'vtiger_vehicleterminations';
    $field01->column = 'termination_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleTerminations', 'TERM', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('termination_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_TERMINATION_VEHICLE_NO';
    $field0->name = 'termination_vehicle';
    $field0->table = 'vtiger_vehicleterminations';
    $field0->column = 'termination_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);

    $field0->setRelatedModules(array('Vehicles'));
}

$field1 = Vtiger_Field::getInstance('termination_reason', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TERMINATION_REASON';
    $field1->name = 'termination_reason';
    $field1->table = 'vtiger_vehicleterminations';
    $field1->column = 'termination_reason';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Driver Terminated', 'Driver Resigned', 'Mechanical Problems', 'Other', 'Unknown Reason', 'Vehicle Sold'));
}

$field2 = Vtiger_Field::getInstance('termination_date', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TERMINATION_DATE';
    $field2->name = 'termination_date';
    $field2->table = 'vtiger_vehicleterminations';
    $field2->column = 'termination_date';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';

    $block1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('termination_driver', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TERMINATION_DRIVER_NO';
    $field3->name = 'termination_driver';
    $field3->table = 'vtiger_vehicleterminations';
    $field3->column = 'termination_driver';
    $field3->columntype = 'INT(10)';
    $field3->uitype = 10;
    $field3->typeofdata = 'I~M';

    $block1->addField($field3);
    $field3->setRelatedModules(array('Employees'));
}

$field4 = Vtiger_Field::getInstance('termination_comments', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TERMINATION_COMMENTS';
    $field4->name = 'termination_comments';
    $field4->table = 'vtiger_vehicleterminations';
    $field4->column = 'termination_comments';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 19;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('termination_problem_solved', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TERMINATION_PROBLEM_SOLVED';
    $field5->name = 'termination_problem_solved';
    $field5->table = 'vtiger_vehicleterminations';
    $field5->column = 'termination_problem_solved';
    $field5->columntype = 'VARCHAR(3)';
    $field5->uitype = 56;
    $field5->typeofdata = 'C~O';

    $block1->addField($field5);
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

if ($vehiclesTermIsNew) {
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
            ->addField($field5, 6);
}

// Add documents related list
if ($vehiclesTermIsNew) {
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Termination', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleTerminations'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";