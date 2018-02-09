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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

$disposition_lost_reasons = [
    'Move Date has passed',
    'Capacity/Scheduling',
    'Pricing',
    'No Longer Moving',
    'Moving Themselves',
    'No Contact',
    'Past Experience',
    'National Account Move',
    'Incomplete Customer Info',
    'Out of Time',
    'Appointment Cancelled',
    'Not Serviceable',
    'Move too small',
    'Other'
];

if ($leadModule = Vtiger_Module::getInstance('Leads')) {
    if ($leadBlock = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadModule)) {
        $leadsField = Vtiger_Field::getInstance('disposition_lost_reasons', $leadModule);
        if ($leadsField) {
            echo "The disposition_lost_reasons field already exists".PHP_EOL;
        } else {
            echo "The disposition_lost_reasons field doesn't exist creating it now.".PHP_EOL;
            $leadsField             = new Vtiger_Field();
            $leadsField->label      = 'LBL_LEADS_DISPOSITIONLOSTREASONS';
            $leadsField->name       = 'disposition_lost_reasons';
            $leadsField->table      = 'vtiger_leaddetails';
            $leadsField->column     = 'disposition_lost_reasons';
            $leadsField->columntype = 'VARCHAR(255)';
            $leadsField->uitype     = 16;
            $leadsField->typeofdata = 'V~O';
            $leadBlock->addField($leadsField);
        }
    }
}

if ($opportunityModule = Vtiger_Module::getInstance('Leads')) {
    if ($opportunityBlock = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $opportunityModule)) {
        $opportunityField = Vtiger_Field::getInstance('disposition_lost_reasons', $opportunityModule);
        if ($opportunityField) {
            echo "The disposition_lost_reasons field already exists".PHP_EOL;
        } else {
            echo "The disposition_lost_reasons field doesn't exist creating it now.".PHP_EOL;
            $opportunityField             = new Vtiger_Field();
            $opportunityField->label      = 'LBL_OPPORTUNITY_OPPORTUNITYDETAILDISPOSITIONLOST';
            $opportunityField->name       = 'disposition_lost_reasons';
            $opportunityField->table      = 'vtiger_potential';
            $opportunityField->column     = 'disposition_lost_reasons';
            $opportunityField->columntype = 'VARCHAR(255)';
            $opportunityField->uitype     = 16;
            $opportunityField->typeofdata = 'V~O';
            $opportunityBlock->addField($opportunityField);
        }
    }
}
if (
    $leadsField ||
    $opportunityField
) {

    if (Vtiger_Utils::CheckTable('vtiger_disposition_lost_reasons')) {
        $db = &PearDatabase::getInstance();
        $stmt = 'TRUNCATE TABLE `vtiger_disposition_lost_reasons`';
        $db->query($stmt);
    }

    if (
        $leadsField &&
        method_exists($leadsField, 'setPicklistValues')
    ) {
        $leadsField->setPicklistValues($disposition_lost_reasons);
    } elseif (
        $opportunityField &&
        method_exists($opportunityField, 'setPicklistValues')
    ) {
        $opportunityField->setPicklistValues($disposition_lost_reasons);
    } else {
        print "FAILED TO UPDATE disposition_lost_reasons picklist\n";
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";