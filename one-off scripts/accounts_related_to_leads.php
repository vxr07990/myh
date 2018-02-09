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
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


// START Dependency field
$module = Vtiger_Module::getInstance('Accounts');
$block1 = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $module);

$field1 = new Vtiger_Field();
$field1->label = 'Leads';
$field1->name = 'leads_id';
$field1->table = 'vtiger_account';
$field1->column = 'leads_id';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 10;
$field1->typeofdata = 'V~O';

$block1->addField($field1);
$field1->setRelatedModules(array('Leads'));

$block1->save($module);
// END Dependency field

//START Add navigation link in module
$module = Vtiger_Module::getInstance('Accounts');
$module->setRelatedList(Vtiger_Module::getInstance('Leads'), 'Leads', array('ADD', 'SELECT'), 'get_related_list');
//END Add navigation link in module
;
