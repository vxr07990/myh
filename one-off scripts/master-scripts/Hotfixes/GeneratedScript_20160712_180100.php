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



//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleAccounts = Vtiger_Module::getInstance('Accounts');

$blockAccounts9 = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleAccounts);
if ($blockAccounts9) {
    echo "<br> The LBL_ACCOUNT_INFORMATION block already exists in Accounts <br>";
} else {
    $blockAccounts9 = new Vtiger_Block();
    $blockAccounts9->label = 'LBL_ACCOUNT_INFORMATION';
    $moduleAccounts->addBlock($blockAccounts9);
}

$field = Vtiger_Field::getInstance('brand', $moduleAccounts);
if ($field) {
    echo "<br> The brand field already exists in Accounts <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ACCOUNTS_BRAND';
    $field->name = 'brand';
    $field->table = 'vtiger_account';
    $field->column ='brand';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAccounts9->addField($field);
    $field->setPicklistValues(['AVL', 'NAVL']);
}
/*Language Strings

    'LBL_ACCOUNTS_BRAND' => 'Brand',
*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";