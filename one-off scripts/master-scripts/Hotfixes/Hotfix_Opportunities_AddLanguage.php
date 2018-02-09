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



//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');
$modulePotentials = Vtiger_Module::getInstance('Potentials');

$blockOpportunities302 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $moduleOpportunities);
if ($blockOpportunities302) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_LEADDETAILS block already exists in Opportunities <br>";
} else {
    $blockOpportunities302 = new Vtiger_Block();
    $blockOpportunities302->label = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS';
    $moduleOpportunities->addBlock($blockOpportunities302);
}

$blockPotentials302 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $modulePotentials);
if ($blockPotentials302) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_LEADDETAILS block already exists in Potentials <br>";
} else {
    $blockPotentials302 = new Vtiger_Block();
    $blockPotentials302->label = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS';
    $modulePotentials->addBlock($blockPotentials302);
}

$field = Vtiger_Field::getInstance('preferred_language', $moduleOpportunities);
if ($field) {
    echo "<br> The language field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_LANGUAGE';
    $field->name = 'preferred_language';
    $field->table = 'vtiger_potential';
    $field->column ='preferred_language';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;
    $field->defaultvalue = 'English';

    $blockOpportunities302->addField($field);
    $field->setPicklistValues(['English', 'French']);
}
$field = Vtiger_Field::getInstance('preferred_language', $modulePotentials);
if ($field) {
    echo "<br> The language field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_LANGUAGE';
    $field->name = 'preferred_language';
    $field->table = 'vtiger_potential';
    $field->column ='preferred_language';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials302->addField($field);
}
/*Language Strings

    'LBL_OPPORTUNITIES_LANGUAGE' => 'Language',
    'LBL_OPPORTUNITIES_LANGUAGE' => 'Language',
*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";