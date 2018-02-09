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

echo "<br> Attempting to add DUNS Number to accounts<br>";

$accountModule = Vtiger_Module::getInstance('Accounts');

$accountInfoBlock = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $accountModule);

$dunsNumber = Vtiger_Field::getInstance('duns_number', $accountModule);
if ($dunsNumber) {
    echo "<br> The duns number field already exists in Accounts <br>";
} else {
    echo "done <br> Creating duns number field now...";
    $dunsNumber = new Vtiger_Field();
    $dunsNumber->label = 'LBL_ACCOUNTS_DUNSNUMBER';
    $dunsNumber->name = 'duns_number';
    $dunsNumber->table = 'vtiger_account';
    $dunsNumber->column = 'duns_number';
    $dunsNumber->columntype = 'VARCHAR(40)';
    $dunsNumber->uitype = 1;
    $dunsNumber->typeofdata = 'V~O';
    $dunsNumber->displaytype = 1;
    $dunsNumber->quickcreate = 0;
    $dunsNumber->presence = 2;

    $accountInfoBlock->addField($dunsNumber);
    $dunsNumber->setPicklistValues(['National Accounts', 'Military', 'GSA', 'Consumer/COD', 'RMC', 'One Time National Account']);
    echo "done<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";