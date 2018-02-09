<?php
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
include_once('vtlib/Vtiger/Block.php');

// Survey Module
$module = Vtiger_Module::getInstance('Cubesheets');
// Survey block
$block = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $module);
if(!$block) {
    echo "Surveys base block does not exist, cannot add field. Frankly, I don't know how this happened.<br/>\n";
    return;
}

$field = 'effective_tariff';
$datatype = 'I~O';

$fieldInstance = VTiger_Field::getInstance($field, $module);

if ($fieldInstance) {
    print "no Field: $field\n";
    if ($fieldInstance->typeofdata != $datatype) {
        $db  = &PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET `typeofdata`=? where `fieldid`=? LIMIT 1';
        $db->pquery($sql, [$datatype, $fieldInstance->id]);
    }
}

$field = 'effective_date';
$datatype = 'D~O';

$fieldInstance = VTiger_Field::getInstance($field, $module);

if ($fieldInstance) {
    print "no Field: $field\n";
    if ($fieldInstance->typeofdata != $datatype) {
        $db  = &PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET `typeofdata`=? where `fieldid`=? LIMIT 1';
        $db->pquery($sql, [$datatype, $fieldInstance->id]);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
