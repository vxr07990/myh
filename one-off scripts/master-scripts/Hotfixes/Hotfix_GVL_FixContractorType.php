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

$vendorsInstance = Vtiger_Module::getInstance('Vendors');
$field_b = Vtiger_Field::getInstance('vendors_contractor_type', $vendorsInstance);
if ($field_b) {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype='16' WHERE fieldname='vendors_contractor_type'");
    Vtiger_Utils::ExecuteQuery("DROP TABLE `vtiger_vendors_contractor_type`");
    Vtiger_Utils::ExecuteQuery("DROP TABLE `vtiger_vendors_contractor_type_seq`");
    Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_picklist WHERE name='vendors_contractor_type'");

    $field_b->setPicklistValues(array('IC', 'TSC', 'IT'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";