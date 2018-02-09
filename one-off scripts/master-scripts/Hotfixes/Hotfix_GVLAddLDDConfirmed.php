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
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br>begin add LDD information fields<br>";

$db = PearDatabase::getInstance();

$ordersInstance = Vtiger_Module::getInstance('Orders');
$LDDInformation = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $ordersInstance);

$field2 = Vtiger_Field::getInstance('orders_ldd_pldate', $ordersInstance);

if ($field2) {
    echo "<br> Field 'orders_ldd_pudate' is already present. <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ORDERS_PLANNEDLOADDATE';
    $field2->name = 'orders_ldd_pldate';
    $field2->table = 'vtiger_orders';
    $field2->column = 'orders_ldd_pldate';
    $field2->columntype = 'DATE';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~O';

    $LDDInformation->addField($field2);
    echo 'added field orders_ldd_pudate<br>';
}

$field1 = Vtiger_Field::getInstance('orders_ldd_pddate', $ordersInstance);

if ($field1) {
    echo "<br> Field 'orders_ldd_pldate' is already present. <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ORDERS_PLANNEDDELIVERYDATE';
    $field1->name = 'orders_ldd_pddate';
    $field1->table = 'vtiger_orders';
    $field1->column = 'orders_ldd_pddate';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~O';

    $LDDInformation->addField($field1);
    echo 'added field orders_ldd_pldate<br>';
}

$field3 = Vtiger_Field::getInstance('orders_ldd_plconfirmed', $ordersInstance);

if ($field3) {
    echo "<br> Field 'orders_ldd_puconfirmed' is already present. <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ORDERS_PLCONFIRMED';
    $field3->name = 'orders_ldd_plconfirmed';
    $field3->table = 'vtiger_orders';
    $field3->column = 'orders_ldd_plconfirmed';
    $field3->columntype = 'VARCHAR(3)';
    $field3->uitype = 56;
    $field3->typeofdata = 'V~O';

    $LDDInformation->addField($field3);
    echo 'added field orders_ldd_puconfirmed<br>';
}

$field4 = Vtiger_Field::getInstance('orders_ldd_pdconfirmed', $ordersInstance);

if ($field4) {
    echo "<br> Field 'orders_ldd_pudate' is already present. <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ORDERS_PDCONFIRMED';
    $field4->name = 'orders_ldd_pdconfirmed';
    $field4->table = 'vtiger_orders';
    $field4->column = 'orders_ldd_pdconfirmed';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $LDDInformation->addField($field4);
    echo 'added field orders_ldd_plconfirmed<br>';
}

echo "<br>end add LDD information fields";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";