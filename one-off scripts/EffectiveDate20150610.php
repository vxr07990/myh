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

$module2 = Vtiger_Module::getInstance('Estimates');

$block1 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module2);
$field2 = Vtiger_Field::getInstance('effective_date', $module2);
if ($field2) {
    echo "<h3>The effective_date field already exists</h3><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field2->name = 'effective_date';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'effective_date';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';
    
    $block1->addField($field2);
}

$module3 = Vtiger_Module::getINstance('Quotes');

$block2 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module3);
$field3 = Vtiger_Field::getInstance('effective_date', $module3);
if ($field3) {
    echo "<h3>The effective_date field already exists</h3><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field3->name = 'effective_date';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'effective_date';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~O';
    
    $block2->addField($field3);
}
