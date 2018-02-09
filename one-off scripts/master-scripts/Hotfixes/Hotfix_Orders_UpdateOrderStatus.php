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


$db = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('Orders');
$newValues = ['Ready to Invoice', 'Pending Info', 'Complete'];

if ($moduleInstance) {
	$field = Vtiger_Field::getInstance('ordersstatus', $moduleInstance);
	if ($field) {
		$oldValues = [];
		$result = $db->query('SELECT ordersstatus FROM vtiger_ordersstatus WHERE 1');
		if ($result && $db->num_rows($result) > 0) {
			while ($row = $result->fetchRow()) {
				$oldValues[] = $row['ordersstatus'];
			}
		}

		$arr = array_diff($newValues, $oldValues);
		$field->setPicklistValues($arr);
		
		
	}
}
echo 'OK<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";