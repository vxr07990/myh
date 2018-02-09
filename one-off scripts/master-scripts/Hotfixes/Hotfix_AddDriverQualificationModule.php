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



$MODULENAME = 'DriverQualification';
$isNewModule = false;
$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance) {
    echo "Module already present - Updating the fields.";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();

    $isNewModule = true;
}


$blockDriver = Vtiger_Block::getInstance('LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION', $moduleInstance);
if ($blockDriver) {
    echo "<h3>The LBL_DRIVERQUALIFICATON_INFORMATION block already exists</h3><br> \n";
} else {
    $blockDriver = new Vtiger_Block();
    $blockDriver->label = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
    $moduleInstance->addBlock($blockDriver);
}


$field01 = Vtiger_Field::getInstance('driverqualificationnumber', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'Driver Qualification Number';
    $field01->name = 'driverqualificationnumber';
    $field01->table = 'vtiger_driverqualification';
    $field01->column = $field01->name;
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $blockDriver->addField($field01);


    $moduleInstance->setEntityIdentifier($field01);

    $db = PearDatabase::getInstance();

    $result = $db->pquery('SELECT * FROM vtiger_modentity_num WHERE semodule=?', array('DriverQualification'));
    if ($result && $db->num_rows($result) == 0) {
        $numid = $db->getUniqueId("vtiger_modentity_num");
        $db->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'DriverQualification', 'DRIQUA', 1, 1, 1));
    }


    $result = $db->pquery('SELECT * FROM vtiger_modentity_num WHERE semodule=?', array('Employees'));
    if ($result && $db->num_rows($result) == 0) {
        $numid = $db->getUniqueId("vtiger_modentity_num");
        $db->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Employees', 'DRIVER', 1, 1, 1));
    }
}

$field1 = Vtiger_Field::getInstance('driverapplicationdate', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->name = 'driverapplicationdate';
    $field1->label = 'Driver Application Date';
    $field1->uitype = 5;
    $field1->table = 'vtiger_driverqualification';
    $field1->column = $field1->name;
    $field1->summaryfield = 1;
    $field1->columntype = 'date';
    $field1->typeofdata = 'D~O';
    $blockDriver->addField($field1);
}



$field2 = Vtiger_Field::getInstance('driverqualificationdate', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->name = 'driverqualificationdate';
    $field2->label = 'Driver Qualification Date';
    $field2->uitype = 5;
    $field2->table = 'vtiger_driverqualification';
    $field2->column = $field2->name;
    $field2->summaryfield = 1;
    $field2->columntype = 'date';
    $field2->typeofdata = 'D~O';
    $blockDriver->addField($field2);
}

$field4 = Vtiger_Field::getInstance('driverauthority', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->name = 'driverauthority';
    $field4->label = 'Authority';
    $field4->uitype = 15;
    $field4->table = 'vtiger_driverqualification';
    $field4->column = $field4->name;
    $field4->summaryfield = 1;
    $field4->columntype = 'VARCHAR(255)';
    $field4->typeofdata = 'V~O';
    $field4->setPicklistValues(array('International', 'Interstate', 'Intrastate', 'Local'));
    $blockDriver->addField($field4);
}



$field5 = Vtiger_Field::getInstance('driverrole', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->name = 'driverrole';
    $field5->label = 'Role';
    $field5->uitype = 15;
    $field5->table = 'vtiger_driverqualification';
    $field5->column = $field5->name;
    $field5->summaryfield = 1;
    $field5->columntype = 'VARCHAR(255)';
    $field5->typeofdata = 'V~O';
    $field5->setPicklistValues(array('Driver', 'Co-Driver Only', 'Co-Driver Trainee', 'Local Shuttle Driver Only', 'Utility Driver - no HHG experience', 'NON CDL Rental Courier Driver'));
    $blockDriver->addField($field5);
}


$field11 = Vtiger_Field::getInstance('lastannualreviewdate', $moduleInstance);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->name = 'lastannualreviewdate';
    $field11->label = 'Last Annual Review';
    $field11->uitype = 5;
    $field11->table = 'vtiger_driverqualification';
    $field11->column = $field11->name;
    $field01->summaryfield = 1;
    $field11->columntype = 'date';
    $field11->typeofdata = 'D~O';
    $blockDriver->addField($field11);
}


$field12 = Vtiger_Field::getInstance('annualreviewdue', $moduleInstance);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->name = 'annualreviewdue';
    $field12->label = 'Annual Review Due';
    $field12->uitype = 5;
    $field12->table = 'vtiger_driverqualification';
    $field12->column = $field12->name;
    $field01->summaryfield = 1;
    $field12->columntype = 'date';
    $field12->typeofdata = 'D~O';
    $blockDriver->addField($field12);
}


