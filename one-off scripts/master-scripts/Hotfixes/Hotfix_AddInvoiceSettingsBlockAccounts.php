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
$blockName = 'LBL_ACCOUNT_INVOICESETTINGS';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);

echo '<h3>Starting Add Invoice Settings Block to Accounts</h3>';

if ($block) {
    echo '<p>LBL_ACCOUNT_INVOICESETTINGS Block exists</p>';
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_ACCOUNT_INVOICESETTINGS';
    $block->sequence = '5';
    $module->addBlock($block);

    echo '<p>LBL_ACCOUNT_INVOICESETTINGS Block Added</p>';
}

if ($block) {
    $field = Vtiger_Field::getInstance('commodity', $module);
    if ($field) {
        echo '<p>commodity Field already present</p>';
    } else {
        $picklistOptions = [
            'Commodity 1',
            'Commodity 2',
            'Commodity 3',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNT_INVOICE_SETTINGS';
        $field->name = 'commodity';
        $field->table = 'vtiger_account';
        $field->column = 'commodity';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added commodity Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_document_format', $module);
    if ($field) {
        echo '<p>invoice_document_format Field already present</p>';
    } else {
        $picklistOptions = [
            'PDF',
            'Excel',
            'Word',
            'HTML',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_INVOICE_DOCUMENT_FORMAT';
        $field->name = 'invoice_document_format';
        $field->table = 'vtiger_account';
        $field->column = 'invoice_document_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_document_format Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_document_format', $module);
    if ($field) {
        echo '<p>invoice_document_format Field already present</p>';
    } else {
        $picklistOptions = [
            'PDF',
            'Excel',
            'Word',
            'HTML',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_INVOICE_DOCUMENT_FORMAT';
        $field->name = 'invoice_document_format';
        $field->table = 'vtiger_account';
        $field->column = 'invoice_document_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_document_format Field</p>';
    }

    $field = Vtiger_Field::getInstance('invoice_delivery_format', $module);
    if ($field) {
        echo '<p>invoice_delivery_format Field already present</p>';
    } else {
        $picklistOptions = [
            'Customer Portal',
            'E-mail',
            'Mail',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_INVOICE_DELIVERY_FORMAT';
        $field->name = 'invoice_delivery_format';
        $field->table = 'vtiger_account';
        $field->column = 'invoice_delivery_format';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added invoice_delivery_format Field</p>';
    }

    /// Create the table to store the invoice data
    if (!Vtiger_Utils::CheckTable('vtiger_account_invoicesettings')) {
        $db = PearDatabase::getInstance();
        $sql = 'CREATE TABLE `vtiger_account_invoicesettings` ( `id` INT NOT NULL AUTO_INCREMENT , `record_id` INT NOT NULL , `commodity` VARCHAR(100) NOT NULL , `invoice_template` VARCHAR(100) NOT NULL , `invoice_packet` VARCHAR(100) NOT NULL , `document_format` VARCHAR(100) NOT NULL , `invoice_delivery` VARCHAR(100) NOT NULL , `finance_charge` VARCHAR(20) NULL , `payment_terms` VARCHAR(255) NULL , PRIMARY KEY (`id`))';
        $db->query($sql);
        echo '<p>Created vtiger_account_invoicesettings table</p>';
    } else {
        echo '<p>vtiger_account_invoicesettings already exists</p>';
    }
}



echo '<h3>Ending Add Invoice Settings Block to Accounts</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";