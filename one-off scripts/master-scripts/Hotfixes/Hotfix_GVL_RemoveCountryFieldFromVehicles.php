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


// OT 2765 - Removing Country field from Vehicles Module. Field hidden. Reordering remaining fields.

// Reorder fields script
// Reorder fields in the ui

echo "<h3>Starting Hotfix_GVL_RemoveCountryFieldFromVehicles.php</h3>\n";

$moduleName = 'Vehicles';
$blockName = 'LBL_VEHICLES_LICENSE';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>Reordering Fields in License Information </p>\n";
    $fieldOrder = [
        'vehicle_plateno',      'vehicle_platestate',
        'vehicle_plateexp',     'vehicle_platetype',
        'vehicle_platecountry'
    ];

    $db = PearDatabase::getInstance();
    $count = 0;
    foreach ($fieldOrder as $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, [$count++, $fieldInstance->id]);
        if ($field == 'vehicle_platecountry') {
            if ($fieldInstance->presence != 1) {
                echo "Updating $field to be a have presence = 1 <br />\n";
                $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                $db->pquery($stmt, ['1', $fieldInstance->id]);
            }
            if ($fieldInstance->displaytype != 3) {
                echo "Updating $field to have displaytype = 3 <br />\n";
                $stmt = 'UPDATE `vtiger_field` SET `displaytype` = ? WHERE `fieldid` = ?';
                $db->pquery($stmt, ['3', $fieldInstance->id]);
            }
        }
    }
    echo "<p>Done reordering fields in the License Information</p>\n";
} else {
    echo "<p>$blockName wasn't found in $moduleName</p>\n";
}

echo "<h3>Finished Hotfix_GVL_RemoveCountryFieldFromVehicles.php</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";