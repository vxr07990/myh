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

$moduleInstance = Vtiger_Module::getInstance('Tariffs');
if ($moduleInstance) {
    echo "<br> Tariffs module already exists <br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Tariffs';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<br> Block 'LBL_TARIFFMANAGER_INFORMATION' is already present <br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFMANAGER_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_ADMINISTRATIVE', $moduleInstance);
if ($blockInstance2) {
    echo "<br> Block 'LBL_TARIFFMANAGER_ADMINISTRATIVE' is already present <br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_TARIFFMANAGER_ADMINISTRATIVE';
    $moduleInstance->addBlock($blockInstance2);
}

$field0 = Vtiger_Field::getInstance('tariffmanagername', $moduleInstance);
if ($field0) {
    echo "<br> Field 'tariffmanagername' is already present <br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_TARIFFMANAGER_NAME';
    $field0->name = 'tariffmanagername';
    $field0->table = 'vtiger_tariffmanager';
    $field0->column = 'tariffmanagername';
    $field0->columntype = 'VARCHAR(50)';
    $field0->uitype = 2;
    $field0->typeofdata = 'V~M';
    $field0->presence = 0;
    $field0->summaryfield = 1;

    $blockInstance->addField($field0);
    $moduleInstance->setEntityIdentifier($field0);
}


$field1 = Vtiger_Field::getInstance('tariff_id', $moduleInstance);
if ($field1) {
    echo "<br> Field 'tariff_id' is already present <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TARIFFMANAGER_TARIFFID';
    $field1->name = 'tariff_id';
    $field1->table = 'vtiger_tariffmanager';
    $field1->column = 'tariff_id';
    $field1->columntype = 'VARCHAR(30)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $blockInstance->addField($field1);
}

$field2 = Vtiger_Field::getInstance('tariff_type', $moduleInstance);
if ($field2) {
    echo "<br> Field 'tariff_type' is already present <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TARIFFMANAGER_TYPE';
    $field2->name = 'tariff_type';
    $field2->table = 'vtiger_tariffmanager';
    $field2->column = 'tariff_type';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);

    $field2->setPicklistValues(array('Interstate', 'Intrastate'));
}


$field3 = Vtiger_Field::getInstance('rating_url', $moduleInstance);
if ($field3) {
    echo "<br> Field 'rating_url' is already present <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TARIFFMANAGER_RATINGURL';
    $field3->name = 'rating_url';
    $field3->table = 'vtiger_tariffmanager';
    $field3->column = 'rating_url';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';

    $blockInstance2->addField($field3);
}

$field4 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field4) {
    echo "<br> field createdtime is present <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TARIFFMANAGER_CREATEDTIME';
    $field4->name = 'createdtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $blockInstance2->addField($field4);
}

$field5 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field5) {
    echo "<br> field modifiedtime is present<br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TARIFFMANAGER_MODIFIEDTIME';
    $field5->name = 'modifiedtime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;
    $blockInstance2->addField($field5);
}

$field6 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field6) {
    echo "<br> field assigned_user_id is present<br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_TARIFFMANAGER_ASSIGNEDTO';
    $field6->name = 'assigned_user_id';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'smownerid';
    $field6->uitype = 53;
    $field6->typeofdata = 'V~M';

    $blockInstance2->addField($field6);
}

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field0)->addField($field6, 1)->addField($field4, 2);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

if (!Vtiger_Utils::CheckTable('vtiger_tariff_blockrel')) {
    echo "<h2>Creating vtiger_tariff_blockrel table</h2><br>";
    Vtiger_Utils::CreateTable('vtiger_tariff_blockrel',
                              '(tariffid INT(19),
							    blockid INT(19),
								show_block TINYINT(1)
							    )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";