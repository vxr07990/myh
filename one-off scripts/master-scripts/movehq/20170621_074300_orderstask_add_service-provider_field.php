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

//OT4621 - OrdersTask Module - Add "Service Provider" field

if (!function_exists(lFunctionUpdateBlockSequence)){
	function lFunctionUpdateBlockSequence($moduleInstance,$blockID,$fields){
		$db = PearDatabase::getInstance();
		$i = 1;
		foreach($fields as $field){
			if($field != ""){
				$auxfield = Vtiger_Field::getInstance($field, $moduleInstance);
				if($auxfield){
					$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ? AND block = ?",array($i,$auxfield->id,$blockID));
				}
			}
			$i++;
		}
	}
}

//OT4621
$orderstaskModule = Vtiger_Module::getInstance('OrdersTask');
$blockOT = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $orderstaskModule);

$field1 = Vtiger_Field::getInstance('assigned_vendor', $orderstaskModule);
if($field1){
	if($field1->presence != 2){
		$db = PearDatabase::getInstance();
		$db->pquery("UPDATE vtiger_field SET presence = 2 WHERE fieldid = ?",array($field1->id));
	}
	$fields = array("dispatch_status","disp_assigneddate","disp_assignedstart","disp_actualend","disp_actualdate","disp_actualhours","assigned_employee","assigned_vehicles","check_call","assigned_vendor");
	lFunctionUpdateBlockSequence($orderstaskModule,$blockOT->id,$fields);

	//Adding the field to Local Dispatch Day Page (this is the default filter for local dispatch available to all users)

	$LDDayPage =  Vtiger_Filter::getInstance('Local Dispatch Day Page', $orderstaskModule, true);
	$LDDayPage->addField($field1, 17);
}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
