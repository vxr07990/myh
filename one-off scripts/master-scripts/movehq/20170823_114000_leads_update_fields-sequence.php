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

//OT5269 Leads - Modify Lead Information Block

$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Leads');

$fields = [
    'business_line',
	'commodities',
	'agentid',
	'assigned_user_id',
	'leadstatus',
	'leadsource',
	'reason_cancelled',
	'related_account'
];

$i = 1;
foreach ($fields as $fieldname) {
	$field = Vtiger_Field::getInstance($fieldname, $module);
	
	if($field){
		if($fieldname == "related_account"){
			$db->pquery("UPDATE vtiger_field SET presence = 1 , tabid = ? WHERE fieldid = ?", array($module->id,$field->id));
		}else{
			$db->pquery("UPDATE vtiger_field SET sequence = ? , tabid = ? WHERE fieldid = ?", array($i, $module->id,$field->id));
			$i++;
		}
		echo "Updated field: ".$fieldname. ", fieldid: ".$field->id."<br>";
	}
}