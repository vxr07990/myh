<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/23/2017
 * Time: 10:34 AM
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
require_once('includes/main/WebUI.php');
require_once('modules/Inventory/models/Record.php');
require_once('modules/Quotes/models/Record.php');
require_once('modules/Estimates/models/Record.php');

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('OrdersTask');

if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('carton_name', $module);

if(!$field)
{
    return;
}

$db->pquery('UPDATE vtiger_field SET uitype=? WHERE fieldid=?',
            [16, $field->id]);

$values = array_values(Estimates_Record_Model::getPackingLabelsStatic());

$field->setPicklistValues($values);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";