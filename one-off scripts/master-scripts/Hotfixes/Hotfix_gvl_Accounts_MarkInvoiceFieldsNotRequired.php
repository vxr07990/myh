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

echo "<h3>Starting MarkInvoiceFieldsNotRequired For ORDERS AND Accounts</h3>\n";

$moduleName = 'Orders';
$blockName = 'LBL_ORDERS_INVOICE';
$module = Vtiger_Module::getInstance($moduleName);
$tableName = 'vtiger_orders';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block exists</p>\n";

    $fieldName = 'invoice_format';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['V~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'invoice_pkg_format';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['V~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'invoice_document_format';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['V~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'invoice_delivery_format';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['V~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'invoice_finance_charge';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['I~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'commodity';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['V~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }

    $fieldName = 'payment_terms';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>Updating $fieldName to optional</p>\n";
        $db = PearDatabase::getInstance();

        $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
        $db->pquery($sql, ['I~O', $field->id]);
    } else {
        echo "<p>$fieldName Field not found</p>\n";
    }
}

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
$tableName = 'vtiger_accounts';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block exists</p>\n";
    foreach (['invoice_format', 'invoice_pkg_format'] as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            echo "<p>Updating $fieldName to optional</p>\n";
            $db  = PearDatabase::getInstance();
            $sql = 'UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?';
            $db->pquery($sql, ['V~O', $field->id]);
        } else {
            echo "<p>$fieldName Field not found</p>\n";
        }
    }
}

echo "<h3>Ending MarkInvoiceFieldsNotRequired</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";