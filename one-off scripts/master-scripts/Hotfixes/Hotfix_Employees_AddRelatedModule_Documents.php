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


//OT3314 - Add the related module, Documents under the Empployees module

$moduleInstance = Vtiger_Module::getInstance('Employees');
$docsInstance = Vtiger_Module::getInstance('Documents');

$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($moduleInstance->id, $docsInstance->id));

if ($result && !$db->num_rows($result)) {
	$moduleInstance->setRelatedList($docsInstance, 'Documents', Array('ADD'), 'get_attachments');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";