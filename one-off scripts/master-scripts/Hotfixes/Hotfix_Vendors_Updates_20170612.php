<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 16;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
		print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

if (!function_exists(lFunctionUpdateBlockSequence)){
	function lFunctionUpdateBlockSequence($moduleInstance,$blockID,$fields){
		$db = PearDatabase::getInstance();
		$i = 1;
		foreach($fields as $field){
			if($field != ""){
				$auxfield = Vtiger_Field::getInstance($field, $moduleInstance);
				$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ? AND block = ?",array($i,$auxfield->id,$blockID));
			}
			$i++;
		}
	}
}

if (!function_exists(lFunctionUpdateSequence)){
	function lFunctionUpdateSequence($fromHere,$blockID,$newFieldID,$tableName){
		$db = PearDatabase::getInstance();

		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",array(($fromHere-1),$newFieldID));
		$result = $db->pquery("SELECT * FROM vtiger_field WHERE tablename = ? AND block = ? AND sequence > ?", array($tableName,$blockID,$fromHere));
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",array(($fromHere+1),$newFieldID));

		while ($arr = $db->fetch_array($result)){
			$fieldID = $arr['fieldid'];
			$sequence = intval($arr['sequence']) + 1;
			$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",array($sequence,$fieldID));
		}
	}
}

if (!function_exists(lFunctionGetFields)){
	function lFunctionGetFields($fields,$notThis){
		$fieldIDs = array();
		foreach($fields as $field){
			$name = $field->name;
			if(!in_array($name, $notThis)){
				$fieldIDs[$name] = array("id" => $field->id, "sequence" => $field->sequence);
			}
		}
		return $fieldIDs;
	}
}
$db = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('Vendors');

//OT4611 New Layout
$block1 = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleInstance); //Service Provider Details
$block2 = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $moduleInstance); //Contractor Information

$field881 = Vtiger_Field::getInstance('vendors_in_service_date', $moduleInstance);
$db->pquery("UPDATE vtiger_field SET typeofdata='V~M' AND block = ? WHERE fieldid = ?",array($block1->id,$field881->id)); //move from block2 to block1

$field882 = Vtiger_Field::getInstance('vendors_cancellation_date', $moduleInstance);
$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?",array($block1->id,$field882->id)); //move from block2 to block1

//Hide fields in Blocks Contractor Information & Out of Service
$blockContractors = Vtiger_Block::getInstance('LBL_VENDORS_OUTOFSERVICE', $moduleInstance); //Service Provider Details
$blockOutOfService = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $moduleInstance); //Contractor Information

$db->pquery("UPDATE vtiger_field SET presence = 1 WHERE block = ? OR block=?",array($blockContractors->id,$blockOutOfService->id)); //move from block2 to block1



$field883 = Vtiger_Field::getInstance('vendor_comission_plan', $moduleInstance);
if (!$field883){
	$field883 = new Vtiger_Field();
	$field883->label = 'LBL_VENDORS_COMISSION_PLAN';
	$field883->name = 'vendor_comission_plan';
	$field883->table = 'vtiger_vendor';
	$field883->column = 'vendor_comission_plan';
	$field883->columntype = 'VARCHAR(50)';
	$field883->uitype = 10;
	$field883->typeofdata = 'V~O';
	$field883->presence = '2';
	$block1->addField($field883);
	$field883->setRelatedModules(array('CommissionPlans'));
}

$field884 = Vtiger_Field::getInstance('fein', $moduleInstance);
$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?",array($block1->id,$field884->id)); //move from block2 to block1

$fields1 = array("vendorname","","vendor_status","type","vendors_in_service_date","vendors_cancellation_date","vendors_vendornum","vendor_comission_plan","fein");

lFunctionUpdateBlockSequence($moduleInstance,$block1->id,$fields1);

$block3 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance); //Contact Information

$fields3 = array("vendors_primcontact","website","phone","phone2","email","email2");

lFunctionUpdateBlockSequence($moduleInstance,$block3->id,$fields3);

//Adding pikclist values to class

$db->pquery("DELETE FROM vtiger_type");
$fieldClass = Vtiger_Field::getInstance('type', $moduleInstance);
$fieldClass->setPicklistValues(array('Furniture Repair','Inspection Firm','Surveyor','Terminal Services Contractor','Labor Provider','Third Party Move Services','Truck Rental','Vehicle Maintenance'));


//OT4615
$personnelModule = Vtiger_Module::getInstance('Employees');
$result = $db->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',[$moduleInstance->id, $personnelModule->id]);
if($db->num_rows($result) == 0){
	$moduleInstance->setRelatedList($personnelModule, 'Employees', array('ADD'), 'get_dependents_list');
}

//OT4616
$insuranceModule = Vtiger_Module::getInstance('Insurance');
$result = $db->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',[$moduleInstance->id, $insuranceModule->id]);
if($db->num_rows($result) == 0){
	$moduleInstance->setRelatedList($insuranceModule, 'Insurance', array('ADD'), 'get_dependents_list');
}

//OT4618
$vehiclesModule = Vtiger_Module::getInstance('Vehicles');
$result = $db->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',[$moduleInstance->id, $insuranceModule->id]);
if(!$db->num_rows($result) == 0){
	$moduleInstance->setRelatedList($vehiclesModule, 'Vehicles', array('ADD'), 'get_dependents_list');
}

$auxfield0 = Vtiger_Field::getInstance('vechiles_datequalify', $moduleInstance);

lFunctionUpdateSequence(intval($auxfield0->sequence),$block->id,$field99->id,"vtiger_vehicles");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
