<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1.2;
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

echo "Adding new block and field to vtiger_tariffservices...<br/>\n";
// Get that db though.
$adb = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('Contracts');
$blockInstance = Vtiger_Block::getInstance('LBL_CONTRACTS_INFORMATION', $moduleInstance);

$field = Vtiger_Field::getInstance('move_type', $moduleInstance);
if(!$field) {
    echo "Adding LBL_CONTRACTS_MOVETYPE field...<br/>\n";
    $field = new Vtiger_Field();
    $field->label = 'LBL_CONTRACTS_MOVETYPE';
    $field->name = 'move_type';
    $field->table = 'vtiger_contracts';
    $field->column = 'move_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $blockInstance->addField($field);
}else{
    echo "LBL_CONTRACTS_MOVETYPE already exists, skipping...<br/>\n";
}
// Done adding field

//@TODO, make the sequence correct to make it appear above notes.


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";