<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT3997 - Picklist Customizer Updates
// OT4809 - Update modules Picklists with the new UI Type

echo 'Start: Updating uitype of Picklist fields to 1500<br />\n';
//update uitype of field to 1500
$allModulesToChangeUIType = [
    'Leads' => [
        'leadsource',
        'reason_cancelled',
        'origin_description',
        'destination_description'        
    ],
    'Opportunities' => [
        'leadsource',
        'opportunityreason',
        'origin_description',
        'destination_description'
    ],
    'Orders' => [
        'order_reason',
        'origin_description',
        'destination_description'
    ],
    'ExtraStops' => [
        'extrastops_description'
    ],
    'Documents' => [
        'invoice_document_type'
    ],
    'Contacts' => [
        'leadsource'
    ],
    'OrdersTask' => [
        'operations_task',
        'task_start'
    ],
    'Accounts' => [
        'leadsource',
        'industry',
        'account_type',
        'rating',
        'gvl_account_type'
    ],
    'TimeOff' => [
        'timeoff_reason'
    ],
    'DrivingViolation' => [
        'drivingviolation_convtype',
        'drivingviolation_vehicletype'
    ],
    'DriverQualification' => [
        'drugprogramtype',
        'drugscreentype'
    ],
    'OutOfService' => [
        'outofservice_type',
        'outofservice_typeofreason'
    ],
    'Vehicles' => [
        'vehicle_type'
    ],
    'VehicleMaintenance' => [
        'maintenance_reason'
    ],
    'VehicleTerminations' => [
        'termination_reason'
    ],
    'VehicleOutofService' => [
        'outofservice_reason'
    ]
];

foreach ($allModulesToChangeUIType as $moduleName => $fieldNames) {
    $moduleId = getTabId($moduleName);
    if(is_numeric($moduleId)){
        echo "Module $moduleName: <br>";
        foreach ($fieldNames as $fieldName) {
            $sql = "UPDATE vtiger_field SET uitype = '1500' WHERE columnname = '$fieldName' AND tabid = $moduleId LIMIT 1";
            $result = $db->pquery($sql);
            echo "<li>$fieldName field updated<br>";
        }
    } else {
        echo "Module $moduleName don't exist<br>\n";
    }
}
echo 'Finish: Updating uitype of Picklist field to 1500<br />\n';
