<?php
/**
 * Created by PhpStorm.
 * User: jgriffin
 * Date: 4/27/2017
 * Time: 8:56 AM
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

$newTypeOfData = 'V~M';
$fieldName = 'contact_id';

$db = &PearDatabase::getInstance();
foreach (['Surveys'] as $moduleName) {
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if ($moduleInstance) {
        if ($field3 = Vtiger_Field::getInstance($fieldName, $moduleInstance)) {
            if ($field3->typeofdata != $newTypeOfData) {
                $db = &PearDatabase::getInstance();
                $db->pquery("UPDATE `vtiger_field` SET `typeofdata`=? WHERE `fieldid`=?", [$newTypeOfData, $field3->id]);
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
