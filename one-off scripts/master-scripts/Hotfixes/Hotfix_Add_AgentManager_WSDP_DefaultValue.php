<?php

//Hotfix_Add_AgentManager_WSDP_DefaultValue

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();

$module     = Vtiger_Module::getInstance("AgentManager");
$field = Vtiger_Field::getInstance('payroll_week_start_date', $module);
if($field){
	$db->pquery("UPDATE vtiger_field SET defaultvalue = 'Sunday' WHERE fieldid = ?", array($field->id));
	echo 'Field (payroll_week_start_date) updated!';
}else{
	echo 'The field (payroll_week_start_date) doesn`t exists!';
}