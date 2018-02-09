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

$module = Vtiger_Module::getInstance('Users');

$block1 = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $module);

//-------------------------------------------------------------

$field8 = new Vtiger_Field();
$field8->label = 'Agent';
$field8->name = 'agent_ids';
$field8->table = 'vtiger_users';
$field8->column = 'agent_ids';
$field8->columntype = 'varchar(10)';
$field8->uitype = 33;
$field8->typeofdata = 'V~O';

$block1->addField($field8);

$field8->setPicklistValues(array(123, 456));

//--------------------------------------------------------------

$block1->save($module);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";