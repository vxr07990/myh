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
include_once('modules/ModTracker/ModTracker.php');

$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblockInstance1 = Vtiger_Module::getInstance('LBL_CUSTOM_INFORMATION', $contactsInstance);
    if ($contactsblockInstance1) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $contactsblockInstance1 = new Vtiger_Block();
        $contactsblockInstance1->label = 'LBL_CUSTOM_INFORMATION';
        $contactsInstance->addBlock($contactsblockInstance1);
    }
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblockInstance2 = Vtiger_Module::getInstance('LBL_CONTACTS_VANLINES', $contactsInstance);
    if ($contactsblockInstance2) {
        echo "<br> block 'LBL_CONTACTS_VANLINES' already exists.<br>";
    } else {
        $contactsblockInstance2 = new Vtiger_Block();
        $contactsblockInstance2->label = 'LBL_CONTACTS_VANLINES';
        $contactsInstance->addBlock($contactsblockInstance2);
    }
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblockInstance3 = Vtiger_Module::getInstance('LBL_CONTACTS_ACCOUNTS', $contactsInstance);
    if ($contactsblockInstance3) {
        echo "<br> block 'LBL_CONTACTS_ACCOUNTS' already exists.<br>";
    } else {
        $contactsblockInstance3 = new Vtiger_Block();
        $contactsblockInstance3->label = 'LBL_CONTACTS_ACCOUNTS';
        $contactsInstance->addBlock($contactsblockInstance3);
    }
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblockInstance4 = Vtiger_Module::getInstance('LBL_CONTACTS_AGENTS', $contactsInstance);
    if ($contactsblockInstance4) {
        echo "<br> block 'LBL_CONTACTS_AGENTS' already exists.<br>";
    } else {
        $contactsblockInstance4 = new Vtiger_Block();
        $contactsblockInstance4->label = 'LBL_CONTACTS_AGENTS';
        $contactsInstance->addBlock($contactsblockInstance4);
    }
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblockInstance5 = Vtiger_Module::getInstance('LBL_CONTACT_INFORMATION', $contactsInstance);

$firstnameInstance = Vtiger_Field::getInstance('firstname', $contactsInstance);
$lastnameInstance = Vtiger_Field::getInstance('lastname', $contactsInstance);

//add contacts fields fields

$field1 = Vtiger_Field::getInstance('leads_id', $contactsInstance);
    if ($field1) {
        echo "<br> Field 'leads_id' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_CONTACTS_LEADS';
        $field1->name = 'leads_id';
        $field1->table = 'vtiger_contactdetails';
        $field1->column = 'leads_id';
        $field1->columntype = 'INT(19)';
        $field1->uitype = 10;
        $field1->typeofdata = 'V~O';
    
        $contactsblockInstance5->addField($field1);
        $field1->setRelatedModules(array('Leads'));
    }

$field2 = Vtiger_Field::getInstance('agents_id', $contactsInstance);
    if ($field2) {
        echo "<br> Field 'agents_id' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_CONTACTS_AGENTS';
        $field2->name = 'agents_id';
        $field2->table = 'vtiger_contactdetails';
        $field2->column = 'agents_id';
        $field2->columntype = 'INT(19)';
        $field2->uitype = 10;
        $field2->typeofdata = 'V~O';
    
        $contactsblockInstance4->addField($field2);
        $field2->setRelatedModules(array('Agents'));
    }

$field3 = Vtiger_Field::getInstance('vanline_id', $contactsInstance);
    if ($field3) {
        echo "<br> Field 'vanline_id' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_CONTACT_VANLINE';
        $field3->name = 'vanline_id';
        $field3->table = 'vtiger_contactdetails';
        $field3->column = 'vanline_id';
        $field3->columntype = 'INT(19)';
        $field3->uitype = 10;
        $field3->typeofdata = 'V~O';
    
        $contactsblockInstance2->addField($field3);
        $field3->setRelatedModules(array('Vanlines'));
    }

