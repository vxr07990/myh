<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/3/2017
 * Time: 9:13 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Opportunities');

if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $module);

if(!$block)
{
    return;
}

$fieldNames = ['amount','probability'];

$db = &PearDatabase::getInstance();

foreach($fieldNames as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $module);

    if(!$field)
    {
        continue;
    }

    $db->pquery('UPDATE vtiger_field SET presence=2,block=?,sequence=((SELECT s2 FROM (SELECT MAX(sequence) as s2 FROM vtiger_field WHERE block=?) AS s1)+1) WHERE fieldid=?',
                [$block->id, $block->id, $field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";