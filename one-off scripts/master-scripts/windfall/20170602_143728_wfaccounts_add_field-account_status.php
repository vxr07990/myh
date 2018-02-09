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

$moduleName = 'WFAccounts';
$blockName = 'LBL_WFACCOUNTS_DETAIL';
$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "$moduleName not present. <br>\n";
    return;
}

$block = Vtiger_Block::getInstance($blockName, $module);
if(!$block){
    echo "$blockName not present. <br>\n";
    return;
}

$db = PearDatabase::getInstance();



$field = Vtiger_Field::getInstance('account_status', $module);
if ($field) {
    echo '<p> account_status Field already present</p>';
} else {
    $picklistOptions = [
        'New',
        'Approved',
        'Inactive',
    ];

    $field = new Vtiger_Field();
    $field->label = 'LBL_WFACCOUNTS_ACCOUNT_STATUS';
    $field->name = 'account_status';
    $field->table = 'vtiger_wfaccounts';
    $field->column = 'account_status';
    $field->columntype = 'VARCHAR(150)';
    $field->uitype = '16';
    $field->typeofdata = 'V~O';
    $field->setPicklistValues($picklistOptions);

    $block->addField($field);

    echo '<p>Added account_status field to WFAccounts</p>';
}

// Reorder Fields
$orderOfFields = ['name', 'account_status', 'wfaccounts_type', 'company',
                  'national_account', 'assigned_user_id', 'primary_email',
                  'primary_phone', 'download_to_device', 'description',
                  'logo', 'agentid'];

$count = 0;
foreach ($orderOfFields as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $count++;
        $params = [$count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        echo '<p>UPDATED '.$val.' to the sequence</p>';
    } else {
        echo '<p>'.$val.' Field don\'t exists</p>';
    }
}
