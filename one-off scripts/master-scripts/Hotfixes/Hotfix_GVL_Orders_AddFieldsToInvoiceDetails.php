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


// OT 3298 Add E-mail and Phone Number to Orders Invoice Details in Orders

echo "<br> Starting Add Email and Phone Fields to Invoice Details in Orders </br> \n";
$Orders = Vtiger_Module::getInstance('Orders');
if ($Orders) {
    echo "<h2>Orders exists, updating fields</h2><br>\n";
} else {
    echo "<h2>Orders not present. Exiting.</h2><br>\n";
    return;
}
$block0 = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $Orders);
if ($block0) {
    echo "<p>The LBL_ORDERS_INVOICE block already exists</p><br> \n";
} else {
    echo "<p>The LBL_ORDERS_INVOICE is not present. Exiting.</p><br> \n";
    return;
}
$fieldPhone = Vtiger_Field::getInstance('invoice_phone', $Orders);
if ($fieldPhone) {
    echo "The invoice_phone field already exists<br>\n";
} else {
    $fieldPhone             = new Vtiger_Field();
    $fieldPhone->label      = 'LBL_INVOICE_PHONE';
    $fieldPhone->name       = 'invoice_phone';
    $fieldPhone->table      = 'vtiger_orders';
    $fieldPhone->column     = 'invoice_phone';
    $fieldPhone->columntype = 'VARCHAR(30)';
    $fieldPhone->uitype     = 11;
    $fieldPhone->typeofdata = 'V~O';
    $block0->addField($fieldPhone);
    echo "The $fieldPhone->name field created.<br>\n";
}
$fieldEmail = Vtiger_Field::getInstance('invoice_email', $Orders);
if ($fieldEmail) {
    echo "The invoice_email field already exists<br>\n";
} else {
    $fieldEmail             = new Vtiger_Field();
    $fieldEmail->label      = 'LBL_INVOICE_EMAIL';
    $fieldEmail->name       = 'invoice_email';
    $fieldEmail->table      = 'vtiger_orders';
    $fieldEmail->column     = 'invoice_email';
    $fieldEmail->columntype = 'VARCHAR(100)';
    $fieldEmail->uitype     = 13;
    $fieldEmail->typeofdata = 'E~O';
    $block0->addField($fieldEmail);
    echo "The $fieldEmail->name field created.<br>\n";
}
$fieldOrder = [
    'bill_street',      'bill_city',
    'bill_state',       'bill_country',
    'payment_type',     'commodity',
    'invoice_format',   'invoice_pkg_format',
    'invoice_document_format',  'invoice_delivery_format',
    'invoice_finance_charge',   'payment_terms',
    'invoice_phone',    'invoice_email'
];
$db    = PearDatabase::getInstance();
foreach ($fieldOrder as $key=>$field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $Orders);
    $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
    $db->pquery($sql, [$key, $fieldInstance->id]);
}
echo "<p>Done reordering fields in the Invoice Details block</p>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";