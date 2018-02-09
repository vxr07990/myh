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


include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

$moduleOpp = Vtiger_Module::getInstance('Opportunities');
$modulePot = Vtiger_Module::getInstance('Potentials');

$blockOpp = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleOpp);
$blockPot = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $modulePot);

$field1 = Vtiger_Field::getInstance('order_number', $moduleOpp);
if ($field1) {
    echo "<br /> The order_number field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPP_ORDERNUMBER';
    $field1->name = 'order_number';
    $field1->table = 'vtiger_potential';
    $field1->column = 'order_number';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockOpp->addField($field1);
}

$field1 = Vtiger_Field::getInstance('order_number', $modulePot);
if ($field1) {
    echo "<br /> The order_number field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPP_ORDERNUMBER';
    $field1->name = 'order_number';
    $field1->table = 'vtiger_potential';
    $field1->column = 'order_number';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockPot->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";