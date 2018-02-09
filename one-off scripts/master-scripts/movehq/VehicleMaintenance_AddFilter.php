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

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$vehicleMaintenanceModule = Vtiger_Module::getInstance('VehicleMaintenance');
if($vehicleMaintenanceModule) {
    $idField           = Vtiger_Field::getInstance('maintenance_number', $vehicleMaintenanceModule);
    $dateField         = Vtiger_Field::getInstance('maintenance_date', $vehicleMaintenanceModule);
    $reasonField       = Vtiger_Field::getInstance('maintenance_reason', $vehicleMaintenanceModule);
    $odometerField     = Vtiger_Field::getInstance('maintenance_odometer', $vehicleMaintenanceModule);
    $ownerField        = Vtiger_Field::getInstance('agentid', $vehicleMaintenanceModule);
    $assignedUserField = Vtiger_Field::getInstance('assigned_user_id', $vehicleMaintenanceModule);
}

if($idField && $dateField && $reasonField && $odometerField && $ownerField && $assignedUserField) {
    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $vehicleMaintenanceModule->addFilter($filter1);

    $filter1->addField($idField)->addField($dateField, 1)->addField($reasonField, 2)->addField($odometerField, 3)->addField($ownerField, 4)->addField($assignedUserField, 5);
} else {
    global $scriptVersionsToUpdate;
    unset($scriptVersionsToUpdate[__FILE__]);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
