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



$potModule = Vtiger_Module::getInstance('Potentials');
$oppModule = Vtiger_Module::getInstance('Opportunities');

$potBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potModule);
$oppBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppModule);

$field = Vtiger_Field::getInstance('sent_to_mobile', $potModule);
if ($field) {
    echo "<br />Field sent_to_mobile already exists in Potentials module<br />";
} else {
    echo "<br />Adding field sent_to_mobile to Potentials module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SENTTOMOBILE';
    $field->name = 'sent_to_mobile';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'sent_to_mobile';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';

    $potBlock->addField($field);
}

$field = Vtiger_Field::getInstance('sent_to_mobile', $oppModule);
if ($field) {
    echo "<br />Field sent_to_mobile already exists in Opportunities module<br />";
} else {
    echo "<br />Adding field sent_to_mobile to Opportunities module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SENTTOMOBILE';
    $field->name = 'sent_to_mobile';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'sent_to_mobile';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->sequence = 12;

    $oppBlock->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";