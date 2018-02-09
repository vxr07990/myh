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

$compensationGroupModule = Vtiger_Module::getInstance('AgentCompensationGroup');
$businessLineField       = Vtiger_Field::getInstance('agentcompgr_businessline', $compensationGroupModule);

if($businessLineField) {
    if(!$db) {
        $db = PearDatabase::getInstance();
    }
    $db->query("TRUNCATE TABLE `vtiger_agentcompgr_businessline`");
    $businessLineField->setPicklistValues(['Interstate', 'Intrastate', 'Local', 'International']);
}

$compensationGroupBlock = Vtiger_Block::getInstance('LBL_AGENTCOMPENSATION_GROUP', $compensationGroupModule);
$commoditiesField = Vtiger_Field::getInstance('commodities', $compensationGroupModule);

if(!$commoditiesField) {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_COMMODITIES';
        $field->name       = 'commodities';
        $field->table      = 'vtiger_agentcompensationgroup';
        $field->column     = 'commodities';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = 3333;
        $field->typeofdata = 'V~M';
        $field->sequence   = '4';
        $compensationGroupBlock->addField($field);
}
