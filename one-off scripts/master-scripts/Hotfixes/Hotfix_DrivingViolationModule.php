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



$moduleInstance = Vtiger_Module::getInstance('DrivingViolation');
$DrivingViolationIsNew = false;
if ($moduleInstance) {
    echo "Module DrivingViolation already present - Updating Fields";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'DrivingViolation';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $DrivingViolationIsNew = true;
}
// Field Setup
$blockName = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('drivingviolation_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_DRIVINGVIOLATION_NO';
    $field01->name = 'drivingviolation_number';
    $field01->table = 'vtiger_drivingviolation';
    $field01->column = 'drivingviolation_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleOutofService', 'DV', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field001 = Vtiger_Field::getInstance('drivingviolation_employeeid', $moduleInstance);
if (!$field001) {
    $field001 = new Vtiger_Field();
    $field001->label = 'LBL_DRIVING_VIOLATION_EMPLOYEEID';
    $field001->name = 'drivingviolation_employeeid';
    $field001->table = 'vtiger_drivingviolation';
    $field001->column = 'drivingviolation_employeeid';
    $field001->columntype = 'INT(15)';
    $field001->uitype = 10;
    $field001->typeofdata = 'I~M';
    $block->addField($field001);
    $field001->setRelatedModules(array('Employees'));
}

$field1 = Vtiger_Field::getInstance('drivingviolation_convtype', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_DRIVING_VIOLATION_CONVTYPE';
    $field1->name = 'drivingviolation_convtype';
    $field1->table = 'vtiger_drivingviolation';
    $field1->column = 'drivingviolation_convtype';
    $field1->columntype = 'VARCHAR(10)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    $field1->setPicklistValues(array('---', 'Speed < 14', 'Traf Sign'));
    $block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('drivingviolation_vehicletype', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_DRIVING_VIOLATION_VEHICLETYPE';
    $field2->name = 'drivingviolation_vehicletype';
    $field2->table = 'vtiger_drivingviolation';
    $field2->column = 'drivingviolation_vehicletype';
    $field2->columntype = 'VARCHAR(10)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';
    $field2->summaryfield = 1;
    $field2->setPicklistValues(array('Tractor', 'Trailer - Drop Frame', 'Trailer - Freight', 'Trailer - Pup', 'Trailer - Vault', 'Straight Truck', 'Refrigerated Straight Truck', 'Cube Van', 'Pack Van', 'Passenger Van', 'Pick-up Truck'));
    $block->addField($field2);
}


$field3 = Vtiger_Field::getInstance('drivingviolation_convictiondate', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_DRIVING_VIOLATION_CONVICTIONDATE';
    $field3->name = 'drivingviolation_convictiondate';
    $field3->table = 'vtiger_drivingviolation';
    $field3->column = 'drivingviolation_convictiondate';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~O';
    $field3->summaryfield = 1;
    $block->addField($field3);
}

$field4 = Vtiger_Field::getInstance('drivingviolation_infosource', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_DRIVING_VIOLATION_INFOSOURCE';
    $field4->name = 'drivingviolation_infosource';
    $field4->table = 'vtiger_drivingviolation';
    $field4->column = 'drivingviolation_infosource';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';
    $field4->summaryfield = 1;
    $block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('drivingviolation_comments', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_DRIVINGVIOLATION_COMMENTS';
    $field5->name = 'drivingviolation_comments';
    $field5->table = 'vtiger_drivingviolation';
    $field5->column = 'drivingviolation_comments';
    $field5->columntype = 'TEXT';
    $field5->uitype = 19;
    $field5->typeofdata = 'V~O';
    $field5->summaryfield = 1;
    $block->addField($field5);
}

if ($DrivingViolationIsNew) {

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Employees
    $employeesInstance = Vtiger_Module::getInstance('Employees');
    $employeesInstance->setRelatedList($moduleInstance, 'Driving Violation', array('ADD'), 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";