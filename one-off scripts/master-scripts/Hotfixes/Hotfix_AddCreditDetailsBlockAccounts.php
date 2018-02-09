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
$blockName = 'LBL_ACCOUNT_CREDIT_DETAILS';
$module = Vtiger_Module::getInstance($moduleName);

$db = PearDatabase::getInstance();

echo '<h3>Starting AddCreditDetailsBlockAccounts</h3>';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo '<p>LBL_ACCOUNT_CREDIT_DETAILS Block exists</p>';
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_ACCOUNT_CREDIT_DETAILS';
    $block->sequence = '4';
    $module->addBlock($block);

    echo '<p>LBL_ACCOUNT_CREDIT_DETAILS Block Added</p>';
}

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {

    // credit_check_pass Field
    $field = Vtiger_Field::getInstance('credit_check_pass', $module);
    if ($field) {
        echo '<p> po_required Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_CHECK_PASS';
        $field->name = 'credit_check_pass';
        $field->table = 'vtiger_account';
        $field->column = 'credit_check_pass';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = '56';
        $field->sequence = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added credit_check_pass field to accounts</p>';
    }

    // credit_limit Field
    $field = Vtiger_Field::getInstance('credit_limit', $module);
    if ($field) {
        echo '<p>credit_limit Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_LIMIT';
        $field->name = 'credit_limit';
        $field->table = 'vtiger_account';
        $field->column = 'credit_limit';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype = '71';
        $field->sequence = '2';
        $field->typeofdata = 'V~O';
        $block->addField($field);

        echo '<p>Added credit_limit  field to accounts</p>';
    }

    // credit_hold Field
    $field = Vtiger_Field::getInstance('credit_hold', $module);
    if ($field) {
        echo '<p>credit_hold Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_HOLD';
        $field->name = 'credit_hold';
        $field->table = 'vtiger_account';
        $field->column = 'credit_hold';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = '56';
        $field->sequence = '3';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added credit_hold field to accounts</p>';
    }

    // credit_hold Field
    $field = Vtiger_Field::getInstance('credit_hold_override', $module);
    if ($field) {
        echo '<p>credit_hold_override Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_HOLD_OVERRIDE';
        $field->name = 'credit_hold_override';
        $field->table = 'vtiger_account';
        $field->column = 'credit_hold_override';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = '56';
        $field->sequence = '4';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added credit_hold_override field to accounts</p>';
    }

    // credit_check_date Field
    $field = Vtiger_Field::getInstance('credit_check_date', $module);
    if ($field) {
        echo '<p>credit_check_date Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_CHECK_DATE';
        $field->name = 'credit_check_date';
        $field->table = 'vtiger_account';
        $field->column = 'credit_check_date';
        $field->columntype = 'DATE';
        $field->uitype = '5';
        $field->sequence = '5';
        $field->typeofdata = 'D~O';

        $block->addField($field);

        echo '<p>Added credit_check_date field to accounts</p>';
    }

    // account_balance Field
    $field = Vtiger_Field::getInstance('account_balance', $module);
    if ($field) {
        echo '<p>account_balance Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CREDIT_ACCOUNT_BALANCE';
        $field->name = 'account_balance';
        $field->table = 'vtiger_account';
        $field->column = 'account_balance';
        $field->columntype = 'DECIMAL(24, 2)';
        $field->uitype = '71';
        $field->sequence = '6';
        $field->typeofdata = 'N~O';
        $block->addField($field);

        echo '<p>Added account_balance field to accounts</p>';
    }
} else {
    echo '<p>LBL_ACCOUNT_CREDIT_DETAILS Still Not Found</p>';
}

echo '<h3>Ending AddCreditDetailsBlockAccounts</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";