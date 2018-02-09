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

//OT5272	Contact Mouse Hover popup - update information being shown

$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Contacts');

$fields = [
    'contact_type',
	'firstname',
	'lastname',
	'phone',
	'mobile',
	'homephone',
	'email',
	'secondaryemail'
];

$db->pquery("UPDATE vtiger_field SET summaryfield = 0 WHERE tabid = ?", array($module->id));

foreach ($fields as $fieldname) {
	$field = Vtiger_Field::getInstance($fieldname, $module);
	
	if($field){
		$db->pquery("UPDATE vtiger_field SET summaryfield = 1 WHERE fieldid = ?", array($field->id));
		echo "Updated field: ".$fieldname. ", fieldid: ".$field->id."<br>";
	}
}