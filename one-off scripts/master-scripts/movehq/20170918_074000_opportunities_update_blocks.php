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


//OT5273 Opportunities - Modify Opportunity Information and Record Update Information Blocks

$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('Opportunities');
$infoBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION',$moduleInstance);

//weird Created By field (hide)
$field = Vtiger_Field::getInstance('created_user_id',$moduleInstance);
if($field){
	$db->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldid = ?", array($field->id));
}

//move Assigned To to Opportunities Information Block
$field1 = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
if($field1){
	$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", array($infoBlock->id,$field1->id));
}

//Just in case, update field sequence on both blocks	

$info_fields = [
	'contact_id',
	'opportunitystatus',
	'potentialname',
	'opportunityreason',
	'business_line',
	'commodities',
	'billing_type',
	'amount',
	'leadsource',
	'closingdate',
	'related_to',
	'oppotunitiescontract',
	'is_competitive',
	'agentid',
	'assigned_user_id'
];

$i = 1;
foreach($info_fields as $fieldName){
	$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
	if($field){
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?", array($i,$field->id));
		$i++;
	}
}

$record_update_fields = [
	'createdtime',
	'modifiedtime',
	'createdby'
];

$i = 1;
foreach($record_update_fields as $fieldName){
	$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
	if($field){
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?", array($i,$field->id));
		$i++;
	}
}