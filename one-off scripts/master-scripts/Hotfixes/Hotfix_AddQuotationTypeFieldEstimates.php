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
$module = Vtiger_Module::getInstance($moduleName);

echo "<br><h1>Starting To add quotation_type in estimates</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $fieldCheck = Vtiger_Field::getInstance('quotation_type', $module);
    if ($fieldCheck) {
        echo '<p>quotation_type Field already present</p>';

        echo '<p>Fixing the table name</p>';

        $db = PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET  tablename = ? WHERE columnname = ?';
        $result = $db->pquery($sql, ['vtiger_quotes', 'quotation_type']);

        $sql = 'ALTER TABLE `vtiger_quotes` ADD COLUMN quotation_type VARCHAR(100)';
        $db->query($sql);
    } else {
        $picklistOptions = [
            'Guaranteed',
            'Guranteed Not to Exceed',
            'Non-Binding',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_QUOTATION_TYPE';
        $field->name = 'quotation_type';
        $field->table = 'vtiger_quotes';
        $field->column = 'quotation_type';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added quotation_type Field</p>';
    }
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";