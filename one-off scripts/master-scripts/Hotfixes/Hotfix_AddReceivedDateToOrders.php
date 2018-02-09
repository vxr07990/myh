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

$moduleName = 'Orders';
$module = Vtiger_Module::getInstance($moduleName);
$blockName = 'LBL_ORDERS_INFORMATION';

echo "<br><h1>Starting To add Received Date in contacts</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);

if ($block) {
    $field = Vtiger_Field::getInstance('received_date', $module);

    if ($field) {
        echo '<p>received_date field already exists</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_RECEIVED_DATE';
        $field->name = 'received_date';
        $field->table = 'vtiger_orders';
        $field->column = 'received_date';
        $field->columntype = 'DATE';
        $field->uitype = '23';
        $field->sequence = '21';
        $field->typeofdata = 'D~O';
        $block->addField($field);

        echo '<p>Added received_date field to LBL_ORDERS_INFORMATION details block</p>';

        // Need to hide the start date field
        $field = Vtiger_Field::getInstance('startdate', $module);
        $db = PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ? ';
        $result = $db->pquery($sql, [1, $field->id]);
        echo '<p>Moved Billing Type field down in the ui</p>';
    }
} else {
    echo '<p>'.$blockName.' Block not found</p>';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";