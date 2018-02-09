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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/21/2016
 * Time: 10:52 AM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

// make first name required
$moduleContacts = Vtiger_Module::getInstance('Contacts');
if ($moduleContacts) {
    $field = Vtiger_Field::getInstance('firstname', $moduleContacts);
    if ($field) {
        $db->pquery('UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?', ['V~M', $field->id]);
    }
}

// add contact relation to Accounts
$moduleAccounts = Vtiger_Module::getInstance('Accounts');
if ($moduleAccounts) {
    $block = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleAccounts);
    if ($block) {
        $field = Vtiger_Field::getInstance('transferee_contact', $moduleAccounts);
        if (!$field) {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_ACCOUNT_TRANSFEREE_CONTACT';
            $field->name       = 'transferee_contact';
            $field->table      = 'vtiger_account';
            $field->column     = 'transferee_contact';
            $field->columntype = 'VARCHAR(100)';
            $field->uitype     = 10;
            $field->typeofdata = 'V~M';
            $block->addField($field);
            $field->SetRelatedModules(['Contacts']);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";