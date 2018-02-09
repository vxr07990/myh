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


// OT 3188 - Adding values to fleet type picklist.

$module1 = Vtiger_Module::getInstance('Employees');
$picklistValues = ['None', 'Short Haul', 'Long Haul'];
$fieldName = 'fleet_type';

$field76 = Vtiger_Field::getInstance($fieldName, $module1);
if ($field76) {
    echo "<p>The fleet_type field exists. Updating picklist values.</p><br>";
    $tableName = 'vtiger_'.$fieldName;
    $db = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, array());
    $field76->setPicklistValues($picklistValues);
    echo "<p>Updated picklist values.</p><br>";
} else {
    $block10 = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $module1);
    if ($block10) {
        echo "<p>The LBL_DRIVER_INFORMATION block exists</p><br>";
        echo "<p>The fleet_type field doesn't exist. Adding.</p><br>";
        $field76             = new Vtiger_Field();
        $field76->label      = 'LBL_EMPLOYEES_FLEET_TYPE';
        $field76->name       = 'fleet_type';
        $field76->table      = 'vtiger_employees';
        $field76->column     = 'fleet_type';
        $field76->columntype = 'VARCHAR(255)';
        $field76->uitype     = 16;
        $field76->typeofdata = 'V~O';
        $block10->addField($field76);
        $field76->setPicklistvalues($picklistValues);
        echo "<p>The fleet_type field added</p>";
    } else {
        echo "<p>The LBL_DRIVER_INFORMATION missing. Exiting without adding field.</p><br>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";