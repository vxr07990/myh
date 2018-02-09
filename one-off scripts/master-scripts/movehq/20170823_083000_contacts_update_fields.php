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

//OT5233	Updates to the Contacts Module (Edit/Create View)

$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Contacts');
$block = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);

$fields = [
    'leadsource',
    'title',
    'otherphone',
    'fax',
    'birthday',
    'assistant',
    'contact_id',
    'assistantphone',
    'emailoptout',
    'donotcall',
    'reference',
    'notify_owner',
	
	'otherzip',
	'otherpobox',
	'mailingzip',
	'mailingpobox',
	
	'mailingstreet',
	'otherstreet',
];

foreach ($fields as $fieldname) {
	$field = Vtiger_Field::getInstance($fieldname, $module);
	if($field){
		if($fieldname == "mailingstreet"){
			$db->pquery("UPDATE vtiger_field SET fieldlabel = 'LBL_CONTACTS_MAILINGSTREET' WHERE fieldid = ?", array($field->id));
		}else if($fieldname == "otherstreet"){
			$db->pquery("UPDATE vtiger_field SET fieldlabel = 'LBL_CONTACTS_OTHERSTREET' WHERE fieldid = ?", array($field->id));
		}else{
			$db->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldid = ?", array($field->id));
		}
		echo "Updated field: ".$fieldname. ", fieldid: ".$field->id."<br>";
	}
}

// Mailing Address 2 Field
$field1 = Vtiger_Field::getInstance('mailingstreet2', $module);
if (!$field1) {
	$field1 = new Vtiger_Field();
	$field1->label = 'LBL_CONTACTS_MAILINGSTREET2';
	$field1->name = 'mailingstreet2';
	$field1->table = 'vtiger_contactaddress';
	$field1->column = 'mailingstreet2';
	$field1->columntype = 'VARCHAR(150)';
	$field1->uitype = '1';
	$field1->typeofdata = 'V~O';

	$block->addField($field1);

	echo '<p>Added Mailing Address 2 Field to Contacts</p>';
}

// Other Address 2 Field
$field2 = Vtiger_Field::getInstance('otherstreet2', $module);
if (!$field2) {
	$field2 = new Vtiger_Field();
	$field2->label = 'LBL_CONTACTS_OTHERSTREET2';
	$field2->name = 'otherstreet2';
	$field2->table = 'vtiger_contactaddress';
	$field2->column = 'otherstreet2';
	$field2->columntype = 'VARCHAR(150)';
	$field2->uitype = '1';
	$field2->typeofdata = 'V~O';

	$block->addField($field2);

	echo '<p>Added Other Address 2 Field to Contacts</p>';
}

$seq_fields = [
	'mailingstreet',//1
	'otherstreet',//2
	'mailingstreet2',//3
	'otherstreet2',//4,etc..
	'mailingcity',
	'othercity',
	'mailingstate',
	'otherstate',
	'mailingcountry',
	'othercountry'
];

$i = 1;
foreach ($seq_fields as $fieldname) {
	$fieldseq = Vtiger_Field::getInstance($fieldname, $module);
	if($fieldseq){
		$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?", array($i,$fieldseq->id));

		echo "Updated Sequence field: ".$fieldname. ", fieldid: ".$fieldseq->id."<br>";
	}
	$i++;
}