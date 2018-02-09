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



/* $Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php'); */


$contactsInstance = Vtiger_Module::getInstance('Contacts');
$contactsblock0 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $contactsInstance);
$contactsblock1 = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $contactsInstance);
$contactsblock2 = Vtiger_Block::getInstance('LBL_CUSTOMER_PORTAL_INFORMATION', $contactsInstance);
$contactsblock3 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $contactsInstance);
$contactsblock4 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $contactsInstance);
$contactsblock5 = Vtiger_Block::getInstance('LBL_IMAGE_INFORMATION', $contactsInstance);
$field0 = Vtiger_Field::getInstance('account_id', $contactsInstance);
$field4 = Vtiger_field::getInstance('lastname', $contactsInstance);
$field5 = Vtiger_field::getInstance('firstname', $contactsInstance);



$contactsblock6 = Vtiger_Module::getInstance('LBL_CONTACTS_VANLINES', $contactsInstance);
    if ($contactsblock6) {
        echo "<br> block 'LBL_CONTACTS_VANLINES' already exists.<br>";
    } else {
        $contactsblock6 = new Vtiger_Block();
        $contactsblock6->label = 'LBL_CONTACTS_VANLINES';
        $contactsInstance->addBlock($contactsblock6);
    }

$contactsblock7 = Vtiger_Module::getInstance('LBL_CONTACTS_ACCOUNTS', $contactsInstance);
    if ($contactsblock7) {
        echo "<br> block 'LBL_CONTACTS_ACCOUNTS' already exists.<br>";
    } else {
        $contactsblock7 = new Vtiger_Block();
        $contactsblock7->label = 'LBL_CONTACTS_ACCOUNTS';
        $contactsInstance->addBlock($contactsblock7);
    }

$contactsblock8 = Vtiger_Module::getInstance('LBL_CONTACTS_AGENTS', $contactsInstance);
    if ($contactsblock8) {
        echo "<br> block 'LBL_CONTACTS_AGENTS' already exists.<br>";
    } else {
        $contactsblock8 = new Vtiger_Block();
        $contactsblock8->label = 'LBL_CONTACTS_AGENTS';
        $contactsInstance->addBlock($contactsblock8);
    }

//add equipment fields fields
$field1 = Vtiger_Field::getInstance('contact_type', $contactsInstance);
    if ($field1) {
        echo "<br> Field 'contact_type' is already present. <br>";
        $db->pquery("UPDATE `vtiger_field` SET presence=2 WHERE fieldid=?", array($field1->id));
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
        $field1->setPicklistValues(array('Transferee', 'Account', 'Vanline', 'Agent'));
        

        $contactsblock0->addField($field1);
    }

$field2 = Vtiger_Field::getInstance('vanlines', $contactsInstance);
    if ($field2) {
        echo "<br> Field 'vanlines' is already present. <br>";
        $db->pquery("UPDATE `vtiger_field` SET presence=2 WHERE fieldid=?", array($field2->id));
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Van Line';
        $field2->name = 'vanlines';
        $field2->table = 'vtiger_contactdetails';
        $field2->column = 'vanlines';
        $field2->columntype = 'INT(19)';
        $field2->uitype = 10;
        $field2->typeofdata = 'V~O';

        $contactsblock6->addField($field2);
        $field2->setRelatedModules(array('Vanlines'));
    }

$field3 = Vtiger_Field::getInstance('agents', $contactsInstance);
    if ($field3) {
        echo "<br> Field 'agents' is already present. <br>";
        $db->pquery("UPDATE `vtiger_field` SET presence=2 WHERE fieldid=?", array($field3->id));
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'Agent';
        $field3->name = 'agents';
        $field3->table = 'vtiger_contactdetails';
        $field3->column = 'agents';
        $field3->columntype='INT(19)';
        $field3->uitype = 10;
        $field3->typeofdata = 'V~O';

        $contactsblock8->addField($field3);
        $field3->setRelatedModules(array('Agents'));
    }

//change these core fields to uitype 1 so that address autofill will work on them
$field6 = Vtiger_Field::getInstance('mailingstreet', $contactsInstance);
$field7 = Vtiger_Field::getInstance('otherstreet', $contactsInstance);
$query = "UPDATE `vtiger_field` SET uitype = 1 WHERE fieldid = ".$field6->id." OR fieldid = ".$field7->id;
Vtiger_Utils::ExecuteQuery($query);

    //START Add navigation link in module employees to accidents
    /*
    $contactsInstance = Vtiger_Module::getInstance('Contacts');
    $contactsInstance->setRelatedList(Vtiger_Module::getInstance('CubeSheets'), 'Surveys',Array('ADD'),'get_related_list');
    //END Add navigation link in module
    */

    
    //add filter in accidents module
    $filter1 = Vtiger_Filter::getInstance('Transferees', $contactsInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'Transferees';
        $filter1->isdefault = true;
        $contactsInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field4, 1)->addField($field5, 2)->addRule($field1, 'EQUALS', 'Transferee');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter1->id.", 'and' , '0' )");
    }

    $filter2 = Vtiger_Filter::getInstance('Van Lines', $contactsInstance);
    if ($filter2) {
        echo "<br> Filter exists <br>";
    } else {
        $filter2 = new Vtiger_Filter();
        $filter2->name = 'Van Lines';
        $filter2->isdefault = true;
        $contactsInstance->addFilter($filter2);

        $filter2->addField($field1)->addField($field4, 1)->addField($field5, 2)->addRule($field1, 'EQUALS', 'Vanline');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter2->id.", 'and' , '0' )");
    }

    $filter3 = Vtiger_Filter::getInstance('Agents', $contactsInstance);
    if ($filter3) {
        echo "<br> Filter exists <br>";
    } else {
        $filter3 = new Vtiger_Filter();
        $filter3->name = 'Agents';
        $filter3->isdefault = true;
        $contactsInstance->addFilter($filter3);

        $filter3->addField($field1)->addField($field4, 1)->addField($field5, 2)->addRule($field1, 'EQUALS', 'Agent');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter3->id.", 'and' , '0' )");
    }

    $filter4 = Vtiger_Filter::getInstance('Accounts', $contactsInstance);
    if ($filter4) {
        echo "<br> Filter exists <br>";
    } else {
        $filter4 = new Vtiger_Filter();
        $filter4->name = 'Accounts';
        $filter4->isdefault = true;
        $contactsInstance->addFilter($filter4);

        $filter4->addField($field1)->addField($field4, 1)->addField($field5, 2)->addRule($field1, 'EQUALS', 'Account');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter4->id.", 'and' , '0' )");
    }

    echo "<h2>Reordering Blocks</h2><br>";
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 1 WHERE blockid  = ' . $contactsblock0->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 5 WHERE blockid  = ' . $contactsblock1->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 6 WHERE blockid  = ' . $contactsblock2->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 7 WHERE blockid  = ' . $contactsblock3->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 8 WHERE blockid  = ' . $contactsblock4->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 9 WHERE blockid  = ' . $contactsblock5->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 2 WHERE blockid  = ' . $contactsblock7->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid  = ' . $contactsblock6->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid  = ' . $contactsblock8->id);

Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $contactsblock7->id . ' WHERE fieldid = ' . $field0->id);

    echo "<h2>Setting status = 0 in customview table</h2><br>";
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_customview` SET status = 0 WHERE entitytype  = "Contacts" AND (viewname = "Transferees" OR viewname = "Accounts" OR viewname = "Van Lines" OR viewname = "Agents")');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";