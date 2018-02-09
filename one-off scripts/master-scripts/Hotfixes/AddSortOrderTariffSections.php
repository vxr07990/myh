<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/7/2017
 * Time: 8:19 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('TariffSections');

if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_TARIFFSECTIONS_INFORMATION', $module);

if(!$block)
{
    return;
}
$field = Vtiger_Field::getInstance('tariffsection_sortorder', $module);
if ($field) {
    echo "The tariffsection_sortorder field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_TARIFFSECTION_SORTORDER';
    $field->name       = 'tariffsection_sortorder';
    $field->table      = 'vtiger_tariffsections';
    $field->column     = 'tariffsection_sortorder';
    $field->columntype = 'INT(10) NOT NULL';
    $field->uitype     = 7;
    $field->typeofdata = 'I~O';
    $block->addField($field);
}





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";