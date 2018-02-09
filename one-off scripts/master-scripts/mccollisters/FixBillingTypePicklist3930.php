<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/24/2017
 * Time: 9:10 AM
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

$module = Vtiger_Module::getInstance('Opportunities');

if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('billing_type', $module);
if(!$field)
{
    return;
}

$db = &PearDatabase::getInstance();
$db->pquery('TRUNCATE TABLE vtiger_billing_type');

$field->setPicklistValues([
                              'COD',
                              'NAT',
                              'DP3',
                              'GSA',
                          ]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";