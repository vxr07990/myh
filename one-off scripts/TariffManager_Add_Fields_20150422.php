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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('TariffManager');

$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<br> Block 'LBL_TARIFFMANAGER_INFORMATION' is already present <br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFMANAGER_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
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

    $adminBlock = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_ADMINISTRATIVE', $moduleInstance);

    $adminBlock->addField($field3);
}
