<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$contactsModule = Vtiger_Module::getInstance('Contacts');

$contactsBlock = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $contactsModule);

$custnumField = Vtiger_Field::getInstance('customer_number', $contactsModule);
if ($custnumField) {
    echo "The customer_number field already exists<br>\n";
} else {
    $custnumField             = new Vtiger_Field();
    $custnumField->label      = 'LBL_CUSTOMER_NUMBER';
    $custnumField->name       = 'customer_number';
    $custnumField->table      = 'vtiger_contactdetails';
    $custnumField->column     = 'customer_number';
    $custnumField->columntype = 'VARCHAR(100)';
    $custnumField->uitype     = 1;
    $custnumField->typeofdata = 'V~O';
    $contactsBlock->addField($custnumField);
}

$updateString = 'UPDATE `vtiger_field` SET block='.$contactsBlock->id.' WHERE fieldid=';
$fieldList = ['account_id', 'vanlines', 'agents'];

foreach($fieldList as $fieldName) {
    $fieldToUpdate = Vtiger_Field::getInstance($fieldName, $contactsModule);
    if ($fieldToUpdate) {
        //Update block for field
        Vtiger_Utils::ExecuteQuery($updateString.$fieldToUpdate->id);
    }
}

