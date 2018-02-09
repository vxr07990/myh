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



$moduleInstance = Vtiger_Module::getInstance('Insurance');
$moduleIsNew = false;
if ($moduleInstance) {
    echo "<h2>Updating Module Fields</h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Insurance';
    $moduleInstance->save();
    echo "<h2>Creating Module Insurance and Updating Fields</h2><br>";
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleIsNew = true;
}


$block = Vtiger_Block::getInstance('LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION', $moduleInstance);
if ($block) {
    echo "<h3>The LBL_INSURANCE_INFORMATION block already exists</h3><br> \n";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
    $moduleInstance->addBlock($block);
}

// Field Setup


$field01 = Vtiger_Field::getInstance('insurance_insurance', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'I/C Insurance';
    $field01->name = 'insurance_insurance';
    $field01->table = 'vtiger_insurance';
    $field01->column = 'insurance_insurance';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Insurance', 'INS', 1, 1, 1));
}

$field1 = Vtiger_Field::getInstance('insurance_coverage', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->name = 'insurance_coverage';
    $field1->label = 'Coverage';
    $field1->uitype = 15;
    $field1->table = 'vtiger_insurance';
    $field1->column = $field1->name;
    $field1->summaryfield = 1;
    $field1->columntype = 'VARCHAR(255)';
    $field1->typeofdata = 'V~O';

    $block->addField($field1);
    
    $field1->setPicklistValues(array('Work Comp', 'NONTRKLIAB', 'OCC ACC', 'PHYS DAMAGE'));
}

$field2 = Vtiger_Field::getInstance('insurance_carriername', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->name = 'insurance_carriername';
    $field2->label = 'Carrier Name';
    $field2->uitype = 15;
    $field2->table = 'vtiger_insurance';
    $field2->column = $field2->name;
    $field2->summaryfield = 1;
    $field2->columntype = 'VARCHAR(255)';
    $field2->typeofdata = 'D~O';
    $block->addField($field2);
    $field2->setPicklistValues(array('---'));
}




$field3 = Vtiger_Field::getInstance('insurance_effectivedate', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->name = 'insurance_effectivedate';
    $field3->label = 'Effective Date';
    $field3->uitype = 5;
    $field3->table = 'vtiger_insurance';
    $field3->summaryfield = 1;
    $field3->column = $field3->name;
    $field3->columntype = 'DATE';
    $field3->typeofdata = 'D~O';
    $block->addField($field3);
}



$field4 = Vtiger_Field::getInstance('insurance_cuc', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->name = 'insurance_cuc';
    $field4->label = 'CUC';
    $field4->uitype = 56;
    $field4->table = 'vtiger_insurance';
    $field4->column = $field4->name;
    $field4->summaryfield = 1;
    $field4->columntype = 'VARCHAR(3)';
    $field4->typeofdata = 'V~O';
    $block->addField($field4);
}



$field5 = Vtiger_Field::getInstance('insurance_expirationdate', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->name = 'insurance_expirationdate';
    $field5->label = 'Expiration Date';
    $field5->uitype = 5;
    $field5->table = 'vtiger_insurance';
    $field5->column = $field5->name;
    $field5->summaryfield = 1;
    $field5->columntype = 'DATE';
    $field5->typeofdata = 'D~O';
    $block->addField($field5);
}



$field6 = Vtiger_Field::getInstance('insurance_renew', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->name = 'insurance_renew';
    $field6->label = 'Renew';
    $field6->uitype = 56;
    $field6->table = 'vtiger_insurance';
    $field6->column = $field6->name;
    $field6->summaryfield = 1;
    $field6->columntype = 'VARCHAR(3)';
    $field6->typeofdata = 'V~O';
    $block->addField($field6);
}



$field7 = Vtiger_Field::getInstance('insurance_policynumber', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->name = 'insurance_policynumber';
    $field7->label = 'Policy Number';
    $field7->uitype = 1;
    $field7->table = 'vtiger_insurance';
    $field7->summaryfield = 1;
    $field7->column = $field7->name;
    $field7->columntype = 'VARCHAR(100)';
    $field7->typeofdata = 'V~O';
    $block->addField($field7);
}

$fieldv3 = Vtiger_Field::getInstance('vendors_id', $moduleInstance);
if (!$fieldv3) {
    $fieldv3 = new Vtiger_Field();
    $fieldv3->label = 'Vendors';
    $fieldv3->name = 'vendors_id';
    $fieldv3->table = 'vtiger_insurance';
    $fieldv3->column = 'vendors_id';
    $fieldv3->columntype = 'INT(19)';
    $fieldv3->uitype = 10;
    $fieldv3->typeofdata = 'V~M';

    $block->addField($fieldv3);
    $fieldv3->setRelatedModules(array('Vendors'));
}


if ($moduleIsNew) {
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);
    
    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'createdtime';
    $mfield2->label = 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'T~O';
    $mfield2->displaytype = 2;
    $block->addField($mfield2);
    
    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'modifiedtime';
    $mfield3->label = 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'T~O';
    $mfield3->displaytype = 2;
    $block->addField($mfield3);
    
    $mfield4 = new Vtiger_Field();
    $mfield4->label = 'Owner';
    $mfield4->name = 'agentid';
    $mfield4->table = 'vtiger_crmentity';
    $mfield4->column = 'agentid';
    $mfield4->columntype = 'INT(11)';
    $mfield4->uitype = 1002;
    $mfield4->typeofdata = 'I~M';
    $block->addField($mfield4);

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1)->addField($field6, 2)->addField($mfield1, 3);

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Vendors
    $VendorsInstance = Vtiger_Module::getInstance('Vendors');
    $VendorsInstance->setRelatedList($moduleInstance, 'Insurance', array('ADD'), 'get_dependents_list');
    
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='' WHERE name='Insurance'"); // It's a Vendors related module. We dont need it under the main menu

    echo "OK\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";