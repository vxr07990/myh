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

error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global  $adb;

$module4 = Vtiger_Module::getInstance('TariffServices');
$filterInstance = Vtiger_Filter::getInstance('All',$module4);
if(!$filterInstance){
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module4->addFilter($filter1);
    $field18 = Vtiger_Field::getInstance('service_name', $module4);
    $field19 = Vtiger_Field::getInstance('tariff_section', $module4);
    $field20 = Vtiger_Field::getInstance('effective_date', $module4);
    $field21 = Vtiger_Field::getInstance('related_tariff', $module4);
    $field22 = Vtiger_Field::getInstance('rate_type', $module4);

    $filter1->addField($field18)->addField($field19, 1)->addField($field20, 2)->addField($field21, 3)->addField($field22, 4);
    $module4->setDefaultSharing();
    $module4->initWebservice();
}

//Re-order Tariff Service Information block
$tabId = getTabid('TariffServices');
$adb->pquery("UPDATE vtiger_field SET sequence = 1 WHERE fieldname = 'service_name' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'tariff_section' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'rate_type' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'applicability' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'is_required' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'tariffservices_discountable' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 7 WHERE fieldname = 'tariffservices_assigntomodule' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 8 WHERE fieldname = 'tariffservices_assigntorecord' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 9 WHERE fieldname = 'related_itemcodes' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 10 WHERE fieldname = 'agentid' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 11 WHERE fieldname = 'assigned_user_id' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 12 WHERE fieldname = 'effective_date' AND tabid = ?",[$tabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 13 WHERE fieldname = 'related_tariff' AND tabid = ?",[$tabId]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";