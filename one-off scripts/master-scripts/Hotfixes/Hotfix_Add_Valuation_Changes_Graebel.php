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

$moduleOrders = Vtiger_Module::getInstance('Orders');

$blockOrders309 = Vtiger_Block::getInstance('LBL_ORDERS_BLOCK_VALUATION', $moduleOrders);
if ($blockOrders309) {
    echo "<br> The LBL_ORDERS_BLOCK_VALUATION block already exists in Orders <br>";
} else {
    $blockOrders309 = new Vtiger_Block();
    $blockOrders309->label = 'LBL_ORDERS_BLOCK_VALUATION';
    $moduleOrders->addBlock($blockOrders309);
}

$field = Vtiger_Field::getInstance('valuation_deductible', $moduleOrders);
if ($field) {
    echo "<br> The valuation_deductible field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_VALUATIONDEDUCTIBLE';
    $field->name = 'valuation_deductible';
    $field->table = 'vtiger_orders';
    $field->column ='valuation_deductible';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders309->addField($field);
    $field->setPicklistValues(['60¢ /lb.', 'FVP - $0', 'FVP - $250', 'FVP - $500']);
}
$field = Vtiger_Field::getInstance('valuation_amount', $moduleOrders);
if ($field) {
    echo "<br> The valuation_amount field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_VALUATIONAMOUNT';
    $field->name = 'valuation_amount';
    $field->table = 'vtiger_orders';
    $field->column ='valuation_amount';
    $field->columntype = 'decimal(22,8)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders309->addField($field);
}
$field = Vtiger_Field::getInstance('additional_valuation', $moduleOrders);
if ($field) {
    echo "<br> The additional_valuation field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_ADDITIONALVALUATION';
    $field->name = 'additional_valuation';
    $field->table = 'vtiger_orders';
    $field->column ='additional_valuation';
    $field->columntype = 'decimal(22,8)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders309->addField($field);
}

$blockOrders310 = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleOrders);

$field = Vtiger_Field::getInstance('contract', $moduleOrders);
if ($field) {
    echo "<br> The contract field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_CONTRACT';
    $field->name = 'contract';
    $field->table = 'vtiger_orders';
    $field->column = 'contract';
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';

    $blockOrders310->addField($field);

    $field->setRelatedModules(array('Contracts'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";