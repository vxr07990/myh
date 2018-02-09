<?php
/**
 * FAKE NEWS
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/23/2017
 * Time: 3:32 PM
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

$moduleInstance = Vtiger_Module::getInstance('ItemCodes');
$fieldInstance = Vtiger_Field::getInstance('itemcodes_tariffservicecode', $moduleInstance);
if($fieldInstance) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype=16 WHERE fieldid=".$fieldInstance->id);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
