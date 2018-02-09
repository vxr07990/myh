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

//OT4617 - Vehicles Module - Add "Service Provider" look up field - resequence fields
//OT4618 - Vendors Module - Add Vehicles as Related Module

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

//OT4617
$vehiclesModule = Vtiger_Module::getInstance('Vehicles');
$block= Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $vehiclesModule);

$field1 = Vtiger_Field::getInstance('vechiles_unit', $vehiclesModule);

if($field1){
	$db->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldid = ?",array($field1->id));
}

$field2 = Vtiger_Field::getInstance('vehicle_type', $vehiclesModule);
if($field2){
	$db->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldid = ?",array($field2->id));
}

//OT4618
$field3 = Vtiger_Field::getInstance('vehicles_vendorid', $vehiclesModule);
if (!$field3){
	$field3 = new Vtiger_Field();
	$field3->label = 'LBL_VEHICLES_SERVICE_PROVIDER';
	$field3->name = 'vehicles_vendorid';
	$field3->table = 'vtiger_vehicles';
	$field3->column = 'vehicles_vendorid';
	$field3->columntype = 'VARCHAR(50)';
	$field3->uitype = 10;
	$field3->typeofdata = 'V~O';
	$block->addField($field3);
	$field3->setRelatedModules(array('Vendors'));
}

//Re-Sequence Fields

$fields = array("vechiles_unit","","vehicle_status","vehicle_type","agentid","vehicles_reg_date","vechiles_datequalify","vehicles_agent_no","vehicles_vendorid","vehicles_availlocal","vehicles_availinter","smownerid","vechiles_no");
lFunctionUpdateBlockSequence($moduleInstance,$block->id,$fields);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";