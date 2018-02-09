<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/9/2017
 * Time: 5:06 PM
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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Orders');

if($module)
{
    $field = Vtiger_Field::getInstance('payment_terms', $module);
    if($field) {
        $db->pquery('UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?', ['I~O', $field->id]);
        $db->pquery('ALTER TABLE `'.$field->table.'` MODIFY COLUMN `'.$field->column.'` INT(10) DEFAULT NULL');
    }
}

$module = Vtiger_Module::getInstance('Accounts');
if($module)
{
    $db->pquery('ALTER TABLE vtiger_account_invoicesettings MODIFY COLUMN payment_terms INT(10) DEFAULT NULL');
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";