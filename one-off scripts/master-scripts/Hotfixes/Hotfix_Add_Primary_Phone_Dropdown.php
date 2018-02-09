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

echo '<br />Checking if primary_phone already exists:<br />';

$userModule = Vtiger_Module::getInstance('Users');

$userBlock = Vtiger_Block::getInstance('LBL_MORE_INFORMATION', $userModule);

$field = Vtiger_Field::getInstance('primary_phone');

if ($field) {
    echo "<br> primary_phone already exists in Users";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_PRIMARY_PHONE';
    $field->name = 'primary_phone';
    $field->table = 'vtiger_users';
    $field->column = 'primary_phone';
    $field->columntype = 'VARCHAR(100)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 0;
    $field->presence = 2;

    $userBlock->addField($field);
    $picklistOptions = [
    'Home Phone',
    'Mobile Phone',
    'Office Phone',
    'Secondary Phone',
  ];
    $field->setPicklistValues($picklistOptions);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";