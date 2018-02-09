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

$moduleName = 'Contracts';
$blockName = 'LBL_CONTRACTS_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

echo "<br><h1>Starting To add status field to contracts</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $fieldCheck = Vtiger_Field::getInstance('contract_status', $module);
    if ($fieldCheck) {
        echo '<p>Contracts status field already present</p>';
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_contract_status`";
        $db->pquery($sql, array());

        $picklistOptions = [
            'New',
            'Requested',
            'Approved',
            'On Hold',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_STATUS';
        $field->name = 'contract_status';
        $field->table = 'vtiger_contracts';
        $field->column = 'contract_status';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->sequence = '10';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added contracts status field</p>';
    }
} else {
    echo '<p>Contracts status field could not be added, couldn\'t find LBL_CONTRACTS_INFORMATION block</p>';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";