$field13 = Vtiger_Field::getInstance('codrivername', $moduleInstance);
if (!$field13) {
    $field13 = new Vtiger_Field();
    $field13->name = 'codrivername';
    $field13->label = 'Co-Driver Name';
    $field13->uitype = 1;
    $field13->table = 'vtiger_driverqualification';
    $field13->column = $field13->name;
    $field13->columntype = 'VARCHAR(100)';
    $field13->typeofdata = 'V~O~LE~100';
    $blockDriver->addField($field13);
}


$field14 = Vtiger_Field::getInstance('codrivernumber', $moduleInstance);
if (!$field14) {
    $field14 = new Vtiger_Field();
    $field14->name = 'codrivernumber';
    $field14->label = 'Co-Driver Number';
    $field14->uitype = 1;
    $field14->table = 'vtiger_driverqualification';
    $field14->column = $field14->name;
    $field14->columntype = 'VARCHAR(100)';
    $field14->typeofdata = 'V~O~LE~100';
    $blockDriver->addField($field14);
}


$field16 = Vtiger_Field::getInstance('employeesid', $moduleInstance);
if (!$field16) {
    $field16 = new Vtiger_Field();
    $field16->name = 'employeesid';
    $field16->label = 'Related To';
    $field16->uitype = 10;
    $field16->table = 'vtiger_driverqualification';
    $field16->column = $field16->name;
    $field16->columntype = 'int(11)';
    $field16->typeofdata = 'V~O';
    $blockDriver->addField($field16);
    $field16->setRelatedModules(array('Employees'));
}


$field22 = Vtiger_Field::getInstance('coments', $moduleInstance);
if (!$field22) {
    $field22 = new Vtiger_Field();
    $field22->name = 'coments';
    $field22->label = 'Coments';
    $field22->uitype = 21;
    $field22->table = 'vtiger_driverqualification';
    $field22->column = $field22->name;
    $field22->columntype = 'text';
    $field22->typeofdata = 'V~O';
    $blockDriver->addField($field22);
}


$blockExamination = Vtiger_Block::getInstance('LBL_EXAMINATION_INFORMATION', $moduleInstance);
if ($blockExamination) {
    echo "<h3>The LBL_EXAMINATION_INFORMATION block already exists</h3><br> \n";
} else {
    $blockExamination = new Vtiger_Block();
    $blockExamination->label = 'LBL_EXAMINATION_INFORMATION';
    $moduleInstance->addBlock($blockExamination);
}


$field9 = Vtiger_Field::getInstance('physicaldate', $moduleInstance);
if (!$field9) {
    $field9 = new Vtiger_Field();
    $field9->name = 'physicaldate';
    $field9->label = 'Physical Date';
    $field9->uitype = 5;
    $field9->table = 'vtiger_driverqualification';
    $field9->column = $field9->name;
    $field9->summaryfield = 1;
    $field9->columntype = 'date';
    $field9->typeofdata = 'D~O';
    $blockExamination->addField($field9);
}



$field10 = Vtiger_Field::getInstance('physicalexpirationdate', $moduleInstance);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->name = 'physicalexpirationdate';
    $field10->label = 'Physical Expiration Date';
    $field10->uitype = 5;
    $field10->table = 'vtiger_driverqualification';
    $field10->column = $field10->name;
    $field10->summaryfield = 1;
    $field10->columntype = 'date';
    $field10->typeofdata = 'D~O';
    $blockExamination->addField($field10);
}



$field7 = Vtiger_Field::getInstance('mvrdate', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->name = 'mvrdate';
    $field7->label = 'MVR Date';
    $field7->uitype = 5;
    $field7->table = 'vtiger_driverqualification';
    $field7->column = $field7->name;
    $field7->summaryfield = 1;
    $field7->columntype = 'date';
    $field7->typeofdata = 'D~O';
    $blockExamination->addField($field7);
}



$field8 = Vtiger_Field::getInstance('mvrexpirationdate', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->name = 'mvrexpirationdate';
    $field8->label = 'MVR Expiration Date';
    $field8->uitype = 5;
    $field8->table = 'vtiger_driverqualification';
    $field8->column = $field8->name;
    $field8->summaryfield = 1;
    $field8->columntype = 'date';
    $field8->typeofdata = 'D~O';
    $blockExamination->addField($field8);
}


$blockDrug = Vtiger_Block::getInstance('LBL_DRUG_INFORMATION', $moduleInstance);
if ($blockDrug) {
    echo "<h3>The LBL_DRUG_INFORMATION block already exists</h3><br> \n";
} else {
    $blockDrug = new Vtiger_Block();
    $blockDrug->label = 'LBL_DRUG_INFORMATION';
    $moduleInstance->addBlock($blockDrug);
}


