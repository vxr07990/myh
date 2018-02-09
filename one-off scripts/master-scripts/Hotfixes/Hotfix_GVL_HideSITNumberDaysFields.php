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


//OT 15886 - Hotfix to hide Number Days field from SIT as the SIT Number of Days are determined from entered dates.
//LATER MODIFICATION - Unhid fields. Going to hide them on edit via .tpl or .js instead.

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<h2>Preparing to hide SIT Number Days fields in Estimates Edit</h2>\n";

foreach (['Estimates'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        //Hide these fields
        $hideFields = [
            'sit_origin_number_days',
            'sit_dest_number_days',
        ];
        hideFields_HSNDF($hideFields, $module);
    }
}

function hideFields_HSNDF($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 0) {
                    echo "Updating $field_name to be a have presence = 0 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['0', $field0->id]);
                }
                if ($field0->displaytype != 1) {
                    echo "Updating $field_name to have displaytype = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `displaytype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}
echo "<h2>Exiting hotfix to hide SIT Number Days fields in Estimates Edit</h2>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";