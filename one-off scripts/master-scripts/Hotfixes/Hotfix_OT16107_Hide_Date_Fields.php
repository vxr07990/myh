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


/*
 *
 *Goals:
 * Updates to Orders based on request - removing date fields that are used elsewhere.
 * OT - 16107
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

foreach (['Orders'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        //Hide these fields
        $hideFields = [
            'orders_planneddeliverydate',
            'orders_plannedloaddate',
            'orders_actualdeliverydate'
        ];
        hideFields_HOHDF($hideFields, $module);
    }
}

function hideFields_HOHDF($fields, $module)
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
                if ($field0->displaytype != 3) {
                    echo "Updating $field_name to have displaytype = 3 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `displaytype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['3', $field0->id]);
                }
            }
        }
    }
    return false;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";