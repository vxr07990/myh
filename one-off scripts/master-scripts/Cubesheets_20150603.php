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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
$isNew = false;

$moduleInstance = Vtiger_Module::getInstance('Cubesheets');
if ($moduleInstance) {
    echo "<h2>CubeSheets already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Cubesheets';
    $moduleInstance->save();

    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_CUBESHEETS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_CUBESHEETS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
    $isNew = true;
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field0 = Vtiger_Field::getInstance('cubesheet_name', $moduleInstance);
if ($field0) {
    "<li>The cubesheet_name field already exists</li><br> \n";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_CUBESHEETS_NAME';
    $field0->name = 'cubesheet_name';
    $field0->table = 'vtiger_cubesheets';
    $field0->column = 'cubesheet_name';
    $field0->columntype = 'VARCHAR(255)';
    $field0->uitype = 2;
    $field0->typeofdata = 'V~M';
    $field0->summaryfield = 1;

    $blockInstance->addField($field0);

    $moduleInstance->setEntityIdentifier($field0);
}

$field1 = Vtiger_Field::getInstance('contact_id', $moduleInstance);
if ($field1) {
    "<li>The contact_id field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CUBESHEETS_CONTACT';
    $field1->name = 'contact_id';
    $field1->table = 'vtiger_cubesheets';
    $field1->column = 'contact_id';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);

    $field1->setRelatedModules(array('Contacts'));
}

$field2 = Vtiger_Field::getInstance('potential_id', $moduleInstance);
if ($field2) {
    "<li>The potential_id field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CUBESHEETS_OPPORTUNITY';
    $field2->name = 'potential_id';
    $field2->table = 'vtiger_cubesheets';
    $field2->column = 'potential_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);

    $field2->setRelatedModules(array('Opportunities'));
}


$field03 = Vtiger_Field::getInstance('is_primary', $moduleInstance);
if ($field03) {
    "<li>The is_primary field already exists</li><br> \n";
} else {
    $field03 = new Vtiger_Field();
    $field03->label = 'LBL_CUBESHEETS_ISPRIMARY';
    $field03->name = 'is_primary';
    $field03->table = 'vtiger_cubesheets';
    $field03->column = 'is_primary';
    $field03->columntype = 'VARCHAR(3)';
    $field03->uitype = 56;
    $field03->typeofdata = 'C~O';
    $field03->summaryfield = 1;

    $blockInstance->addField($field03);
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field3) {
    "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Surveyor';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';
    $field3->summaryfield = 1;

    $blockInstance->addField($field3);
}

$field4 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field4) {
    "<li>The createdtime field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'Created Time';
    $field4->name = 'createdtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $blockInstance->addField($field4);
}

$field5 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field5) {
    "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Modified Time';
    $field5->name = 'modifiedtime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;

    $blockInstance->addField($field5);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3);

    $moduleInstance->setDefaultSharing();

    $moduleInstance->initWebservice();

    /*$relatedInstance = Vtiger_Module::getInstance('Opportunities');
    $relationLabel = 'Surveys';
    $relatedInstance->setRelatedList($moduleInstance, $relationLabel, Array('Add'));*/

    $relatedInstance = Vtiger_Module::getInstance('Contacts');
    $relationLabel = 'Surveys';
    $relatedInstance->setRelatedList($moduleInstance, $relationLabel, array('Add'));

    $relatedInstance = Vtiger_Module::getInstance('Accounts');
    $relationLabel = 'Surveys';
    $relatedInstance->setRelatedList($moduleInstance, $relationLabel);

    $opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
    $opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Cubesheets'), 'Surveys', array('ADD'), 'get_related_list');
}

echo "<br> cubesheets complete. <br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";