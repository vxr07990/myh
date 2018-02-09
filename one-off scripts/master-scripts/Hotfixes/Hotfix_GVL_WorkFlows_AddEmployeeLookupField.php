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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$employeesInstance = Vtiger_Module::getInstance('Employees');
$employeesBlock = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $employeesInstance);
if(!$employeesBlock){
	echo "Couldn't find block. Failed.<br>\n";
	return;
}
$field0 = Vtiger_Field::getInstance('related_user_id', $employeesInstance);
if ($field0) {
	echo "The related_user_id field already exists<br>\n";
} else {
	$field0             = new Vtiger_Field();
	$field0->label      = 'LBL_RELATED_USER_ID';
	$field0->name       = 'related_user_id';
	$field0->table      = 'vtiger_employees';
	$field0->column     = 'related_user_id';
	$field0->columntype = 'VARCHAR(100)';
	$field0->uitype     = 53;
	$field0->typeofdata = 'V~O';
	$employeesBlock->addField($field0);
	echo "The related_user_id field added successfully<br>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";



