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



$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

echo "<h3>Starting AddInvoiceDetailsBlock</h3>\n";

$moduleName = 'Orders';
$picklistFieldName = 'payment_type';

$picklistOrder = [
    'AG-Cr Card',
    'DR-Cr Card',
    'Invoice',
    'COD',
    'Prepaid',
    'Split',
    'Unknown',
];

$oldPicklist = [
    'Check',
    'Electronic Transfer',
    'Credit',
    'Cash',
];

$module = Vtiger_Module::getInstance($moduleName);
$db = PearDatabase::getInstance();

$field = Vtiger_Field::getInstance($picklistFieldName, $module);
if ($field) {
    echo "payment_type Field exists lets check the options<br>\n";


    $sql = 'SELECT * FROM `vtiger_payment_type`';
    $result = $db->query($sql);
    $data = [];
    while ($row = $result->fetchRow()) {
        $data[] = $row['payment_type'];
    }

    $difference = array_diff($data, $oldPicklist);
    if (empty($difference)) {
        echo "vtiger_payment_type has the old values lets update them<br>\n";
        Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_payment_type`");

        $count = 1;
        foreach ($picklistOrder as $val) {
            echo "adding $val into picklist<br>\n";
            $sql = "INSERT INTO `vtiger_payment_type` (payment_typeid, payment_type, sortorderid, presence) VALUES (?, ?, ?, ?)";
            $params = [$count, $val, $count, 1];
            $db->pquery($sql, $params);
            $count++;
        }
        echo "Added all the new picklist values<br>\n";
    } else {
        echo "Looks like the picklist has already been updated<br>\n";
    }
} else {
    echo "payment_type Field doesn't exists<br>\n";
}

///-----------------------------------------------------------------//

echo "Removing Some fields from orders invoice block<br>\n";

$block = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $module);
if ($block) {
    $field = Vtiger_Field::getInstance('pricing_type', $module);
    if ($field) {
        $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
        $db->pquery($sql, [$field->id]);
        echo "Removed pricing_type from vtiger_field<br>\n";
    }

    $field = Vtiger_Field::getInstance('pricing_mode', $module);
    if ($field) {
        $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
        $db->pquery($sql, [$field->id]);
        echo "Removed pricing_mode from vtiger_field<br>\n";
    }

    $field = Vtiger_Field::getInstance('invoice_status', $module);
    if ($field) {
        $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
        $db->pquery($sql, [$field->id]);
        echo "Removed invoice_status from vtiger_field<br>\n";
    }

    $field = Vtiger_Field::getInstance('bill_weight', $module);
    if ($field) {
        $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
        $db->pquery($sql, [$field->id]);
        echo "Removed bill_weight from vtiger_field<br>\n";
    }

    echo "Done removing fields from LBL_ORDERS_INVOICE<br>\n";

    /////---------------------------------------------------

    echo "Starting adding fields to LBL_ORDERS_INVOICE<br>\n";

    $field = Vtiger_Field::getInstance('commodity', $module);
    if ($field) {
        echo '<p>commodity Field already present</p>';
    } else {
        $picklistOptions = [

        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_COMMODITY';
        $field->name = 'commodity';
        $field->table = 'vtiger_orders';
        $field->column = 'commodity';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~R';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added commodity Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_format', $module);
    if ($field) {
        echo '<p>invoice_format Field already present</p>';
    } else {
        $picklistOptions = [

        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_TEMPLATE';
        $field->name = 'invoice_format';
        $field->table = 'vtiger_orders';
        $field->column = 'invoice_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~R';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_document_format Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_pkg_format', $module);
    if ($field) {
        echo '<p>invoice_packet Field already present</p>';
    } else {
        $picklistOptions = [

        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_PACKET';
        $field->name = 'invoice_pkg_format';
        $field->table = 'vtiger_orders';
        $field->column = 'invoice_pkg_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~R';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_packet Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_document_format', $module);
    if ($field) {
        echo '<p>invoice_document_format Field already present</p>';
    } else {
        $picklistOptions = [

        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_DOCUMENT_FORMAT';
        $field->name = 'invoice_document_format';
        $field->table = 'vtiger_orders';
        $field->column = 'invoice_document_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~R';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_document_format Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_delivery_format', $module);
    if ($field) {
        echo '<p>invoice_delivery_format Field already present</p>';
    } else {
        $picklistOptions = [

        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_DELIVERY_FORMAT';
        $field->name = 'invoice_delivery_format';
        $field->table = 'vtiger_orders';
        $field->column = 'invoice_delivery_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~R';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_delivery_format Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_finance_charge', $module);
    if ($field) {
        echo '<p>customer_number field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_INVOICE_FINANCE_CHARGE';
        $field->name = 'invoice_finance_charge';
        $field->table = 'vtiger_orders';
        $field->column = 'invoice_finance_charge';
        $field->columntype = 'INT';
        $field->uitype = '1';
        $field->typeofdata = 'I~R';

        $block->addField($field);

        echo '<p>Added invoice_finance_charge field to accounts</p>';
    }

    $field = Vtiger_Field::getInstance('payment_terms', $module);
    if ($field) {
        echo '<p>payment_terms field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_PAYMENT_TERMS';
        $field->name = 'payment_terms';
        $field->table = 'vtiger_orders';
        $field->column = 'payment_terms';
        $field->columntype = 'INT';
        $field->uitype = '1';
        $field->typeofdata = 'I~R';

        $block->addField($field);

        echo '<p>Added payment_terms field to accounts</p>';
    }



    echo "Done adding fields to LBL_ORDERS_INVOICE<br>\n";
} else {
    echo "Could not remove some fields, couldn't find LBL_ORDERS_INVOICE block<br>\n";
}






echo "<h3>Ending AddInvoiceDetailsBlock</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";