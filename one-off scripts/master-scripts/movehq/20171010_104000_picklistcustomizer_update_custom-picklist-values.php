<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$valueMap = [
    'leadsource' => [
        'All My Sons',
        'National Account Referral',
        'Network Group',
        'Other',
        'Previous Customer',
        'Realtor - Local',
        'Retirement Community',
        'Trade Conference',
        'Walker Sands',
        'WPS Customer',
        'NASMM',
        'Local Mover',
        'Angie\'s List',
        'BBB',
        'Beneplace',
        'Branch Call-In',
        'Chamber of Commerce',
        'Corporate Affinity',
        'Direct Mail',
        'Facebook',
        'Friends or Relatives',
        'Graebelmoving.com',
        'Yelp'
    ],
    'operations_task' => [
        'Storage In',
        'Storage Out',
        'Attempted Pick (Operations Only)',
        'Auto Pickup/Delivery',
        'Auxiliary Service',
        'Carton Delivery',
        'Commercial Move',
        'Container Delivery',
        'Container Load',
        'Day Certain Load',
        'Debris Pickup',
        'Delivery',
        'Destination Services - Airbox',
        'Destination Services - Liftvan',
        'Destination Services - Sea Container',
        'Disposal',
        'Driver Help Loading',
        'Driver Help Unloading',
        'Driver Pack',
        'Equipment/Material Delivery',
        'Equipment/Material Pickup',
        'Flatbed Service',
        'G\'11/APU',
        'Install',
        'International Delivery/Unpack',
        'International Load',
        'International Pack',
        'International Pack/Load',
        'Interstate Load',
        'Load',
        'Local Move',
        'Non-Revenue',
        'Origin Services - Airbox',
        'Origin Services - Liftvan',
        'Origin Services - Sea Container',
        'Pack',
        'Pack & APU - for Billing Type of DPS (military only)',
        'Pack & Crate - for Billing Type of DPS (military only)',
        'Pack and Load',
        'Pack and Shuttle',
        'Project Manager',
        'QC Visit',
        'Same Day Load/Deliver',
        'Same Day Pack/Load',
        'Same Day Pack/Load/Deliver',
        'Shuttle Destination',
        'Small Shipments/One Day Load',
        'Storage Access',
        'Storage Delivery',
        'Storage In/Warehouse Handling',
        'Storage Out/Warehouse Handling',
        'Storage Pickup',
        'Spec Comm Load',
        'Spec Comm Unload',
        'Unpack'
    ],
    'opportunityreason' => [
        'value1',
        'value2'
    ],
    'ordersstatus' => [
        'Initial Order',
        'Booked',
        'On Hold, Will Advise',
        'Cancelled',
        'Pending Estimate',
        'Estimate Received',
        'Registered',
        'Residence to Dock',
        'Loaded & On Origin Dock',
        'Loaded & In Transit',
        'In Transit',
        'Storage @ Origin',
        'Delivered to Dock',
        'Delivered to Residence',
        'Customs'
    ],
    'order_reason' => [
        'Move date has passed',
        'Capacity/Scheduling',
        'Pricing',
        'No longer moving',
        'Moving themselves',
        'No contact',
        'Past experience',
        'National account move',
        'Incomplete customer info',
        'Out of time',
        'Appointment cancelled',
        'Not serviceable',
        'Move too small',
        'Other'
    ],
    'reason_cancelled' => [
        'value1'
    ],
    'vehicle_type' => [
        'Cube Van',
        'Double Trailer',
        'Drop Trailer',
        'Flatbed Trailer',
        'Flat Trailer',
        'Freight Trailer',
        'Pack Van',
        'Pallet Trailer',
        'Passenger Van',
        'Straight Truck',
        'Tractor',
        'Truck',
        'Auto Transport',
        'Local Tractors',
        'Local Vans',
        'O/O Van',
        'O/O Trailer'
    ]
];
$specialValues = [
    'ordersstatus' => [
        'Booked',
        'Cancelled'
    ]
];

$processedFields = [];

$currentTime = date('Y-m-d H:i:s');
$db = PearDatabase::getInstance();
$sql = "SELECT * FROM `vtiger_field` WHERE uitype=1500";
$result = $db->query($sql);
while($row =& $result->fetchRow()) {
    $fieldName = $row['fieldname'];
    $tablename = 'vtiger_'.$fieldName;
    if(!Vtiger_Utils::CheckTable($tablename)) {
        continue;
    }
    //Get table's primary key
    $indexRes   = $db->query("SHOW KEYS FROM `".$tablename."` WHERE Key_name = 'PRIMARY'");
    $primaryKey = $indexRes->fields['Column_name'];

    $sql      = "SELECT * FROM `vtiger_custompicklist` JOIN `".$tablename."` ON `vtiger_custompicklist`.id=`".$tablename."`.`".$primaryKey."` WHERE `vtiger_custompicklist`.`fieldid`='".$fieldName."'";
    $valueRes = $db->query($sql);
    while ($valueRow =& $valueRes->fetchRow()) {
        if(in_array($valueRow[$fieldName], $valueMap[$fieldName])) {
            continue;
        }
        $sql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
        $db->pquery($sql, [$valueRow['agentid'], $row['fieldid'], $valueRow[$fieldName], 'ADDED', $currentTime, $currentTime, 1]);
    }

    resetPicklistValues_19416($row['fieldid'], $tablename, $valueMap[$fieldName], $specialValues[$fieldName]);
}

function resetPicklistValues_19416($fieldid, $tablename, $values, $specialValues = null) {
    $seqTablename = $tablename.'_seq';
    if(!Vtiger_Utils::CheckTable($tablename) || !Vtiger_Utils::CheckTable($seqTablename)) {
        return false;
    }
    if(!is_array($values) || count($values) < 1) {
        return false;
    }

    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `$tablename`");
    Vtiger_Utils::ExecuteQuery("UPDATE `$seqTablename` SET id=0");

    $fieldModel = Vtiger_Field::getInstance($fieldid);
    $fieldModel->setPicklistValues($values);

    if($specialValues && is_array($specialValues) && count($specialValues) > 0) {
        Vtiger_Utils::ExecuteQuery("UPDATE `$tablename` SET `special`=1 WHERE `".$fieldModel->name."` IN ('".implode("','", $specialValues)."')");
    }
}
