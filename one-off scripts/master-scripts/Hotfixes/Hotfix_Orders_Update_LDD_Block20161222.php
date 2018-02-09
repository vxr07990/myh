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

echo "<h3>Starting Add Driver and Agents Fields</h3>\n";

$moduleName = 'Orders';
$blockName = 'LBL_LONGDISPATCH_INFO';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block exists add the filds</p>\n";

    //**************** DRIVER TRIP FIELD *******************//
    $fieldName = 'driver_trip';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = "INT(19), ADD INDEX ($fieldName)";
        $field->uitype = 10;
        $field->typeofdata = 'I~O';

        $block->addField($field);
	$field->setRelatedModules(array('Employees'));
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** AGENT TRIP FIELD *******************//
    $fieldName = 'agent_trip';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = "INT(19), ADD INDEX ($fieldName)";
        $field->uitype = 10;
        $field->typeofdata = 'I~O';

        $block->addField($field);
		$field->setRelatedModules(array('Agents'));
        echo "<p>Added $fieldName Field</p>\n";
    }
} else {
    echo "<p>The $blockName block doesn't exist</p>\n";
}

echo "<h3>Ending Add Driver and Agents Fields</h3>\n";

//Updating existing orders records

$db = PearDatabase::getInstance();
$db->pquery('UPDATE vtiger_orders, vtiger_trips 
SET vtiger_orders.driver_trip = vtiger_trips.driver_id, vtiger_orders.agent_trip = vtiger_trips.agent_unit
WHERE vtiger_orders.orders_trip = vtiger_trips.tripsid');

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";