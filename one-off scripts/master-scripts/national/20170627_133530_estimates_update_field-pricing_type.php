<?php
/**
 * Created by PhpStorm.
 * Date: 6/27/2017
 * Time: 1:36 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');

$moduleInstance = Vtiger_Module::getInstance('Estimates');
if(!$moduleInstance)
{
    return;
}

$fieldName = 'pricing_type';
//@NOTE: Changing this to V~O because I forgot how many things make an estiamte without considering the required fields.
$typeofdata = 'V~O';

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    echo "The $fieldName field DOES NOT exists<br>\n";
    return;
}

if ($field->typeofdata == $typeofdata) {
    echo "The $fieldName is already: $typeofdata<br>\n";
    return;
}

$db = &PearDatabase::getInstance();
$stmt = 'update vtiger_field set typeofdata = ? where fieldid = ?';
$db->pquery($stmt, [$typeofdata, $field->id]);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";