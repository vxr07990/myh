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



$moduleInstance = Vtiger_Module::getInstance('Orders');
$moduleIsNew = false;

$block = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleInstance);
if ($block) {
    echo "<h3>The LBL_ORDERS_INFORMATION block already exists</h3><br> \n";
}

// Field Setup

$field1 = Vtiger_Field::getInstance('orders_projectname', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->name = 'orders_projectname';
    $field1->label = 'LBL_ORDERS_PROJECTNAME';
    $field1->uitype = 2;
    $field1->table = 'vtiger_orders';
    $field1->column = $field1->name;
    $field1->columntype = 'VARCHAR(255)';
    $field1->typeofdata = 'V~O';
    $block->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";