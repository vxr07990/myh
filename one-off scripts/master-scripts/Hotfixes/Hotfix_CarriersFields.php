<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
		print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

if (!function_exists(localFunctionUpdateSequence)){
	function localFunctionUpdateSequence($fromHere,$blockID,$newFieldID){
		$db = PearDatabase::getInstance();
		$newFieldSeq = intval($fromHere) + 1;
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",array($newFieldSeq,$newFieldID));
		$result = $db->pquery("SELECT * FROM vtiger_field WHERE tablename = 'vtiger_carriers' AND block = ? AND sequence > ?", array($blockID,$fromHere));

		while ($arr = $db->fetch_array($result)){
			$fieldID = $arr['fieldid'];
			$sequence = intval($arr['sequence']) + 1;
			$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?",array($sequence,$fieldID));
		}
	}
}

$moduleInstance = Vtiger_Module::getInstance('Carriers');
$fields = $moduleInstance->getFields();
$notThis = array("createdtime","modifiedtime","modifiedby","assigned_user_id");
$fieldIDs = array();
foreach($fields as $field){
	$name = $field->name;
	if(!in_array($name, $notThis)){
		$fieldIDs[$name] = array("id" => $field->id, "sequence" => $field->sequence);
	}
}

$block= Vtiger_Block::getInstance('LBL_CARRIER_INFORMATION', $moduleInstance);

$field1 = Vtiger_Field::getInstance('duns_number', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_DUNS_NUMBER';
    $field1->name = 'duns_number';
    $field1->table = 'vtiger_carriers';
    $field1->column = 'duns_number';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 7;
    $field1->typeofdata = 'I~O';
    $block->addField($field1);
}

localFunctionUpdateSequence($fieldIDs['carrier_status']['sequence'],$block->id,$field1->id);

$field2 = Vtiger_Field::getInstance('federal_id_number', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_FEDERAL_ID_NUMBER';
    $field2->name = 'federal_id_number';
    $field2->table = 'vtiger_carriers';
    $field2->column = 'federal_id_number';
    $field2->columntype = 'VARCHAR(30)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $block->addField($field2);
}
localFunctionUpdateSequence($fieldIDs['agentid']['sequence'],$block->id,$field2->id);

$field99 = Vtiger_Field::getInstance('primary_contact', $moduleInstance);
if (!$field99){
	$field99 = new Vtiger_Field();
	$field99->label = 'LBL_PRIMARY_CONTACT';
	$field99->name = 'primary_contact';
	$field99->table = 'vtiger_carriers';
	$field99->column = 'primary_contact';
	$field99->columntype = 'VARCHAR(50)';
	$field99->uitype = 10;
	$field99->typeofdata = 'V~O';
	$block->addField($field99);
	$field99->setRelatedModules(array('Contacts'));
}

$block2 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $moduleInstance);
if (!$block2) {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_ADDRESS_INFORMATION';
    $moduleInstance->addBlock($block2);
}

$field7 = Vtiger_Field::getInstance('address1', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CARRIERS_ADDRESS1';
    $field7->name = 'address1';
    $field7->table = 'vtiger_carriers';
    $field7->column = 'address1';
    $field7->columntype = 'VARCHAR(50)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';

    $block2->addField($field7);
}
$field8 = Vtiger_Field::getInstance('address2', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CARRIERS_ADDRESS2';
    $field8->name = 'address2';
    $field8->table = 'vtiger_carriers';
    $field8->column = 'address2';
    $field8->columntype = 'VARCHAR(50)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    
    $block2->addField($field8);
}
$field9 = Vtiger_Field::getInstance('city', $moduleInstance);
if (!$field9) {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CARRIERS_CITY';
    $field9->name = 'city';
    $field9->table = 'vtiger_carriers';
    $field9->column = 'city';
    $field9->columntype = 'VARCHAR(50)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';

    $block2->addField($field9);
}
$field10 = Vtiger_Field::getInstance('state', $moduleInstance);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CARRIERS_STATE';
    $field10->name = 'state';
    $field10->table = 'vtiger_carriers';
    $field10->column = 'state';
    $field10->columntype = 'VARCHAR(50)';
    $field10->uitype = 1;
    $field10->typeofdata = 'V~O';

    $block2->addField($field10);
}
$field11 = Vtiger_Field::getInstance('zip', $moduleInstance);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CARRIERS_ZIP';
    $field11->name = 'zip';
    $field11->table = 'vtiger_carriers';
    $field11->column = 'zip';
    $field11->columntype = 'INT(10)';
    $field11->uitype = 7;
    $field11->typeofdata = 'V~O';

    $block2->addField($field11);
}
$field12 = Vtiger_Field::getInstance('country', $moduleInstance);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CARRIERS_COUNTRY';
    $field12->name = 'country';
    $field12->table = 'vtiger_carriers';
    $field12->column = 'country';
    $field12->columntype = 'VARCHAR(50)';
    $field12->uitype = 1;
    $field12->typeofdata = 'V~O';

    $block2->addField($field12);
}

$contactsModule = Vtiger_Module::getInstance('Contacts');
$moduleInstance->unsetRelatedList($contactsModule);
$moduleInstance->setRelatedList($contactsModule, 'Contacts', array('ADD', 'SELECT'));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";