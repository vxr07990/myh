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


//OT 1599 - Adding Valuation Block to Orders based on block in Estimates

$moduleOrders = Vtiger_Module::getInstance('Orders');

$blockOrdersVal = Vtiger_Block::getInstance('LBL_ORDERS_BLOCK_VALUATION', $moduleOrders);
if ($blockOrdersVal) {
    echo "<br> The LBL_ORDERS_BLOCK_VALUATION block already exists in Orders <br>";
} else {
    $blockOrdersVal = new Vtiger_Block();
    $blockOrdersVal->label = 'LBL_ORDERS_BLOCK_VALUATION';
    $moduleOrders->addBlock($blockOrdersVal);
}

$field0 = Vtiger_Field::getInstance('valuation_deductible', $moduleOrders);
if ($field0) {
    echo "<br> The valuation_deductible field already exists in Orders <br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_ORDERS_VALUATIONDEDUCTIBLE';
    $field0->name = 'valuation_deductible';
    $field0->table = 'vtiger_orders';
    $field0->column ='valuation_deductible';
    $field0->columntype = 'varchar(250)';
    $field0->uitype = 16;
    $field0->typeofdata = 'V~O';
    $field0->displaytype = 1;
    $field0->quickcreate = 1;
    $field0->summaryfield = 0;
    $blockOrdersVal->addField($field0);
    echo "<br>valuation_deductible field added to Orders<br>";
}
$field1 = Vtiger_Field::getInstance('valuation_amount', $moduleOrders);
if ($field1) {
    echo "<br> The valuation_amount field already exists in Orders <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ORDERS_VALUATIONAMOUNT';
    $field1->name = 'valuation_amount';
    $field1->table = 'vtiger_orders';
    $field1->column ='valuation_amount';
    $field1->columntype = 'decimal(56,2)';
    $field1->uitype = 71;
    $field1->typeofdata = 'N~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 1;
    $field1->summaryfield = 0;
    $blockOrdersVal->addField($field1);
    echo "<br>valuation_amount field added to Orders<br>";
}

$field2 = Vtiger_Field::getInstance('valuation_discounted', $moduleInstance);
if (!$field2) {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_ORDERS_VALUATIONDISCOUNTED';
    $field2->name       = 'valuation_discounted';
    $field2->table      = 'vtiger_orders';
    $field2->column     = 'valuation_discounted';
    $field2->columntype = 'VARCHAR(3)';
    $field2->uitype     = 56;
    $field2->typeofdata = 'V~O';
    $blockOrdersVal->addField($field2);
    echo "<br>valuation_discounted field added to Orders<br>";
} else {
    echo "<br>valuation_discounted already exists<br>";
}
$field3 = Vtiger_Field::getInstance('valuation_discount_amount', $moduleInstance);
if (!$field3) {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_ORDERS_VALUATIONDISCOUNTAMOUNT';
    $field3->name       = 'valuation_discount_amount';
    $field3->table      = 'vtiger_orders';
    $field3->column     = 'valuation_discount_amount';
    $field3->columntype = 'DECIMAL(10,2)';
    $field3->uitype     = 71;
    $field3->typeofdata = 'V~O';
    $blockOrdersVal->addField($field3);
    echo "<br>valuation_discount_amount field added to Orders<br>";
} else {
    echo "<br>valuation_discount_amount already exists<br>";
}


$field4 = Vtiger_Field::getInstance('additional_valuation', $moduleOrders);
if ($field4) {
    echo "<br> The additional_valuation field already exists in Orders <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ORDERS_ADDITIONALVALUATION';
    $field4->name = 'additional_valuation';
    $field4->table = 'vtiger_orders';
    $field4->column ='additional_valuation';
    $field4->columntype = 'decimal(22,2)';
    $field4->uitype = 71;
    $field4->typeofdata = 'N~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 1;
    $field4->summaryfield = 0;
    $blockOrdersVal->addField($field4);
    echo "<br>additional_valuation field added to Orders<br>";
}

$field5 = Vtiger_Field::getInstance('total_valuation', $moduleOrders);
if ($field5) {
    echo "<br> The total_valuation field already exists in Estimates <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_ORDERS_TOTALVALUATION';
    $field5->name = 'total_valuation';
    $field5->table = 'vtiger_orders';
    $field5->column ='total_valuation';
    $field5->columntype = 'decimal(22,2)';
    $field5->uitype = 71;
    $field5->typeofdata = 'N~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 1;
    $field5->summaryfield = 0;

    $blockOrdersVal->addField($field5);
    echo "<br>total_valuation field added to Orders<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";