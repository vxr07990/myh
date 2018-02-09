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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br> Attempting to add Billing Type picklist to accounts<br>";

$accountModule = Vtiger_Module::getInstance('Accounts');

$accountInfoBlock = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $accountModule);

$billingType = Vtiger_Field::getInstance('billing_type', $accountModule);
if ($billingType) {
    echo "<br> The billing type field already exists in Accounts <br>";
} else {
    echo "<br> blowing out old picklist values...";
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_billing_type`");
    echo "done <br> Creating billing type field now...";
    $billingType = new Vtiger_Field();
    $billingType->label = 'LBL_ACCOUNTS_BILLINGTYPE';
    $billingType->name = 'billing_type';
    $billingType->table = 'vtiger_account';
    $billingType->column = 'billing_type';
    $billingType->columntype = 'VARCHAR(30)';
    $billingType->uitype = 16;
    $billingType->typeofdata = 'V~O';
    $billingType->displaytype = 1;
    $billingType->quickcreate = 0;
    $billingType->presence = 2;

    $accountInfoBlock->addField($billingType);
    $billingType->setPicklistValues(['National Accounts', 'Military', 'GSA', 'Consumer/COD', 'RMC', 'One Time National Account']);
    echo "done<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";