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

$module = Vtiger_Module::getInstance('Opportunities');

$block = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $module);

$field1 = Vtiger_Field::getInstance('appointment_type', $module);

if ($field1) {
    echo "<br> Field 'appointment_type' is already present <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPPORTUNITIES_APPOINTMENTTYPE';
    $field1->name = 'appointment_type';
    $field1->table = 'vtiger_potential';
    $field1->column = 'appointment_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block->addField($field1);

    $field1->setPicklistValues(array('QLAB', 'CAS', 'AAS'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";