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

$contactsModule = Vtiger_Module::getInstance('Contacts');

$fieldListToMakeMandatory = ['firstname','lastname','mobile','email'];

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "UPDATE `vtiger_field` SET typeofdata=? WHERE fieldid=?";

foreach($fieldListToMakeMandatory as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $contactsModule);
    $typeofdata = $field->typeofdata;
    $pieces = explode('~', $typeofdata);
    $pieces[1] = 'M';
    $typeofdata = implode('~', $pieces);
    $db->pquery($sql, [$typeofdata, $field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";