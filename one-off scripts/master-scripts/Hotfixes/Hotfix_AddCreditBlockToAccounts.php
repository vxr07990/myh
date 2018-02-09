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

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_CREDIT_REQUEST';
$module = Vtiger_Module::getInstance($moduleName);

$db = PearDatabase::getInstance();

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo '<p>LBL_ACCOUNT_CREDIT_REQUEST Block exists</p>';
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_ACCOUNT_CREDIT_REQUEST';
    $block->sequence = '2';
    $module->addBlock($block);

    echo '<p>LBL_ACCOUNT_CREDIT_REQUEST Block Added</p>';
}

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {

    // duns_number Name Field
    $field = Vtiger_Field::getInstance('duns_number', $module);
    if ($field) {
        echo '<p>duns_number Field already present</p>';

        $sql = 'UPDATE `vtiger_field` SET block = ?, presence = ?, sequence = ? WHERE fieldid = ?';
        $params = [$block->id, 2, 1, $field->id];
        $db->pquery($sql, $params);
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_DUNS_NUMBER';
        $field->name = 'duns_number';
        $field->table = 'vtiger_account';
        $field->column = 'duns_number';
        $field->columntype = 'INT(19)';
        $field->uitype = '1';
        $field->sequence = '1';
        $field->typeofdata = 'N~O';
        $block->addField($field);

        echo '<p>Added duns_number field to accounts</p>';
    }

    // credit amount requested Name Field
    $field = Vtiger_Field::getInstance('credit_amount_requested', $module);
    if ($field) {
        echo '<p>credit_amount_requested Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_AMOUNT_REQUESTED';
        $field->name = 'credit_amount_requested';
        $field->table = 'vtiger_account';
        $field->column = 'credit_amount_requested';
        $field->columntype = 'DECIMAL(5, 2)';
        $field->uitype = '71';
        $field->sequence = '2';
        $field->typeofdata = 'N~O';
        $block->addField($field);

        echo '<p>Added credit_amount_requested field to accounts</p>';
    }

    // credit amount requested Field
    $field = Vtiger_Field::getInstance('finance_charge_requested', $module);
    if ($field) {
        echo '<p>finance_charge_requested  Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_FINANCE_CHARGE';
        $field->name = 'finance_charge_requested';
        $field->table = 'vtiger_account';
        $field->column = 'finance_charge_requested';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype = '9';
        $field->sequence = '3';
        $field->typeofdata = 'V~O';
        $block->addField($field);

        echo '<p>Added finance_charge_requested  field to accounts</p>';
    }

    // credit amount requested Field
    $field = Vtiger_Field::getInstance('credit_payment_terms', $module);
    if ($field) {
        echo '<p>credit_payment_terms  Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_PAYMENT_TERMS';
        $field->name = 'credit_payment_terms';
        $field->table = 'vtiger_account';
        $field->column = 'credit_payment_terms';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype = '1';
        $field->sequence = '4';
        $field->typeofdata = 'V~O';
        $block->addField($field);

        echo '<p>Added credit_payment_terms  field to accounts</p>';
    }

    // invoicing_frequency Field
    $field = Vtiger_Field::getInstance('invoicing_frequency', $module);
    if ($field) {
        echo '<p>Added invoicing_frequency Field already present</p>';
    } else {
        $picklistOptions = [
            'By Shipment',
            'Progress Billing',
            'Phase',
            'Monthly',
            'End Of Project',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_INVOICING_FREQUENCY';
        $field->name = 'invoicing_frequency';
        $field->table = 'vtiger_account';
        $field->column = 'invoicing_frequency';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->sequence = '5';
        $field->typeofdata = 'V~O';
        $field->setPicklistValues($picklistOptions);

        $block->addField($field);

        echo '<p>Added invoicing_frequency field to accounts</p>';
    }

    // po_required Field
    $field = Vtiger_Field::getInstance('po_required', $module);
    if ($field) {
        echo '<p>Added po_required Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_PO_REQUIRED';
        $field->name = 'po_required';
        $field->table = 'vtiger_account';
        $field->column = 'po_required';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = '56';
        $field->sequence = '6';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added po_required field to accounts</p>';
    }
    
    // billing_contact Field
    $field = Vtiger_Field::getInstance('billing_contact', $module);
    if ($field) {
        echo '<p>Added billing_contact Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_BILLING_CONTACT';
        $field->name = 'billing_contact';
        $field->table = 'vtiger_account';
        $field->column = 'billing_contact';
        $field->columntype = 'INT(19)';
        $field->uitype = '10';
        $field->sequence = '7';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setRelatedModules(array('Contacts'));

        echo '<p>Added billing_contact field to accounts</p>';
    }

    // ap_contact Field
    $field = Vtiger_Field::getInstance('ap_contact', $module);
    if ($field) {
        echo '<p>Added ap_contact Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_AP_CONTACT';
        $field->name = 'ap_contact';
        $field->table = 'vtiger_account';
        $field->column = 'ap_contact';
        $field->columntype = 'INT(19)';
        $field->uitype = '10';
        $field->sequence = '8';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setRelatedModules(array('Contacts'));

        echo '<p>Added ap_contact field to accounts</p>';
    }
} else {
    echo '<p>LBL_ACCOUNT_CREDIT_REQUEST Still Not Found</p>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";