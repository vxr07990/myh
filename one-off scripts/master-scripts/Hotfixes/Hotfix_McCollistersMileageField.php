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



$potModule = Vtiger_Module::getInstance('Potentials');
$oppModule = Vtiger_Module::getInstance('Opportunities');
$ordModule = Vtiger_Module::getInstance('Orders');

$potBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potModule);
$oppBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppModule);
$ordBlock = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordModule);

if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

$field1 = Vtiger_Field::getInstance('mileage', $potModule);
if ($field1) {
    echo "Field mileage already exists in Potentials module<br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_POTENTIALS_MILEAGE';
    $field1->name = 'mileage';
    $field1->table = 'vtiger_potential';
    $field1->column = 'mileage';
    $field1->columntype = 'INT(11)';
    $field1->uitype = 7;
    $field1->typeofdata = 'I~O';
    
    $potBlock->addField($field1);
}

$field2 = Vtiger_Field::getInstance('mileage', $oppModule);
if ($field2) {
    echo "Field mileage already exists in Opportunities module<br />";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_POTENTIALS_MILEAGE';
    $field2->name = 'mileage';
    $field2->table = 'vtiger_potential';
    $field2->column = 'mileage';
    $field2->columntype = 'INT(11)';
    $field2->uitype = 7;
    $field2->typeofdata = 'I~O';
    
    $oppBlock->addField($field2);
}

$field3 = Vtiger_Field::getInstance('mileage', $ordModule);
if ($field3) {
    echo "Field mileage already exists in Orders module<br />";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ORDERS_MILEAGE';
    $field3->name = 'mileage';
    $field3->table = 'vtiger_orders';
    $field3->column = 'mileage';
    $field3->columntype = 'INT(11)';
    $field3->uitype = 7;
    $field3->typeofdata = 'I~O';
    
    $ordBlock->addField($field3);
    
    $currentid = $db->getUniqueID('vtiger_modentity_num');
    
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_modentity_num` VALUES (".$currentid.", 'Orders', 'ORDER', 1, 1, 1)");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_cvcolumnlist` WHERE cvid=84");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvcolumnlist` VALUES (84, 0, 'vtiger_orders:orders_contacts:orders_contacts:Orders_LBL_ORDERS_CONTACTS:V')");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvcolumnlist` VALUES (84, 1, 'vtiger_orders:orders_orders_no:orders_no:Orders_Orders_No:V')");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvcolumnlist` VALUES (84, 2, 'vtiger_orders:orders_account:orders_account:Orders_LBL_ORDERS_ACCOUNT:V')");
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvcolumnlist` VALUES (84, 3, 'vtiger_orders:orders_accounttype:orders_accounttype:Orders_LBL_ORDERS_ACCOUNTTYPE:V')");
}

$field4 = Vtiger_Field::getInstance('orders_miles', $ordModule);
if ($field4) {
    echo "Field orders_miles exists in Orders module - disabling<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field4->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";