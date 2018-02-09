<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/7/2017
 * Time: 10:18 AM
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


$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Employees');

if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $module);
if(!$block)
{
    return;
}

$field = Vtiger_Field::getInstance('fleet_type', $module);
$seq = 2;
if($field)
{
    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                [1, $field->id]);
    $res = $db->pquery('SELECT sequence FROM vtiger_field WHERE fieldid=?',
                       [$field->id]);
    if($res && $row = $res->fetchRow())
    {
        $seq = $row['sequence'];
    }
}

$field = Vtiger_Field::getInstance('skills_level_completed', $module);
if ($field) {
    echo "The skills_level_completed field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EMPLOYEES_SKILLS_LEVEL_COMPLETED';
    $field->name       = 'skills_level_completed';
    $field->table      = 'vtiger_employees';
    $field->column     = 'skills_level_completed';
    $field->columntype = 'VARCHAR(50)';
    $field->uitype     = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = $seq;
    $block->addField($field);
    $field->setPicklistValues(['HHG', 'O&I']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";