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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Accounts');

if (!$module) {
    return;
}

$db = PearDatabase::getInstance();
$field = Vtiger_Field::getInstance('invoice_pkg_format', $module);

if ($field) {
    $newValues = [
        'COD HHGs standard',
        'Corporate HHGs standard',
        'Corporate HHGs extended or audited account',
        'MMI HHGs standard',
        'MMI HHGs standard intrastate or local',
        'JLL workspace standard',
        'Military HHGs standard (entered in Syncada/powertrack for payment)',
        'Military HHGS interline',
        'GSA HHGs standard',
        'GSA HHGs WHR',
        'Cartus HHGs standard',
        'Graebel Movers International HHGs standard',
        'HHGs standard – SIRVA',
        'Cummins HHGs standard',
        'Relocation Management Worldwide HHGs standard',
        'Altair  HHGs standard',
        'JP Morgan Chase',
        'Chevron',
        'Chevron  intrastate or local',
        'Siemens Shared Services',
        'Workspace - No Backup',
        'Workspace - Project'
    ];
    echo "Dumping old stuff making it new<br />";
    $db->pquery('TRUNCATE TABLE `vtiger_invoice_pkg_format`');
    // same picklist table is used in contracts, estimates, actuals, and orders, so this should update them all
    $field->setPicklistValues($newValues);
}

$field = Vtiger_Field::getInstance('invoice_format', $module);
if ($field) {
    $newValues = [
        'Bottom Line Invoice',
        'Gross and Net',
        'No discount',
        'Performance Based',
        'Gross Only w/o Remarks',
        'Gross Net and Gross Only',
        'GRSW Invoice',
        'Gross Only Invoice',
        'Permanent Storage Invoice',
        'Invoice with Payment',
        'Project One Line Invoice',
        'Event Item Invoice',
        'Event Total Invoice',
        'JLL Invoice',
        'CBRE invoice',
        'State Farm Invoice',
        'Asurion Invoice',
    ];
    echo "Dumping old stuff making it new<br />";
    $db->pquery('TRUNCATE TABLE `vtiger_invoice_format`');
    // same picklist table is used in contracts, estimates, actuals, and orders, so this should update them all
    $field->setPicklistValues($newValues);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";