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

echo '<br />Checking if converted_from field exists:<br />';

$opportunitiesQuotes = Vtiger_Module::getInstance('Opportunities');

$block = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $opportunitiesQuotes);
if (!$block) {
    $block = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $opportunitiesQuotes);
}

$field1 = Vtiger_Field::getInstance('converted_from', $opportunitiesQuotes);

if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPPORTUNITIES_OPPORTUNITYTOLEAD';
    $field1->name = 'converted_from';
    $field1->table = 'vtiger_potential';
    $field1->column = 'converted_from';
    $field1->columntype = 'INT(11)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->presence = 2;

    $block->addField($field1);
    $field1->setRelatedModules(array('Leads'));
}

$opportunitiesQuotes = Vtiger_Module::getInstance('Potentials');

$block = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $opportunitiesQuotes);

if (!$block) {
    $block = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $opportunitiesQuotes);
}

$field1 = Vtiger_Field::getInstance('converted_from', $opportunitiesQuotes);

if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPPORTUNITIES_OPPORTUNITYTOLEAD';
    $field1->name = 'converted_from';
    $field1->table = 'vtiger_potential';
    $field1->column = 'converted_from';
    $field1->columntype = 'INT(11)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->presence = 2;

    $block->addField($field1);
    $field1->setRelatedModules(array('Leads'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";