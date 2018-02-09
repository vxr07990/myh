<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$fieldName = 'move_type';
$newTypeOfData = 'V~O';

$moduleInstance = Vtiger_Module::getInstance('Contracts');
if (!$moduleInstance) {
    return;
}

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if(!$field) {
    return;
}

if ($field->typeofdata != $newTypeOfData) {
    print 'Updating typeofdata for ' . $fieldName . ' TO ' . $newTypeOfData . '.' . PHP_EOL;
    $db = &PearDatabase::getInstance();
    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
    $db->pquery($stmt, [$newTypeOfData, $field->id]);
}

print "\e[32mFINISHED: " . __FILE__ . "<br />\n\e[0m";
