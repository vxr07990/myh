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

$module1 = Vtiger_Module::getInstance('TariffServices');
$field1 = Vtiger_Field::getInstance('is_discountable', $module1);
if ($field1) {
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldname = 'is_discountable'");
    echo "<h2>Deleting is_discountable field from TariffServices</h2><br>";
    Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_tariffservices` DROP COLUMN is_discountable");
    echo "<h2>Dropping 'is_discountable' column from TariffServices</h2><br>";
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_sectiondiscount')) {
    echo "<h2>Creating vtiger_quotes_sectiondiscount table</h2><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_sectiondiscount',
                              '(estimateid INT(11),
							    sectionid INT(11),
								discount_percent DECIMAL(4,1)
							    )', true);
}
$module2 = Vtiger_Module::getInstance('Estimates');

$block1 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module2);
$field2 = Vtiger_Field::getInstance('local_bl_discount', $module2);
if ($field2) {
    echo "<h3>The bottom_line_discount field already exists</h3><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_LOCALBLDISCOUNT';
    $field2->name = 'local_bl_discount';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'local_bl_discount';
    $field2->columntype = 'DECIMAL(12,3)';
    $field2->uitype = 7;
    $field2->typeofdata = 'NN~O';
    
    $block1->addField($field2);
}

$module3 = Vtiger_Module::getInstance('Quotes');

$block2 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module3);
$field3 = Vtiger_Field::getInstance('local_bl_discount', $module3);
if ($field3) {
    echo "<h3>The bottom_line_discount field already exists</h3><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_LOCALBLDISCOUNT';
    $field3->name = 'local_bl_discount';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'local_bl_discount';
    $field3->columntype = 'DECIMAL(12,3)';
    $field3->uitype = 7;
    $field3->typeofdata = 'NN~O';
    
    $block2->addField($field3);
}

$module4 = Vtiger_Module::getInstance('TariffSections');
$block3 = Vtiger_Block::getInstance('LBL_TARIFFSECTIONS_INFORMATION', $module4);
$field4 = Vtiger_Field::getInstance('is_discountable', $module4);
if ($field4) {
    echo "<h3>The is_discountable field already exists</h3><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TARIFFSECTIONS_ISDISCOUTABLE';
    $field4->name = 'is_discountable';
    $field4->table = 'vtiger_tariffsections';
    $field4->column = 'is_discountable';
    $field4->columntype = 'TINYINT(1)';
    $field4->uitype = 56;
    $field4->typeofdata = 'C~O';
    
    $block3->addField($field4);
}
