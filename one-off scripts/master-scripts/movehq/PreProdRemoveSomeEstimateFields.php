<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/16/2017
 * Time: 2:13 PM
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

$db = &PearDatabase::getInstance();

$moduleNames = ['Estimates', 'Actuals'];
$fieldNames = ['bulky_article_changes', 'hours_per_van', 'hours_first_man', 'additional_men' , 'hours_per_additional_man', 'rush_shipment_fee'];

foreach($moduleNames as $moduleName)
{
    $module = Vtiger_Module::getInstance($moduleName);
    if(!$module)
    {
        continue;
    }

    foreach($fieldNames as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if(!$field)
        {
            continue;
        }
        $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                    [1, $field->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";