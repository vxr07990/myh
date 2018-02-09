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

$moduleName = 'Estimates';
$blockName = 'LBL_QUOTE_INFORMATION';
$fieldName = 'estimate_type';
$picklistOptions = [
    'Non-Binding',
    'Binding',
    'Not To Exceed',
];

echo "<br><h1>Starting To add $fieldname in estimates</h1><br>\n";

$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    print "Failed: no $moduleName\n";
    return;
}

$block = Vtiger_Block::getInstance($blockName, $module);
if (!$block) {
    print "Failed: no $blockName\n";
    return;
}

$fieldCheck = Vtiger_Field::getInstance('estimate_type', $module);

if (!$fieldCheck) {
    $fieldCheck = new Vtiger_Field();
    $fieldCheck->label = 'LBL_ORDERS_ESTIMATE_TYPE';
    $fieldCheck->name = 'estimate_type';
    $fieldCheck->table = 'vtiger_quotes';
    $fieldCheck->column = 'estimate_type';
    $fieldCheck->defaultvalue = 'Non-Binding';
    $fieldCheck->columntype = 'VARCHAR(100)';
    $fieldCheck->uitype = '16';
    $fieldCheck->typeofdata = 'V~O';

    $block->addField($fieldCheck);
    echo '<p>Added estimate_type Field</p>';
}

if (Vtiger_Utils::CheckTable('vtiger_estimate_type')) {
    $db = &PearDatabase::getInstance();
    $sql = 'TRUNCATE TABLE `vtiger_estimate_type`';
    $db->query($sql);
}

$fieldCheck->setPicklistValues($picklistOptions);

print PHP_EOL."\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";