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



$ordModule = Vtiger_Module::getInstance('Orders');

$ordBlock = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordModule);

if (!isset($db)) {
    $db = PearDatabase::getInstance();
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
}

$field4 = Vtiger_Field::getInstance('orders_miles', $ordModule);
if ($field4) {
    echo "Field orders_miles exists in Orders module - disabling<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field4->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
