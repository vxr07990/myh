<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/7/2017
 * Time: 10:41 AM
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

$module = Vtiger_Module::getInstance('MovingViolation');

if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_MOVINGVIOLATION_INFORMATION', $module);

if(!$block)
{
    return;
}

$field = Vtiger_Field::getInstance('movingviolation_status', $module);
if ($field) {
    echo "The movingviolation_status field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_MOVINGVIOLATION_STATUS';
    $field->name       = 'movingviolation_status';
    $field->table      = 'vtiger_movingviolation';
    $field->column     = 'movingviolation_status';
    $field->columntype = 'VARCHAR(50)';
    $field->uitype     = 16;
    $field->typeofdata = 'V~M';
    $block->addField($field);
    $field->setPicklistValues(['Open','Closed']);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";