$field15 = Vtiger_Field::getInstance('drugprogramtype', $moduleInstance);
if (!$field15) {
    $field15 = new Vtiger_Field();
    $field15->name = 'drugprogramtype';
    $field15->label = 'Drug Program Type';
    $field15->uitype = 15;
    $field15->table = 'vtiger_driverqualification';
    $field15->column = $field15->name;
    $field15->summaryfield = 1;
    $field15->columntype = 'VARCHAR(255)';
    $field15->typeofdata = 'V~O';
    $blockDrug->addField($field15);
}


$field17 = Vtiger_Field::getInstance('pedrugscreen', $moduleInstance);
if (!$field17) {
    $field17 = new Vtiger_Field();
    $field17->name = 'pedrugscreen';
    $field17->label = 'PE Drug Screen';
    $field17->uitype = 5;
    $field17->summaryfield = 1;
    $field17->table = 'vtiger_driverqualification';
    $field17->column = $field17->name;
    $field17->columntype = 'date';
    $field17->typeofdata = 'D~O';
    $blockDrug->addField($field17);
}


$field18 = Vtiger_Field::getInstance('lastdrugscreen', $moduleInstance);
if (!$field18) {
    $field18 = new Vtiger_Field();
    $field18->name = 'lastdrugscreen';
    $field18->label = 'Last Drug Screen';
    $field18->uitype = 5;
    $field18->table = 'vtiger_driverqualification';
    $field18->column = $field18->name;
    $field18->columntype = 'date';
    $field18->typeofdata = 'D~O';
    $blockDrug->addField($field18);
}


$field19 = Vtiger_Field::getInstance('drugscreentype', $moduleInstance);
if (!$field19) {
    $field19 = new Vtiger_Field();
    $field19->name = 'drugscreentype';
    $field19->label = 'Drug Screen Type';
    $field19->uitype = 15;
    $field19->table = 'vtiger_driverqualification';
    $field19->column = $field19->name;
    $field01->summaryfield = 1;
    $field19->columntype = 'VARCHAR(255)';
    $field19->typeofdata = 'V~O';
    $blockDrug->addField($field19);
}


$blockOrientation = Vtiger_Block::getInstance('LBL_ORIENTATION_INFORMATION', $moduleInstance);
if ($blockOrientation) {
    echo "<h3>The LBL_ORIENTATION_INFORMATION block already exists</h3><br> \n";
} else {
    $blockOrientation = new Vtiger_Block();
    $blockOrientation->label = 'LBL_ORIENTATION_INFORMATION';
    $moduleInstance->addBlock($blockOrientation);
}


$field20 = Vtiger_Field::getInstance('orientationdate', $moduleInstance);
if (!$field20) {
    $field20 = new Vtiger_Field();
    $field20->name = 'orientationdate';
    $field20->label = 'Orientation Date';
    $field20->uitype = 5;
    $field20->table = 'vtiger_driverqualification';
    $field20->column = $field20->name;
    $field01->summaryfield = 1;
    $field20->columntype = 'date';
    $field20->typeofdata = 'D~O';
    $blockOrientation->addField($field20);
}



$field21 = Vtiger_Field::getInstance('orientationexpitariondate', $moduleInstance);
if (!$field21) {
    $field21 = new Vtiger_Field();
    $field21->name = 'orientationexpitariondate';
    $field21->label = 'Orientation Expiration Date';
    $field21->uitype = 5;
    $field01->summaryfield = 1;
    $field21->table = 'vtiger_driverqualification';
    $field21->column = $field21->name;
    $field21->columntype = 'date';
    $field21->typeofdata = 'D~O';
    $blockOrientation->addField($field21);
}


if ($isNewModule) {


    // Recommended common fields every Entity module should have (linked to core table)
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $blockDriver->addField($mfield1);


    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'CreatedTime';
    $mfield2->label = 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'T~O';
    $mfield2->displaytype = 2;
    $blockDriver->addField($mfield2);

    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'ModifiedTime';
    $mfield3->label = 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'T~O';
    $mfield3->displaytype = 2;
    $blockDriver->addField($mfield3);

    $mfield4 = new Vtiger_Field();
    $mfield4->label = 'Owner';
    $mfield4->name = 'agentid';
    $mfield4->table = 'vtiger_crmentity';
    $mfield4->column = 'agentid';
    $mfield4->columntype = 'INT(11)';
    $mfield4->uitype = 1002;
    $mfield4->typeofdata = 'I~M';
    $blockDriver->addField($mfield4);

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1)->addField($field4, 2)->addField($mfield1, 3);

    // Webservice Setup
    $moduleInstance->initWebservice();

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='' WHERE name='DriverQualification'"); // It's a Vendors related module. We dont need it under the main menu
    //Relate to Employees


    echo "OK\n";
}


$adb = PearDatabase::getInstance();

$employeesInstance = Vtiger_Module::getInstance('Employees');

$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($employeesInstance->id, $moduleInstance->id));

if ($result && $adb->num_rows($result) == 0) {
    $employeesInstance->setRelatedList($moduleInstance, 'Driver Qualification', array('ADD'), 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";