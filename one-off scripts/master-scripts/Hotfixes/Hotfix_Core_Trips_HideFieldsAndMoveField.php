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


$moduleName = 'Trips';
$module = Vtiger_Module::getInstance($moduleName);

if(!$module){
    return;
}

$db = PearDatabase::getInstance();

$block = Vtiger_Block_Model::getInstance('LBL_TRIPS_DRIVER', $module);

$field = Vtiger_Field_Model::getInstance('trips_committedstatus', $module);
if ($field) {
    echo "trips_committedstatus field exists. Moving to Driver Information block<br>\n";
    if ($block) {
        $sql = 'UPDATE `vtiger_field` SET block = ? WHERE fieldid = ?';
        $db->pquery($sql, [$block->id, $field->id]);

        echo "trips_committedstatus field moved to the Driver Information block<br>\n";
    } else {
        echo "LBL_TRIPS_DRIVER block doesn't exists<br>\n";
    }
} else {
    echo "trips_committedstatus Field doesn't exists<br>\n";
}

$hideFields = [
    'fleet_status', 'trips_csarating',
    'trips_pqcrating', 'fleet_manager',
    'fleet_manager_email', 'trips_vehi_cube',
    'trips_vehi_length'
];

hideFields_CTHFAMF($hideFields, $module);

function hideFields_CTHFAMF($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}
