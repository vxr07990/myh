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

$potBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $potModule);
$oppBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $oppModule);

$field = Vtiger_Field::getInstance('segment', $potModule);
if ($field) {
    echo "<br />Field segment already exists in Potentials module<br />";
} else {
    echo "<br />Adding field segment to Potentials module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENT';
    $field->name = 'segment';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $potBlock->addField($field);
}

$field = Vtiger_Field::getInstance('segment', $oppModule);
if ($field) {
    echo "<br />Field segment already exists in Opportunities module<br />";
} else {
    echo "<br />Adding field segment to Opportunities module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENT';
    $field->name = 'segment';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $oppBlock->addField($field);
}

$field = Vtiger_Field::getInstance('segment_used', $potModule);
if ($field) {
    echo "<br />Field segment_used already exists in Potentials module<br />";
} else {
    echo "<br />Adding field segment_used to Potentials module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENTUSED';
    $field->name = 'segment_used';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment_used';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype = 56;
    $field->typeofdata = 'V~O';

    $potBlock->addField($field);
}

$field = Vtiger_Field::getInstance('segment_used', $oppModule);
if ($field) {
    echo "<br />Field segment_used already exists in Opportunities module<br />";
} else {
    echo "<br />Adding field segment_used to Opportunities module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENTUSED';
    $field->name = 'segment_used';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment_used';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype = 56;
    $field->typeofdata = 'V~O';

    $oppBlock->addField($field);
}

$field = Vtiger_Field::getInstance('segment_desc', $potModule);
if ($field) {
    echo "<br />Field segment_desc already exists in Potentials module<br />";
} else {
    echo "<br />Adding field segment_desc to Potentials module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENTDESCRIPTION';
    $field->name = 'segment_desc';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment_desc';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O';

    $potBlock->addField($field);
}

$field = Vtiger_Field::getInstance('segment_desc', $oppModule);
if ($field) {
    echo "<br />Field segment_desc already exists in Opportunities module<br />";
} else {
    echo "<br />Adding field segment_desc to Opportunities module<br />";
    $field = new Vtiger_Field();
    $field->label = 'LBL_POTENTIALS_SEGMENTDESCRIPTION';
    $field->name = 'segment_desc';
    $field->table = 'vtiger_potentialscf';
    $field->column = 'segment_desc';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O';

    $oppBlock->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";