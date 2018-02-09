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



//VehicleOutofService.php
include_once 'vtlib/Vtiger/Module.php';
$vehiclesOutServIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleOutofService'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleOutofService';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vehiclesOutServIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_OUT_OF_SERVICE_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_OUT_OF_SERVICE_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_OUT_OF_SERVICE_INFORMATION';
    $moduleInstance->addBlock($block1);
}

//start block1 fields

$field01 = Vtiger_Field::getInstance('outofservice_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_OUTOFSERVICE_NO';
    $field01->name = 'outofservice_number';
    $field01->table = 'vtiger_vehicleoutofservice';
    $field01->column = 'outofservice_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleOutofService', 'OUTS', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('outofservice_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_OUTOFSERVICE_VEHICLE_NO';
    $field0->name = 'outofservice_vehicle';
    $field0->table = 'vtiger_vehicleoutofservice';
    $field0->column = 'outofservice_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);

    $field0->setRelatedModules(array('Vehicles'));
}

$field1 = Vtiger_Field::getInstance('outofservice_status', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OUTOFSERVICE_STATUS';
    $field1->name = 'outofservice_status';
    $field1->table = 'vtiger_vehicleoutofservice';
    $field1->column = 'outofservice_status';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Out', 'Notice'));
}

$field2 = Vtiger_Field::getInstance('outofservice_reason', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_OUTOFSERVICE_REASON';
    $field2->name = 'outofservice_reason';
    $field2->table = 'vtiger_vehicleoutofservice';
    $field2->column = 'outofservice_reason';
    $field2->columntype = 'VARCHAR(150)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);
    $field2->setPicklistValues(array('Accident - Damage to the vehicle', 'Contract - Not signed and returned', 'Contract I/C - 30-day notice to cancel', 'Contract I/C - Contract cancelled - Call Safety to clear P-3', 'Contract TSC - 30-day notice to cancel', 'Contract TSC - Contract cancelled - Call Safety to clear P-3', 'Corporate Inspection - Need proof of repair', 'DOT roadside inspection - Need proof of repair', 'DOT roadside inspection - Submit report to Safety', 'Daily Inspection Rpt - Missing', 'Daily Inspection Rpt - Need repair receipt', 'Decommision - Driver on leave of absence', 'Decommision - Major refurbish', 'Decommision - Major repair needed', 'Decommision - No driver assigned to vehicle', 'Decommision - Registration problem', 'Decommision - Storage trailer', 'Decommision - Vehicle stolen or missing', 'Decommision - Vehicle to be sold', 'Fuel Tax - Decommissioned vehicle', 'Fuel Tax - Odometer broken - need repair', 'Fuel Tax - Report incomplete', 'Fuel Tax - Reports missing', 'Insurance - Auto liability expired', 'Insurance - General liability expired', 'Insurance - Non-trucking liability expired', 'Insurance - OCC/ACC expired', 'Insurance - Physical damage expired', 'Insurance - Umbrella expired', 'Insurance - Workers Comp. expired', 'Insurance - Incomplete (P-3 auto O/S new drivers', 'Periodic Inspect - Due', 'Periodic Inspect - Incomplete', 'Periodic Inspect - Need inspector certification form', 'Periodic Inspect - Photos due', 'Periodic Inspect - Photos inadequate or imcomplete', 'Periodic Inspect - Safety defect', 'Register - 2290 form due', 'Register - Inspection due', 'Register - Cab card incorrect unit number', 'Register - Cab card missing', 'Register - Inspection incomplete', 'Register - License plates expired', 'Register - Need copy of title', 'Register - Need copy of title application', 'Register - No driver assigned', 'Register - Owners info. Missing', 'Register - Photos due', 'Register - Photos inadequate', 'Register - Registration form incomplete', 'Register - Rental vehicle - Lease period expired', 'Safety - Brake lining @ 25% due for replacement', 'Safety - Brake lining below 25%', 'Safety - Brakers - Push road travel', 'Safety - Defect', 'Safety - Equipment violation', 'Safety - Investigation', 'Safety - Misc.', 'Safety - Reflective tape missing or inadequate', 'Safety - Tire depth below standar', 'Safety - Tires 30 day due', 'Terminate - Need equipment receipt form to terminate', 'Terminate from fleet', 'Vehicle ID - VIN # tag on vehicle missing', 'Vehicle ID - Appearance unacceptable', 'Vehicle ID - Color does not meet standard', 'Vehicle ID - Markings missing or incomplete', 'Vehicle ID - Transfer - need new unit #', 'Vehicle ID - Unauthorized marking'));
}

$field3 = Vtiger_Field::getInstance('outofservice_effective_date', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_OUTOFSERVICE_EFFECTIVE_DATE';
    $field3->name = 'outofservice_effective_date';
    $field3->table = 'vtiger_vehicleoutofservice';
    $field3->column = 'outofservice_effective_date';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~O';

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('outofservice_reinstated_date', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_OUTOFSERVICE_REINSTATED_DATE';
    $field4->name = 'outofservice_reinstated_date';
    $field4->table = 'vtiger_vehicleoutofservice';
    $field4->column = 'outofservice_reinstated_date';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('outofservice_comments', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OUTOFSERVICE_COMMENTS';
    $field5->name = 'outofservice_comments';
    $field5->table = 'vtiger_vehicleoutofservice';
    $field5->column = 'outofservice_comments';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 19;
    $field5->typeofdata = 'V~O';

    $block1->addField($field5);
}

/*
//Type – drop down – to be defined by Graebel
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
}*/

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


if ($vehiclesOutServIsNew) {
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
            //->addField($field5, 6);
}

// Add documents related list
if ($vehiclesOutServIsNew) {
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Out of Service', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleOutofService'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";