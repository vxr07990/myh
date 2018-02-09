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
//include_once('vtlib/Vtiger/Module.php');
//include_once('vtlib/Vtiger/Menu.php');

$moduleInstance = Vtiger_Module::getInstance('EffectiveDates');
if ($moduleInstance) {
    echo "<br> module Effective Dates already exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'EffectiveDates';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_EFFECTIVEDATES_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<br> block LBL_EFFECTIVEDATES_INFORMATION already exists <br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_EFFECTIVEDATES_INFORMATION';
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

$field1 = Vtiger_Field::getInstance('effective_date', $moduleInstance);
if ($field1) {
    echo "<br> field effective_date already exists <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Effective Date';
    $field1->name = 'effective_date';
    $field1->table = 'vtiger_effectivedates';
    $field1->column = 'effective_date';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~M';

    $blockInstance->addField($field1);
    
    $moduleInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('related_tariff', $moduleInstance);
if ($field2) {
    echo "<br> field related_tariff already exists <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Tariff';
    $field2->name = 'related_tariff';
    $field2->table = 'vtiger_effectivedates';
    $field2->column = 'related_tariff';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';

    $blockInstance->addField($field2);

    $field2->setRelatedModules(array('Tariffs'));
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field3) {
    echo "<br> field assigned_user_id already exists <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Assigned To';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';

    $blockInstance->addField($field3);
}

$field4 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field4) {
    echo "<br> field createdtime already exists <br>";
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
    echo "<br> field modifiedtime already exists <br>";
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


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

$moduleInstance = Vtiger_Module::getInstance('EffectiveDates');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('TariffServices'), 'Tariff Services', array('ADD'), 'get_dependents_list');

/*been handeled in tariffs script
$tariffInstance = Vtiger_Module::getInstance('Tariffs');
$relationLabel = 'Effective Dates';
$tariffInstance->setRelatedList($moduleInstance, $relationLabel, Array('Add'));*/;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";