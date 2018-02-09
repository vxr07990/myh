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



if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line` WHERE `business_line` = 'Auto Transportation'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line_est` WHERE `business_line_est` = 'Auto Transportation'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line` WHERE `business_line` = 'International Move'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line_est` WHERE `business_line_est` = 'International Move'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line` WHERE `business_line` = 'Commercial Move'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_business_line_est` WHERE `business_line_est` = 'Commercial Move'");

$gvlLines = ['Work Space - MAC', 'Work Space - Special Services', 'Work Space - Commodities'];

//Add in the new lines if they don't exist
foreach ($gvlLines as $line) {
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_business_line` (`business_line`, `sortorderid`, `presence`) SELECT * FROM (SELECT '$line', (SELECT COUNT(*) + 1 FROM `vtiger_business_line`), 1) as tmp WHERE NOT EXISTS(SELECT * FROM `vtiger_business_line` WHERE `business_line` = '$line')");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_business_line_est` (`business_line_est`, `sortorderid`, `presence`) SELECT * FROM (SELECT '$line', (SELECT COUNT(*) + 1 FROM `vtiger_business_line_est`), 1) as tmp WHERE NOT EXISTS(SELECT * FROM `vtiger_business_line_est` WHERE `business_line_est` = '$line')");
}

$moduleAccounts = Vtiger_Module::getInstance('Accounts');

$blockAccounts9 = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleAccounts);
if ($blockAccounts9) {
    echo "<br> The LBL_ACCOUNT_INFORMATION block already exists in Accounts <br>";
} else {
    $blockAccounts9 = new Vtiger_Block();
    $blockAccounts9->label = 'LBL_ACCOUNT_INFORMATION';
    $moduleAccounts->addBlock($blockAccounts9);
}

$field = Vtiger_Field::getInstance('business_line', $moduleAccounts);
if ($field) {
    echo "<br> The business_line field already exists in Accounts <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ACCOUNTS_BUSINESSLINE';
    $field->name = 'business_line';
    $field->table = 'vtiger_account';
    $field->column ='business_line';
    $field->columntype = 'text';
    $field->uitype = 33;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAccounts9->addField($field);
    $result = $db->pquery('SELECT business_line FROM vtiger_business_line', []);

    $field->setPicklistvalues(array_column($result->GetAll(), 'business_line'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";