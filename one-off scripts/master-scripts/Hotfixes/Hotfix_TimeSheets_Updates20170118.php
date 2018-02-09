<?php

if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
	//already ran
	print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
	return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$module = Vtiger_Module::getInstance('TimeSheets');
$block = Vtiger_Block::getInstance('LBL_TIMESHEETS_INFORMATION', $module);
$field = Vtiger_Field::getInstance('timesheet_personnelroleid', $module);

if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_TIMESHEET_PERSONNEL_ROLE';
    $field->name = 'timesheet_personnelroleid';
    $field->table = 'vtiger_timesheets';  // This is the tablename from your database that the new field will be added to.
    $field->column = 'timesheet_personnelroleid';   //  This will be the columnname in your database for the new field.
    $field->columntype = 'INT(10)';
    $field->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'I~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $block->addField($field);
    $field->setRelatedModules(['EmployeeRoles']);
}

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 1 WHERE fieldname = 'employee_id' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 2 WHERE fieldname = 'timesheet_personnelroleid' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 3 WHERE fieldname = 'ordertask_id' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 4, typeofdata='D~M' WHERE fieldname = 'actual_start_date' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 5 WHERE fieldname = 'actual_start_hour' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 6 WHERE fieldname = 'actual_end_hour' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 7 WHERE fieldname = 'total_hours' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 8 WHERE fieldname = 'agentid' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 9 WHERE fieldname = 'assigned_user_id' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 10 WHERE fieldname = 'createdtime' AND tabid=$module->id");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 11 WHERE fieldname = 'modifiedtime' AND tabid=$module->id");

//Updating orders identifier. Otherwise we do you see anything in the field
$orderTaskModule = Vtiger_Module::getInstance('OrdersTask');
$fieldInstance = Vtiger_Field::getInstance('operations_task', $orderTaskModule);
if($fieldInstance){
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  summaryfield = 1 WHERE fieldid=$fieldInstance->id");
    $orderTaskModule->setEntityIdentifier($fieldInstance);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";