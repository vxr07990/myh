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

echo "<h3>Starting AddPhoneTypeFields</h3>\n";

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
$tableName = 'vtiger_account';
$block = Vtiger_Block::getInstance($blockName, $module);

$addedField = false;

if ($block) {
    echo "<p>The $blockName block exists</p>\n";


    //**************** Primary Phone Type FIELD *******************//
    $fieldName = 'primary_phone_type';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $picklistOptions = [
            'Home',
            'Work',
            'Cell',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);

        $addedField = true;

        echo "<p>Added $fieldName Field</p>\n";
    }


    //**************** Secondary Phone Type FIELD *******************//
    $fieldName = 'secondary_phone_type';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $picklistOptions = [
            'Home',
            'Work',
            'Cell',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);

        $addedField = true;

        echo "<p>Added $fieldName Field</p>\n";
    }
} else {
    echo "<p>The $blockName block not found</p>\n";
}

// Reorder fields in the ui
if ($addedField) {
    echo "<p>Reordering fields in the account information</p>\n";
    $fieldOrder = [
        'accountname',          'apn',
        'website',              'phone',
        'tickersymbol',         'primary_phone_type',
        'account_id',           'otherphone',
        'employees',            'secondary_phone_type',
        'email1',               'fax',
        'email2',               'ownership',
        'industry',             'rating',
        'accounttype',          'siccode',
        'annual_revenue',       'assigned_user_id',
        'emailoptout',          'agentid',
        'notify_owner'
    ];

    $db = PearDatabase::getInstance();
    $count = 0;
    foreach ($fieldOrder as $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);
        if ($field == 'phone') {
            $sql = 'UPDATE `vtiger_field` SET sequence = ?, fieldlabel = ? WHERE fieldid = ?';
            $db->pquery($sql, [$count++, 'LBL_ACCOUNTS_PHONE', $fieldInstance->id]);
        } else {
            $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, [$count++, $fieldInstance->id]);
        }
    }
    echo "<p>Done reordering fields in the account information</p>\n";
}


echo "<h3>Ending AddPhoneTypeFields</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";