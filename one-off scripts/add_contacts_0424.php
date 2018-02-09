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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Contacts');

$block1 = new Vtiger_Block();
$block1->label = 'LBL_CONTACTS_ORDERS';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTACT_ORDER';
$field1->name = 'orders';
$field1->table = 'vtiger_contactdetails';
$field1->column = 'orders';
$field1->columntype = 'INT(19)';
$field1->uitype = 10;
$field1->typeofdata = 'V~O';


$block1->addField($field1);
$field1->setRelatedModules(array('Orders'));

$block1->save($module);

$block2 = new Vtiger_Block();
$block2->label = 'LBL_CONTACTS_VANLINES';
$module->addBlock($block2);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTACT_VANLINE';
$field1->name = 'vanlines';
$field1->table = 'vtiger_contactdetails';
$field1->column = 'vanlines';
$field1->columntype = 'INT(19)';
$field1->uitype = 10;
$field1->typeofdata = 'V~O';


$block2->addField($field1);
$field1->setRelatedModules(array('Vanlines'));

$block2->save($module);

$block3 = new Vtiger_Block();
$block3->label = 'LBL_CONTACTS_ACCOUNTS';
$module->addBlock($block3);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTACT_ACCOUNT';
$field1->name = 'accounts';
$field1->table = 'vtiger_contactdetails';
$field1->column = 'accounts';
$field1->columntype = 'INT(19)';
$field1->uitype = 10;
$field1->typeofdata = 'V~O';


$block3->addField($field1);
$field1->setRelatedModules(array('Accounts'));

$block3->save($module);

$block4 = new Vtiger_Block();
$block4 = $block4->getInstance('LBL_CONTACT_INFORMATION', $module);

$field1 = new Vtiger_Field();
$field1->label = 'Contact Type';
$field1->name = 'contact_type';
$field1->table = 'vtiger_contactdetails';
$field1->column = 'contact_type';
$field1->columntype = 'VARCHAR(225)';
$field1->uitype = 16;
$field1->typeofdata = 'V~O';

$block4->addField($field1);
$field1->setPicklistValues(array('Account', 'Order', 'Vanline', 'Agent'));

$block4->save($module);

$block5 = new Vtiger_Block();
$block5->label = 'LBL_CONTACTS_AGENTS';
$module->addBlock($block5);


$block5->save($module);
