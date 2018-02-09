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

echo "<br>Begin hotfix Graebel add Orders Accounts fields<br>";

$ordersModule = Vtiger_Module::getInstance('Orders');
$orderDetailsBlock = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersModule);

echo "<br>Attempting to add orders account contract field.<br>";

//Add our Contracts UI type 10 to orders
$accountContractField = Vtiger_Field::getInstance('account_contract', $module);
if ($accountContractField) {
    echo "<br>The account contract field already exists<br>";
} else {
    echo "<br>The account contract field was not found, creating now...";
    $accountContractField = new Vtiger_Field();
    $accountContractField->label = 'LBL_ORDERS_ACCOUNTCONTRACT';
    $accountContractField->name = 'account_contract';
    $accountContractField->table = 'vtiger_orders';
    $accountContractField->column = 'account_contract';
    $accountContractField->columntype = 'INT(19)';
    $accountContractField->uitype = 10;
    $accountContractField->typeofdata = 'V~O';
    $accountContractField->displaytype = 1;

    $orderDetailsBlock->addField($accountContractField);
    $accountContractField->setRelatedModules(array('Contracts'));
    echo "done!<br>";
}

//Conrado is going to take this over since he is working on move policies currently
/*/Add our MovePolicies UI type 10 to orders
$accountMovePolicyField = Vtiger_Field::getInstance('account_move_policy',$module);
if($accountMovePolicyField) {
    echo "<br>The account move policy field already exists<br>";
}
else {
    $accountMovePolicyField = new Vtiger_Field();
    $accountMovePolicyField->label = 'LBL_ORDERS_ACCOUNTMOVEPOLICY';
    $accountMovePolicyField->name = 'account_move_policy';
    $accountMovePolicyField->table = 'vtiger_orders';
    $accountMovePolicyField->column = 'account_move_policy';
    $accountMovePolicyField->columntype = 'INT(19)';
    $accountMovePolicyField->uitype = 10;
    $accountMovePolicyField->typeofdata = 'V~O';
    $accountMovePolicyField->displaytype = 1;

    $orderDetailsBlock->addField($accountMovePolicyField);
    $accountMovePolicyField->setRelatedModules(array('MovePolicies'));
}*/

echo "<br>Attempting to remove account type from orders. (replaced with billing type)...";
//remove orders account-type field (this field is replaced by billing type)
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE columnname='orders_accounttype' AND fieldname='orders_accounttype' AND tabid=70");
echo "done.<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";