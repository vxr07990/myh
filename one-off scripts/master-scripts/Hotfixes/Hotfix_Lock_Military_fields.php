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

echo '<br />Checking if lock_military_fields field exists:<br />';

$oppsModule = Vtiger_Module::getInstance('Opportunities');
$potModule = Vtiger_Module::getInstance('Potentials');

$field1 = Vtiger_Field::getInstance('lock_military_fields', $oppsModule);
if ($field1) {
    echo "<br /> The lock_military_fields field already exists in Opportunities <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LOCKMILITARY';
    $field1->name = 'lock_military_fields';
    $field1->table = 'vtiger_potential';
    $field1->column = 'lock_military_fields';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;

    Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule)->addField($field1);
}

$field1 = Vtiger_Field::getInstance('lock_military_fields', $potModule);
if ($field1) {
    echo "<br /> The lock_military_fields field already exists in Potentials <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LOCKMILITARY';
    $field1->name = 'lock_military_fields';
    $field1->table = 'vtiger_potential';
    $field1->column = 'lock_military_fields';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;

    Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potModule)->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";