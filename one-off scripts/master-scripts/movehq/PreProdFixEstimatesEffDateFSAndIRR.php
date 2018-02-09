<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/16/2017
 * Time: 8:52 AM
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

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Estimates');
if(!$module)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
if(!$block)
{
    return;
}

$field1727 = Vtiger_Field::getInstance('interstate_effective_date', $module);
if ($field1727) {
    echo "<br> Field 'interstate_effective_date' is already present <br>";
} else {
    $field1727 = new Vtiger_Field();
    $field1727->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field1727->name = 'interstate_effective_date';
    $field1727->table = 'vtiger_quotes';
    $field1727->column = 'interstate_effective_date';
    $field1727->columntype = 'date';
    $field1727->uitype = 5;
    $field1727->typeofdata = 'D~O';
    $field1727->displaytype = 1;
    $field1727->presence = 2;
    $field1727->defaultvalue = '';
    $field1727->quickcreate = 0;
    $field1727->summaryfield = 0;

    $block->addField($field1727);
}

$db = &PearDatabase::getInstance();
$modules = ['Estimates', 'Actuals'];
foreach ($modules as $moduleName)
{
    $module = Vtiger_Module::getInstance($moduleName);
    if(!$module)
    {
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
    if(!$block)
    {
        continue;
    }
    $field = Vtiger_Field::getInstance('accesorial_fuel_surcharge', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET block=? WHERE fieldid=?',
                    [$block->id, $field->id]);
    }
    $field = Vtiger_Field::getInstance('irr_charge', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET block=? WHERE fieldid=?',
                    [$block->id, $field->id]);
    }
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";