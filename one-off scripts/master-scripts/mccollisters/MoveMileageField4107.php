<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/3/2017
 * Time: 10:12 AM
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

$moduleNames = ['Estimates', 'Actuals'];
$db = &PearDatabase::getInstance();
foreach($moduleNames as $moduleName)
{
    $module = Vtiger_Module::getInstance($moduleName);
    if(!$module)
    {
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
    if(!$block)
    {
        continue;
    }
    $field = Vtiger_Field::getInstance('interstate_mileage', $module);
    if(!$field)
    {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET block=?,sequence=? WHERE fieldid=?',
                [$block->id, 30, $field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";