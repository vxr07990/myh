<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


// OT 2898 - Correcting Confirmed fields for Orders LDD items in Trips to be picklists instead of checkboxes.
// version 2 - OT 16950 Load Date Confirmed and Delivery Date confirmed should be read only fields, changing to text displays populated from Trips.

echo "<br>Begin modify LDD confirmed fields<br>";

$db = PearDatabase::getInstance();

$ordersInstance = Vtiger_Module::getInstance('Orders');
$LDDInformation = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $ordersInstance);

$field0 = Vtiger_Field::getInstance('orders_ldd_plconfirmed', $ordersInstance);

if ($field0) {
    echo "<br> Field 'orders_ldd_puconfirmed' is already present.<br>";
    $sql = "SELECT fieldid, columnname, tablename, uitype FROM `vtiger_field` WHERE columnname = ? AND (uitype = ? OR uitype = ?)";
    $result = $db->pquery($sql, ['orders_ldd_plconfirmed', 16, 56]);
    while ($row =& $result->fetchRow()) {
        if ($row['uitype'] == 16 or $row['uitype'] == 56) {
            $sql = 'UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldid` = ?  LIMIT 1';
            $query = $db->pquery($sql, [$row['fieldid']]);
            //$field0->setPicklistValues(Array('AM','PM'));
            echo "<br>Modified field.</br>";
        }
    }
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_ORDERS_PLCONFIRMED';
    $field0->name = 'orders_ldd_plconfirmed';
    $field0->table = 'vtiger_orders';
    $field0->column = 'orders_ldd_plconfirmed';
    $field0->columntype = 'VARCHAR(3)';
    $field0->uitype = 1;
    $field0->typeofdata = 'V~O';
    //$field0->setPicklistValues(Array('AM','PM'));

    $LDDInformation->addField($field0);
    echo 'added field orders_ldd_puconfirmed<br>';
}

$field1 = Vtiger_Field::getInstance('orders_ldd_pdconfirmed', $ordersInstance);

if ($field1) {
    echo "<br> Field 'orders_ldd_pdconfirmed' is already present.<br>";
    $sql = "SELECT fieldid, columnname, tablename, uitype FROM `vtiger_field` WHERE columnname = ? AND (uitype = ? OR uitype = ?)";
    $result = $db->pquery($sql, ['orders_ldd_pdconfirmed', 16, 56]);
    while ($row =& $result->fetchRow()) {
        if ($row['uitype'] == 16 or $row['uitype'] == 56) {
            $sql = 'UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldid` = ?  LIMIT 1';
            $query = $db->pquery($sql, [$row['fieldid']]);
            //$field1->setPicklistValues(Array('AM','PM'));
            echo "<br>Modified field.</br>";
        }
    }
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ORDERS_PDCONFIRMED';
    $field1->name = 'orders_ldd_pdconfirmed';
    $field1->table = 'vtiger_orders';
    $field1->column = 'orders_ldd_pdconfirmed';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
//    $field1->setPicklistValues(Array('AM','PM'));

    $LDDInformation->addField($field1);
    echo 'added field orders_ldd_plconfirmed<br>';
}

echo "<br>End modify LDD confirmed fields<br/>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";