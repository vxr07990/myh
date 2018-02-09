<?php

if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//OT5307 - Modify Local Operations Task

$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('OrdersTask');

$block_operative_task_info = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $module);

$field = Vtiger_Field::getInstance('commodities', $module);
if (!$field) {
	$field = new Vtiger_Field();
	$field->label = 'LBL_OPERATIVE_TASK_COMMODITIES';
	$field->name = 'commodities';
	$field->table = 'vtiger_orderstask';
	$field->column = 'commodities';
	$field->columntype = 'VARCHAR(255)';
	$field->uitype = 33;
	$field->typeofdata = 'V~M';
	$field->summaryfield = 1;
	
	$block_operative_task_info->addField($field);

}else{
	$db->pquery("UPDATE vtiger_field SET presence = 2 WHERE tabid = ? AND fieldname = ?", array($module->id,'commodities'));
}

$fields = [
	'ordersid',
    'business_line',
	'commodities',
	'operations_task',
	'date_spread',
	'service_date_from',
	'service_date_to',
	'pref_date_service',
	'task_start',
	'calendarcode',
	'participating_agent',
	'notes_to_dispatcher',
	'service_provider_notes',
	'agentid',
	'smownerid',
	'assigned_user_id',
	'estimated_hours',
];

$i = 1;
foreach ($fields as $fieldname) {
	$afield = Vtiger_Field::getInstance($fieldname, $module);
	if($afield){
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE tabid = ? AND fieldid = ?", array($i, $module->id,$afield->id));
		echo "Updated field: ".$fieldname. ", fieldid: ".$afield->id."<br>";
	}
	$i++;
	
	if($fieldname == "notes_to_dispatcher" && $afield){
		$db->pquery("UPDATE vtiger_field SET uitype = 19 WHERE tabid = ? AND fieldid = ?", array($module->id,$afield->id));
	}
}

$removable_fields = [
	'multiservice_date',
	'include_saturday',
	'include_sunday',
	'reason_cancelled',
	'cancel_task',
	'cod_amount',
	'disp_actualdate',
	'specialrequest'
];

foreach ($removable_fields as $fieldname) {
	$afield = Vtiger_Field::getInstance($fieldname, $module);
	if($afield){
		$db->pquery("UPDATE vtiger_field SET presence = 1 WHERE tabid = ? AND fieldid = ?", array($module->id,$afield->id));
		echo "Updated field: ".$fieldname. ", fieldid: ".$afield->id."<br>";
	}
}

$block_dispatch_updates = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $module);

$fields2 = [
	'dispatch_status',
    'disp_assigneddate',
	'disp_assignedstart',
	'disp_actualend',
	'assigned_employee',
	'assigned_vehicles',
	'assigned_vendor',
	'disp_actualhours',
	'actual_of_crew',
	'actual_of_vehicles',
	'check_call',
];

$i = 1;
foreach ($fields2 as $fieldname) {
	$afield = Vtiger_Field::getInstance($fieldname, $module);
	if($afield){
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE tabid = ? AND  fieldid = ?", array($i, $module->id,$afield->id));
		echo "Updated field: ".$fieldname. ", fieldid: ".$afield->id."<br>";
	}
	$i++;
}