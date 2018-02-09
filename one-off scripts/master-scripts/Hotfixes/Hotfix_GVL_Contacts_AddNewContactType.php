<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 11/22/2016
 * Time: 1:02 PM
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
$db = PearDatabase::getInstance();
$contactsInstance = Vtiger_Module::getInstance('Contacts');

if (!$contactsInstance) {
    echo 'Contacts module not found. Exiting.';
    return;
}

$contactsblock0 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $contactsInstance);

if (!$contactsblock0) {
    echo 'Block not found. Exiting.';
}

$field1 = Vtiger_Field::getInstance('contact_type', $contactsInstance);
if ($field1) {
    echo "<br> Field 'contact_type' is already present. Updating picklist values.<br>";
    $db->pquery("UPDATE `vtiger_field` SET presence=2 WHERE fieldid=?", array($field1->id));
    $db->pquery("TRUNCATE TABLE `vtiger_contact_type`");
    $field1->setPicklistValues(array('Transferee', 'Account', 'Vanline', 'Agent', 'Claimant'));
    echo "$field1->name picklist updated.<br/>\n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Contact Type';
    $field1->name = 'contact_type';
    $field1->table = 'vtiger_contactdetails';
    $field1->column = 'contact_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;
    $field1->setPicklistValues(array('Transferee', 'Account', 'Vanline', 'Agent', 'Claimant'));

    $contactsblock0->addField($field1);
    echo "$field1->name field added. <br/>\n";
}
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
