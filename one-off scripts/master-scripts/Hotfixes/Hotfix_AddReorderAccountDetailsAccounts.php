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
$blockName = 'LBL_ACCOUNT_DETAILS';
$module = Vtiger_Module::getInstance($moduleName);
$db = PearDatabase::getInstance();

echo '<h3>Starting AddReorderAccountDetailsAccounts</h3>';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo '<p>LBL_ACCOUNT_DETAILS Block exists</p>';
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_ACCOUNT_DETAILS';
    $block->sequence = '2';
    $module->addBlock($block);
}

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {

    // leadsource Name Field
    $field = Vtiger_Field::getInstance('leadsource', $module);
    if ($field) {
        echo '<p>leadsource Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNT_LEADSOURCE';
        $field->name = 'leadsource';
        $field->table = 'vtiger_account';
        $field->column = 'leadsource';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added leadsource field to accounts</p>';
    }

    // annualrevenue Name Field
    $field = Vtiger_Field::getInstance('annualrevenue', $module);
    if ($field) {
        echo '<p>annualrevenue Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ANNUALREVENUE';
        $field->name = 'annualrevenue';
        $field->table = 'vtiger_account';
        $field->column = 'annualrevenue';
        $field->columntype = 'decimal(25,8)';
        $field->uitype = '71';
        $field->typeofdata = 'N~O';

        $block->addField($field);

        echo '<p>Added annualrevenue field to accounts</p>';
    }


    // Reorder Fields
    $orderOfFields = ['leadsource', 'website', 'employees', 'ownership', 'industry', 'accounttype', 'rating', 'siccode', 'annualrevenue', 'tickersymbol'];

    $count = 0;
    foreach ($orderOfFields as $val) {
        $field = Vtiger_Field::getInstance($val, $module);
        if ($field) {
            $count++;
            $params = [$block->id, 2, $count, $field->id];
            $sql = 'UPDATE `vtiger_field` SET block = ?, presence = ?, sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, $params);
            echo '<p>UPDATED '.$val.' to the sequence</p>';
        } else {
            echo '<p>'.$val.' Field don\'t exists</p>';
        }
    }
} else {
    echo '<p>LBL_ACCOUNT_DETAILS Block doesn\'t exist</p>';
}

echo '<h3>Ended AddReorderAccountDetailsAccounts</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";