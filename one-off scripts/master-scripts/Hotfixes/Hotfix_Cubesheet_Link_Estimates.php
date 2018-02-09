
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



require_once 'vtlib/Vtiger/Module.php';

$moduleEstimates = Vtiger_Module::getInstance('Estimates');
$field1 = Vtiger_Field::getInstance('cubesheet', $moduleEstimates);
$block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleEstimates);

if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_CUBESHEET';
    $field1->name = 'cubesheet';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'cubesheet';
    $field1->columntype = 'INT(11)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 2;
    $field1->presence = 2;

    $block->addField($field1);
    $field1->setRelatedModules(array('Cubesheets'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";