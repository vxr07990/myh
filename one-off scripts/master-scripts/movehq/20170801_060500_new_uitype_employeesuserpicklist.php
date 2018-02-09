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

//OT5215	Personnel - update UIType for "Related MoveHQ User"

echo 'Creating new Employees User Picklist uitype<br>';

$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT * FROM vtiger_ws_fieldtype WHERE uitype = 5353", array());
if($db->num_rows($result) < 1){
	$aux = $db->pquery("INSERT INTO `vtiger_ws_fieldtype`(`uitype`, `fieldtype`) VALUES (5353,'employeeuserpicklist')", array());
	echo 'Finished creating new Employees User Picklist uitype<br>';
}else{
	echo 'ERROR new Employees User Picklist already exists!<br>';
}

$moduleInstance = Vtiger_Module::getInstance('Employees');
$field = Vtiger_Field::getInstance('userid', $moduleInstance);
if($field){
	$update = $db->pquery("UPDATE vtiger_field SET uitype = 5353 WHERE fieldid = ?", array($field->id));
	
	echo 'userid from Employees module uitype updated!<br>';
}
