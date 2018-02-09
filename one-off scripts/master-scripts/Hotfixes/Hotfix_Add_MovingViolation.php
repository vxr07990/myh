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

$moduleInstance = Vtiger_Module::getInstance('MovingViolation');
$moduleIsNew = false;
if ($moduleInstance) {
    echo "Module MovingViolation already present - Updating Fields";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'MovingViolation';
    $moduleInstance->parent = '';
    $moduleInstance->save();
    echo "<h2>Creating Module Moving Violation and Updating Fields</h2><br>";
    $moduleInstance->initTables();
    $moduleIsNew = true;
}

$block = Vtiger_Block::getInstance('LBL_MOVINGVIOLATION_INFORMATION', $moduleInstance);
if ($block) {
    echo "<h3>The LBL_MOVINGVIOLATION_INFORMATION block already exists</h3><br> \n";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_MOVINGVIOLATION_INFORMATION';
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('movingviolation_movingviolation', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'I/C Moving Violation';
    $field01->name = 'movingviolation_movingviolation';
    $field01->table = 'vtiger_movingviolation';
    $field01->column = 'movingviolation_movingviolation';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);
}

$field0 = Vtiger_Field::getInstance('movingviolation_employeeid', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_MOVINGVIOLATION_CONVICTIONTYPE';
    $field0->name = 'movingviolation_employeeid';
    $field0->table = 'vtiger_movingviolation';
    $field0->column = 'movingviolation_employeeid';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';
    $field0->summaryfield = 1;
    $block->addField($field0);
    $field0->setRelatedModules(array('Employees'));
}


$field1 = Vtiger_Field::getInstance('movingviolation_convictiontype', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_MOVINGVIOLATION_CONVICTIONTYPE';
    $field1->name = 'movingviolation_convictiontype';
    $field1->table = 'vtiger_movingviolation';
    $field1->column = 'movingviolation_convictiontype';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    $block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('movingviolation_vehicletype', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_MOVINGVIOLATION_VEHICLETYPE';
    $field2->name = 'movingviolation_vehicletype';
    $field2->table = 'vtiger_movingviolation';
    $field2->column = 'movingviolation_vehicletype';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->summaryfield = 1;
    $block->addField($field2);
}


$field3 = Vtiger_Field::getInstance('movingviolation_convictiondate', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_MOVINGVIOLATION_CONVICTIONDATE';
    $field3->name = 'movingviolation_convictiondate';
    $field3->table = 'vtiger_movingviolation';
    $field3->column = 'movingviolation_convictiondate';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~O';
    $field3->summaryfield = 1;
    $block->addField($field3);
}


$field4 = Vtiger_Field::getInstance('movingviolation_infosource', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_MOVINGVIOLATION_INFOSOURCE';
    $field4->name = 'movingviolation_infosource';
    $field4->table = 'vtiger_movingviolation';
    $field4->column = 'movingviolation_infosource';
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';
    $field4->summaryfield = 1;
    $block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('movingviolation_comments', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_MOVINGVIOLATION_COMMENTS';
    $field5->name = 'movingviolation_comments';
    $field5->table = 'vtiger_movingviolation';
    $field5->column = 'movingviolation_comments';
    $field5->columntype = 'TEXT';
    $field5->uitype = 19;
    $field5->typeofdata = 'V~O';
    $block->addField($field5);
    $field5->summaryfield = 1;
}

if ($moduleIsNew) {
    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'MovingViolation', 'MV', 1, 1, 1));


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

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);

    //Relate to Employees
    $EmployeesInstance = Vtiger_Module::getInstance('Employees');
    $EmployeesInstance->setRelatedList($moduleInstance, 'Moving Violation', array('ADD'), 'get_dependents_list');

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='' WHERE name='MovingViolation'");

    echo "OK\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";