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
global $adb;


$picklistvalues = array(
    'HHG - Interstate',
    'HHG - Intrastate',
    'HHG - Local',
    'HHG - International',
    'Electronics - Interstate',
    'Electronics - Intrastate',
    'Electronics - Local',
    'Electronics - International',
    'Display & Exhibits - Interstate',
    'Display & Exhibits - Intrastate',
    'Display & Exhibits - Local',
    'Display & Exhibits - International',
    'General Commodities - Interstate',
    'General Commodities - Intrastate',
    'General Commodities - Local',
    'General Commodities - International',
    'Auto - Interstate',
    'Auto - Intrastate',
    'Auto - Local',
    'Auto - International',
    'Commercial - Interstate',
    'Commercial - Intrastate',
    'Commercial - Local',
    'Commercial - International',
);

$moduleInstance = Vtiger_Module::getInstance('CommissionPlansFilter');
$field = Vtiger_Field::getInstance('business_line', $moduleInstance);
$blockInstance = Vtiger_Block::getInstance('LBL_COMMISSIONPLANGROUP', $moduleInstance);
$newField = Vtiger_Field::getInstance('business_line_complansfilter', $moduleInstance);
if ($field) {
    $field->delete();
}

if ($newField) {
    echo "<br> The business_line_complansfilter field already exists <br>";
} else {
    $newField = new Vtiger_Field();
    $newField->label = 'LBL_BUSINESSLINE';
    $newField->name = 'business_line_complansfilter';
    $newField->table = 'vtiger_commissionplansfilter';
    $newField->column = 'business_line_complansfilter';
    $newField->columntype = 'VARCHAR(255)';
    $newField->uitype = 3333;
    $newField->sequence = 3;
    $newField->typeofdata = 'V~M';
    $newField->defaultvalue = 'All';
    $newField->setPicklistValues($picklistvalues);
    $blockInstance->addField($newField);
}

// Add custom filter for CommissionPlansFilter module
$field1 = Vtiger_Field::getInstance('commissionplan', $moduleInstance);
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
$field4 = Vtiger_Field::getInstance('billing_type', $moduleInstance);
$field5 = Vtiger_Field::getInstance('authority', $moduleInstance);
$field6 = Vtiger_Field::getInstance('related_tariff', $moduleInstance);
$field7 = Vtiger_Field::getInstance('related_contract', $moduleInstance);

$filter1=Vtiger_Filter::getInstance('All',$moduleInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1)->addField($newField, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6);
}

// Add custom filter for CommissionPlans module
$commPlansInstance = Vtiger_Module::getInstance('CommissionPlans');
$field1 = Vtiger_Field::getInstance('name', $commPlansInstance);
$field2 = Vtiger_Field::getInstance('description', $commPlansInstance);
$field3 = Vtiger_Field::getInstance('agentid', $commPlansInstance);
$field4 = Vtiger_Field::getInstance('commissionplans_status', $commPlansInstance);

$filter1=Vtiger_Filter::getInstance('All',$commPlansInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $commPlansInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);
}




// Update Revenue Grouping Item AgentId values
$adb->pquery("
    UPDATE vtiger_revenuegrouping ,vtiger_revenuegroupingitem, vtiger_crmentity crmgropitem, vtiger_crmentity crmgroup
    SET crmgropitem.agentid=crmgroup.agentid
    WHERE vtiger_revenuegrouping.revenuegroupingid=vtiger_revenuegroupingitem.revenuegroupingitem_relcrmid
    AND crmgroup.crmid=vtiger_revenuegrouping.revenuegroupingid
    AND crmgropitem.crmid=vtiger_revenuegroupingitem.revenuegroupingitemid
    AND ISNULL(crmgropitem.agentid)
");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";