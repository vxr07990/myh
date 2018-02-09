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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/13/2016
 * Time: 10:51 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$Vtiger_Utils_Log = true;

$newRoles =[
    'Installer ', 'Lead Installer',
    'Mover',        'Packer',
    'Supervisor',       'Project Manager',
    'Computer Technician',      'Unpacker',
    'Warehouse Local',      'Forklift Operator',
    'Warehouse Supervisor',         'Driver - A',
    'Driver - B',       'Driver - Non CDL',
    'Inventory Technician',     'AutoCAD Operator',
    'Project Coordinator',      'Senior Project Manager',
    'Space Planner',        'Supervisor - Inventory',
    'Systems Administrator - Inventory',        'Local Dispatcher',
    'Operations Manager',       'Administrative Assistant',
    'Contractor Relations Manager',         'Operations Manager - Assistant',
    'OA/DA Coordinator',        'Surveyor',
    'Consumer Sales',       'Consumer Customer Service Coordinator',
    'Commercial Customer Service Coordinator',      'Branch Administrator',
    'General Manager',      'Assistant General Manager',
    'Customer Service Coordinator',         'Customer Service Coordinator - Assistant',
    'Customer Service Supervisor',      'Regional Client Service Manager',
    'Claims Adjuster',      'Claims Supervisor',
    'Claims Manager',       'Billing Support',
    'Accounting Specialist',    'Accounting Supervisor',
    'Accounting Manager',       'Director of Billing',
    'Claims Administration',        'Claims Adjusting',
    'Claims Management',        'Regional Planner ',
    'Planning Manager',         'Planning Coordinator',
    'ATS Manager',      'ATS Coordinator',
    'CRM Manager',

    'Split Booking',        'Collecting',
    'Destination',      'Unpacking',
    'Extra Delivery',       'Containerized - DEST',
    'Intermodal-DEST',      'ASO',
    'ASO - 2nd',        'ASO - 3rd',
    'Coordinating',     'Hauling',
    'Split Hauling',        '2nd Split Hauler',
    '3rd Split Hauler',     'Invoicing Billing',
    'Origin',       'Containerized - ORIG',
    'APU',      'Extra Pickup',
    'Radial Dispatch Agent',        'Survey Agent',
    'Warehousing',      'Carrier',
    'Installer',        'Accounting',
    'APU Driver',       'Commercial Svc Coordinator',
    'Helper',       'Authorized Move Coordinator',
    'Admin Support',        'ASO Driver',
    'Shuttle',      'Estimator',
    'GMII Forwarder',       'Storage Pickup Driver',
];

$module = Vtiger_Module::getInstance('Employees');

if (!$module) {
    return;
}

foreach (['employee_prole', 'employee_srole', 'contractor_prole'] as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if (!$field) {
        continue;
    }
    $db = PearDatabase::getInstance();
    $db->pquery('TRUNCATE TABLE `vtiger_' . $fieldName . '`');
    $field->setPicklistValues($newRoles);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";