$field4 = Vtiger_Field::getInstance('contact_account_id', $contactsInstance);
    if ($field4) {
        echo "<br> Field 'account_id' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_CONTACT_ACCOUNT';
        $field4->name = 'contact_account_id';
        $field4->table = 'vtiger_contactdetails';
        $field4->column = 'contact_account_id';
        $field4->columntype = 'INT(19)';
        $field4->uitype = 10;
        $field4->typeofdata = 'V~O';
    
        $contactsblockInstance3->addField($field4);
        $field4->setRelatedModules(array('Accounts'));
    }

$field5 = Vtiger_Field::getInstance('contact_type', $contactsInstance);
    if ($field5) {
        echo "<br> Field 'contact_type' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'Contact Type';
        $field5->name = 'contact_type';
        $field5->table = 'vtiger_contactdetails';
        $field5->column = 'contact_type';
        $field5->columntype = 'VARCHAR(100)';
        $field5->uitype = 16;
        $field5->typeofdata = 'V~M';
    
        $contactsblockInstance1->addField($field5);
        $field5->setPicklistValues(array('Transferee', 'Agent', 'Vanline', 'Account'));
    }

//START Add navigation link in module contacts to orders
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders', array('ADD'), 'get_dependents_list');
//END Add navigation link in module

//START Add navigation link in module contacts to cubesheets
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsInstance->setRelatedList(Vtiger_Module::getInstance('Cubesheets'), 'Surveys', array('ADD'), 'get_related_list');
//END Add navigation link in module

//START Add navigation link in module contacts to surveys
$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsInstance->setRelatedList(Vtiger_Module::getInstance('Surveys'), 'Survey Appointments', array('ADD'), 'get_related_list');
//END Add navigation link in module

    //add filter in contacts module
    $filter1 = Vtiger_Filter::getInstance('contacts_filter_transferees', $contactsInstance);
        if ($filter1) {
            echo "<br> Filter exists <br>";
        } else {
            $filter1 = new Vtiger_Filter();
            $filter1->name = 'contacts_filter_transferees';
            $filter1->isdefault = true;
            $contactsInstance->addFilter($filter1);

            $filter1->addField($field5)->addField($firstnameInstance, 1)->addField($lastnameInstance, 2)->addRule($field5, 'EQUALS', 'Transferee');
        }

    $filter2 = Vtiger_Filter::getInstance('contacts_filter_vanlines', $contactsInstance);
        if ($filter2) {
            echo "<br> Filter exists <br>";
        } else {
            $filter2 = new Vtiger_Filter();
            $filter2->name = 'contacts_filter_vanlines';
            $filter2->isdefault = true;
            $contactsInstance->addFilter($filter2);

            $filter2->addField($field5)->addField($firstnameInstance, 1)->addField($lastnameInstance, 2)->addRule($field5, 'EQUALS', 'Vanline');
        }

    $filter3 = Vtiger_Filter::getInstance('contacts_filter_agents', $contactsInstance);
        if ($filter3) {
            echo "<br> Filter exists <br>";
        } else {
            $filter3 = new Vtiger_Filter();
            $filter3->name = 'contacts_filter_agents';
            $filter3->isdefault = true;
            $contactsInstance->addFilter($filter3);

            $filter3->addField($field5)->addField($firstnameInstance, 1)->addField($lastnameInstance, 2)->addRule($field5, 'EQUALS', 'Agent');
        }

    $filter4 = Vtiger_Filter::getInstance('contacts_filter_accounts', $contactsInstance);
        if ($filter4) {
            echo "<br> Filter exists <br>";
        } else {
            $filter4 = new Vtiger_Filter();
            $filter4->name = 'contacts_filter_accounts';
            $filter4->isdefault = true;
            $contactsInstance->addFilter($filter4);

            $filter4->addField($field5)->addField($firstnameInstance, 1)->addField($lastnameInstance, 2)->addRule($field5, 'EQUALS', 'Account');
        }
