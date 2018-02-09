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


// OT 3256 - Adding fields to Orders for pulling in billing address information

echo "<br> Starting Add Billing Address Description and Companty Fields to Invoice Details in Orders </br> \n";
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
$fielddesc = Vtiger_Field::getInstance('bill_addrdesc', $Orders);
if ($fielddesc) {
    echo "The orders_billaddrdesc field already exists<br>\n";
} else {
    $fielddesc             = new Vtiger_Field();
    $fielddesc->label      = 'LBL_ORDERS_BILLADDRDESC';
    $fielddesc->name       = 'bill_addrdesc';
    $fielddesc->table      = 'vtiger_orders';
    $fielddesc->column     = 'bill_addrdesc';
    $fielddesc->columntype = 'VARCHAR(255)';
    $fielddesc->uitype     = 1;
    $fielddesc->typeofdata = 'V~O';
    $block0->addField($fielddesc);
    echo "The $fielddesc->name field created successfully<br> \n";
}

$fieldCompany = Vtiger_Field::getInstance('bill_company', $Orders);
if ($fieldCompany) {
    echo "The company field already exists<br>\n";
} else {
    $fieldCompany             = new Vtiger_Field();
    $fieldCompany->label      = 'LBL_ORDERS_BILLCOMPANY';
    $fieldCompany->name       = 'bill_company';
    $fieldCompany->table      = 'vtiger_orders';
    $fieldCompany->column     = 'bill_company';
    $fieldCompany->columntype = 'VARCHAR(100)';
    $fieldCompany->uitype     = 1;
    $fieldCompany->typeofdata = 'V~O';
    $block0->addField($fieldCompany);
    echo "The $fieldCompany->name field created successfully<br> \n";
}

$fieldOrder = [
//    'invoice_phone',    'invoice_email',
//    'bill_addrdesc', 'bill_company',
//    'bill_street',      'bill_pobox',
//    'bill_city',        'bill_state',
//    'bill_code',        'bill_country',
//    'payment_type',     'commodity',
//    'invoice_format',   'invoice_pkg_format',
//    'invoice_document_format',  'invoice_delivery_format',
//    'invoice_finance_charge',   'payment_terms'
    'bill_street',              'bill_pobox',
    'bill_addrdesc',            'bill_company',
    'bill_city',                'bill_state',
    'bill_code',                'bill_country',
    'payment_type',             'commodity',
    'invoice_format',           'invoice_pkg_format',
    'invoice_document_format',  'invoice_delivery_format',
    'invoice_finance_charge',   'payment_terms',
    'invoice_phone',            'invoice_email'
];
$db    = PearDatabase::getInstance();
foreach ($fieldOrder as $key=>$field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $Orders);
    $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
    $db->pquery($sql, [$key, $fieldInstance->id]);
}
echo "<p>Done reordering fields in the Invoice Details block</p>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";