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

include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

echo '<br />Checking if modified time field exists:<br />';

$module = Vtiger_Module::getInstance('Users');

$block = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $module);

$field1 = Vtiger_Field::getInstance('mcid', $moduleQuotes);
if ($field1) {
    echo "<br /> The mcid field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_USERS_MCID';
    $field1->name = 'mcid';
    $field1->table = 'vtiger_users';
    $field1->column = 'mcid';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';

    $block->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";