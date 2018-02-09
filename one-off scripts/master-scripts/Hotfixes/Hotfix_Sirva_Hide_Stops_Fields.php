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
 * Purpose of this file is to hide the is primary and weight fields in extra stops module.
 * SIRVA request
 * by Ian Overholt
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

try {
    if (!$db) {
        $db = PearDatabase::getInstance();
    }
    $module = Vtiger_Module::getInstance('ExtraStops');
    if (!$module) {
        echo "The extra stops module needs to be created first!<br/>";
    } else {
        $fields_to_check = [
            'extrastops_isprimary',
            'extrastops_weight'
        ];

        foreach ($fields_to_check as $field_name) {
            $field = Vtiger_Field::getInstance($field_name, $module);
            if (!$field) {
                echo "The ".$field_name." does not exist!!!<br/>";
            } else {
                if ($field->presence!=1) {
                    echo "Updating the ".$field_name." to be hidden<br/>";
                    $sql = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($sql, ['1', $field->id]);
                    echo "".$field_name." is now hidden<br/>".$field->fieldid;
                } else {
                    echo "".$field_name." is already hidden<br/>";
                }
            }
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";