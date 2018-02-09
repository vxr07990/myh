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

echo "Adding new block and field to vtiger_quotes...<br/>\n";
// Get that db though.
$adb = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('Estimates');
$blockInstance = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $moduleInstance);

$field = Vtiger_Field::getInstance('local_origin_acc', $moduleInstance);
if(!$field) {
    echo "Adding LBL_LOCAL_ORIGIN_ACC field...<br/>\n";
    $field = new Vtiger_Field();
    $field->label = 'LBL_LOCAL_ORIGIN_ACC';
    $field->name = 'local_origin_acc';
    $field->table = 'vtiger_quotes';
    $field->column = 'local_origin_acc';
    $field->columntype = 'DECIMAL(10,2)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $blockInstance->addField($field);
}else{
    echo "LBL_LOCAL_ORIGIN_ACC already exists, skipping...<br/>\n";
}
// Done adding CWT/Quantity


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";