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

// OT5256 - Leads Source Default

$module = Vtiger_Module::getInstance('Leads');
if(!$module){
    echo 'Module '.$module->name.' not present.';
    return;
}
$fieldname = 'leadsource';
$field = Vtiger_Field_Model::getInstance($fieldname, $module);
if(!$field){
    echo 'Field '.$field->name.' not present.';
    return;
}
$db = PearDatabase::getInstance();
//delete all options other than Website
$db->pquery("DELETE FROM vtiger_$fieldname WHERE ".$fieldname."id NOT IN (SELECT valueid FROM vtiger_custompicklist WHERE fieldid='$fieldname')");
$field->setNoRolePicklistValues(array('Website'));

//remove options from vtiger_custompicklist that where removed from vtiger_leadsource
$db->pquery("DELETE FROM vtiger_custompicklist WHERE fieldid = '$fieldname' AND valueid NOT IN (SELECT ".$fieldname."id FROM vtiger_$fieldname)");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";