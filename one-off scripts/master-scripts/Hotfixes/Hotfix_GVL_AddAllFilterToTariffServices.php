<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/28/2017
 * Time: 4:22 PM
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

$module = Vtiger_Module::getInstance('TariffServices');

if(!$module)
{
    return;
}

$filter = Vtiger_Filter::getInstance('All', $module);
if(!$filter)
{
    $filter = new Vtiger_Filter();
    $filter->name = 'All';
    $filter->isdefault = true;
    $module->addFilter($filter);

    $i = 0;
    foreach(['service_name','tariff_section','rate_type'] as $fieldName)
    {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if(!$field)
        {
            continue;
        }
        $filter->addField($field, $i);
        $i++;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";