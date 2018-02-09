<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/18/2017
 * Time: 9:03 AM
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

$module = Vtiger_Module::getInstance('TariffManager');
if(!$module)
{
    return;
}
$field = Vtiger_Field::getInstance('custom_tariff_type', $module);

if(!$field)
{
    return;
}

$res = $db->pquery('SELECT * FROM vtiger_custom_tariff_type WHERE custom_tariff_type=?',
                   ['400DOE']);

if($res->numRows() == 0)
{
    $field->setPicklistValues(['400DOE']);
    $db->pquery('UPDATE vtiger_custom_tariff_type SET sortorderid=((SELECT v FROM (SELECT MAX(sortorderid) AS v FROM vtiger_custom_tariff_type) AS sv) + 1) WHERE custom_tariff_type=?',
                ['400DOE']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";