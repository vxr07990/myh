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
    echo "<br> module already exists <br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Tariffs';
    $moduleInstance->save();

    $moduleInstance->initTables();

//$menu = Vtiger_Menu::getInstance('COMPANY_ADMIN_TAB');
//$menu->addModule($moduleInstance);
}

$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<br> block LBL_TARIFFS_INFORMATION already exists <br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<br> block LBL_CUSTOM_INFORMATION already exists <br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}


$field1 = Vtiger_Field::getInstance('tariff_name', $moduleInstance);
if ($field1) {
    echo "<br> field tariff_name already exists";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Tariff Name';
    $field1->name = 'tariff_name';
    $field1->table = 'vtiger_tariffs';
    $field1->column = 'tariff_name';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $blockInstance->addField($field1);
    
    $moduleInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('tariff_state', $moduleInstance);
if ($field2) {
    echo "<br> field tariff_state already exists <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Tariff State';
    $field2->name = 'tariff_state';
    $field2->table = 'vtiger_tariffs';
    $field2->column = 'tariff_state';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 2;
    $field2->typeofdata = 'V~M';

    $blockInstance->addField($field2);
}
// This field is unnecessary the assigned to field has the same functionality
// $field3 = Vtiger_Field::getInstance('related_agent',$moduleInstance);
// if($field3)
// {
    // echo "<br> field related_agent already exists <br>";
// }
// else {
// $field3 = new Vtiger_Field();
// $field3->label = 'Agent';
// $field3->name = 'related_agent';
// $field3->table = 'vtiger_tariffs';
// $field3->column = 'related_agent';
// $field3->columntype = 'INT(19)';
// $field3->uitype = 10;
// $field3->typeofdata = 'V~M';

// $blockInstance->addField($field3);

// $field3->setRelatedModules(Array('Agents'));
// }
/*
$field4 = Vtiger_Field::getInstance('')
$field4 = new Vtiger_Field();
$field4->label = 'Commodity';
$field4->name = 'commodity_type';
$field4->table = 'vtiger_tariffs';
$field4->column = 'commodity_type';
$field4->columntype = 'VARCHAR(255)';
$field4->uitype = 16;
$field4->typeofdata = 'V~M';

$blockInstance->addField($field4);

$field4->setPicklistValues( Array ('HHG', 'Comm. Goods', 'Truckload') );*/

$field5 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field5) {
    echo "<br> field assigned_user_id already exists <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Assigned To';
    $field5->name = 'assigned_user_id';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'smownerid';
    $field5->uitype = 53;
    $field5->typeofdata = 'V~M';

    $blockInstance->addField($field5);
}

$field6 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field6) {
    echo "<br> field createdtime already exists <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'Created Time';
    $field6->name = 'createdtime';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'createdtime';
    $field6->uitype = 70;
    $field6->typeofdata = 'T~O';
    $field6->displaytype = 2;

    $blockInstance->addField($field6);
}

$field7 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field7) {
    echo "<br> field modifiedtime already exists <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'Modified Time';
    $field7->name = 'modifiedtime';
    $field7->table = 'vtiger_crmentity';
    $field7->column = 'modifiedtime';
    $field7->uitype = 70;
    $field7->typeofdata = 'T~O';
    $field7->displaytype = 2;

    $blockInstance->addField($field7);
}


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

$moduleInstance = Vtiger_Module::getInstance('Tariffs');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('TariffSections'), 'Tariff Sections', array('ADD'), 'get_dependents_list');

$moduleInstance = Vtiger_Module::getInstance('Tariffs');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('EffectiveDates'), 'Effective Dates', array('ADD'), 'get_dependents_list');

echo "<br> tariffs complete. <br>";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";