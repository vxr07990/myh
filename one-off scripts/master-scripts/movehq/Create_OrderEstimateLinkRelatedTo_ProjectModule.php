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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;

$ProjectModuleInstance = Vtiger_Module::getInstance('Project');

$fieldName = "order_item";
$field = Vtiger_Field::getInstance($fieldName, $ProjectModuleInstance);
if ($field){
    $field->presence = '1';
    $field->save();
    /*$field->delete();
    $sql = "ALTER TABLE `vtiger_project` DROP COLUMN `vtiger_project`.`order_item`;";
    $adb->pquery($sql);*/
}


$fieldName = "escrow_item";
$field = Vtiger_Field::getInstance($fieldName, $ProjectModuleInstance);
if ($field){
    $field->presence = '1';
    $field->save();
    /*$field->delete();
    $sql = "ALTER TABLE `vtiger_project` DROP COLUMN `vtiger_project`.`escrow_item`;";
    $adb->pquery($sql);*/
}

if ($ProjectModuleInstance){

    $OrdersModuleInstance = Vtiger_Module::getInstance('Orders');
    if ($OrdersModuleInstance) {
        $ProjectModuleInstance->setRelatedList($OrdersModuleInstance, 'Orders');
    }

    $EstimatesModuleInstance = Vtiger_Module::getInstance('Estimates');
    if ($EstimatesModuleInstance) {
        $ProjectModuleInstance->setRelatedList($EstimatesModuleInstance, 'Estimates');
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";