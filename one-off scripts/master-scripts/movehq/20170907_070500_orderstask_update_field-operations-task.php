<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//5268 Update "Operations Task" Pick List in "Local Operations Task" Module

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$newValues = [
	'Pack',
	'Load',
	'Delivery',
	'Unpack',
	'Auxiliary Service',
	'Debris Pick Up',
	'G’11/APU',
	'Local Move',
	'Local Load',
	'Local Delivery',
	'Commercial Move',
	'Install',
	'Equipment/Material Pickup',
	'Equipment/Material Delivery',
	'Auto Pickup/Delivery',
	'Storage Access',
	'Storage Delivery',
	'Storage In/Warehouse Handling',
	'Storage Out/Warehouse Handling',
	'International Pack',
	'International Load',
	'International Pack/Load',
	'International Delivery/Unpack'
];

$module = Vtiger_Module::getInstance('OrdersTask');
$field = Vtiger_Field::getInstance('operations_task', $module);

if (!$field) {
	echo "Field missing.";
}else{
	$db = PearDatabase::getInstance();

	$db->pquery('TRUNCATE TABLE vtiger_operations_task');
	$field->setNoRolePicklistValues($newValues);
	echo "operations_task picklist updated.";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";