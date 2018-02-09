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



$moduleInstance = Vtiger_Module::getInstance('LicensePlateHistory');
$LicensePlateHistoryIsNew = false;
if ($moduleInstance) {
    echo "Module LicensePlateHistory already present - Updating Fields";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'LicensePlateHistory';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $LicensePlateHistoryIsNew = true;
}
// Field Setup
$blockName = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('licenseplatehistory_no', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_LICENSEPLATEHISTORY_NO';
    $field01->name = 'licenseplatehistory_no';
    $field01->table = 'vtiger_licenseplatehistory';
    $field01->column = 'licenseplatehistory_no';
    $field01->columntype = 'VARCHAR(25)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~O';
    $field01->summaryfield = 1;
    $block->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'LicensePlateHistory', 'LPH', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field001 = Vtiger_Field::getInstance('licenseplatehistory_vehicleid', $moduleInstance);
if (!$field001) {
    $field001 = new Vtiger_Field();
    $field001->label = 'LBL_LICENSEPLATEHISTORY_VEHICLE';
    $field001->name = 'licenseplatehistory_vehicleid';
    $field001->table = 'vtiger_licenseplatehistory';
    $field001->column = 'licenseplatehistory_vehicleid';
    $field001->columntype = 'INT(25)';
    $field001->uitype = 10;
    $field001->typeofdata = 'V~M';
    $field001->summaryfield = 0;
    $block->addField($field001);
    $field001->setRelatedModules(array('Vehicles'));
}

$moduleInstance->setEntityIdentifier($field01);

$field1 = Vtiger_Field::getInstance('licenseplatehistory_issuingstate', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LICENSEPLATEHISTORY_ISSUINGSTATE';
    $field1->name = 'licenseplatehistory_issuingstate';
    $field1->table = 'vtiger_licenseplatehistory';
    $field1->column = 'licenseplatehistory_issuingstate';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    $block->addField($field1);
    $field1->setPicklistValues(array('AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY'));
}

$field2 = Vtiger_Field::getInstance('licenseplatehistory_platenumber', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_LICENSEPLATEHISTORY_PLATENUMBER';
    $field2->name = 'licenseplatehistory_platenumber';
    $field2->table = 'vtiger_licenseplatehistory';
    $field2->column = 'licenseplatehistory_platenumber';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->summaryfield = 1;
    $block->addField($field2);
}


$field3 = Vtiger_Field::getInstance('licenseplatehistory_platetype', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_LICENSEPLATEHISTORY_PLATETYPE';
    $field3->name = 'licenseplatehistory_platetype';
    $field3->table = 'vtiger_licenseplatehistory';
    $field3->column = 'licenseplatehistory_platetype';
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 16;
    $field3->setPicklistValues(array('--', 'Truck', 'Trailer', 'Tractor', 'Tow truck', 'Commercial vehicle'));
    $field3->typeofdata = 'V~O';
    $field3->summaryfield = 1;
    $block->addField($field3);
}

$field4 = Vtiger_Field::getInstance('licenseplatehistory_issueddate', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_LICENSEPLATEHISTORY_ISSUEDDATE';
    $field4->name = 'licenseplatehistory_issueddate';
    $field4->table = 'vtiger_licenseplatehistory';
    $field4->column = 'licenseplatehistory_issueddate';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';
    $field4->summaryfield = 1;
    $block->addField($field4);
}


$field5 = Vtiger_Field::getInstance('licenseplatehistory_expirationdate', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_LICENSEPLATEHISTORY_EXPIRATIONDATE';
    $field5->name = 'licenseplatehistory_expirationdate';
    $field5->table = 'vtiger_licenseplatehistory';
    $field5->column = 'licenseplatehistory_expirationdate';
    $field5->columntype = 'DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';
    $field5->summaryfield = 1;
    $block->addField($field5);
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

    $block->addField($field36);
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

    $block->addField($field37);
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

    $block->addField($field38);
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

    $block->addField($agentField);
}

$block->save($module);

if ($LicensePlateHistoryIsNew) {

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field01)
            ->addField($field1, 2)
            ->addField($field2, 3)
            ->addField($field3, 4)
            ->addField($field4, 5)
            ->addField($field5, 6);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Trips
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'License Plate History', array('ADD'), 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";