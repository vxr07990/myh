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
//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Users');

$block1 = new Vtiger_Block();
$block1->label = 'LBL_SMTP_USER_INFORMATION';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Server Name';
$field1->name = 'user_smtp_server';
$field1->table = 'vtiger_users';
$field1->column = 'user_server';
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'User Name';
$field1->name = 'user_smtp_username';
$field1->table = 'vtiger_users';
$field1->column = 'user_smtp_username';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Password';
$field1->name = 'user_smtp_password';
$field1->table = 'vtiger_users';
$field1->column = 'user_smtp_password';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'From E-Mail';
$field1->name = 'user_smtp_fromemail';
$field1->table = 'vtiger_users';
$field1->column = 'user_smtp_fromemail';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Requires Authentication';
$field1->name = 'user_smtp_authentication';
$field1->table = 'vtiger_users';
$field1->column = 'user_smtp_authentication';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);



$block1->save($module);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";