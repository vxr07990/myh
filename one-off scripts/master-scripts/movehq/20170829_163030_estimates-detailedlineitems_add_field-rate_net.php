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
 * User: jgriffin
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$field_name = 'dli_rate_net';
$columntype = 'decimal(13,2)';
$tableName = 'vtiger_detailed_lineitems';
if (Vtiger_Utils::CheckTable($tableName)) {
    $db   = PearDatabase::getInstance();
    $found = false;
    $stmt = 'EXPLAIN `'.$tableName.'` `'.$field_name.'`';
    if ($res = $db->pquery($stmt)) {
        while ($value = $res->fetchRow()) {
            if ($value['Field'] == $field_name) {
                $found = true;
                if (strtolower($value['Type']) != strtolower($columntype)) {
                    echo "Updating $field_name to be a " . $columntype . " type.<br />\n";
                    $db   = PearDatabase::getInstance();
                    $stmt = 'ALTER TABLE `' . $tableName . '` MODIFY COLUMN `' . $field_name . '` ' . $columntype . ' DEFAULT NULL';
                    $db->pquery($stmt);
                }
                //we're only affecting the $field_name so if we find it just break
                break;
            }
        }
    } else {
        echo "NO $field_name column in The actual table?<br />\n";
    }
    if (!$found) {
        echo "Adding $field_name as " . $columntype . " to: " . $tableName . ".<br />\n";
        $stmt = 'ALTER TABLE `' . $tableName . '` ADD COLUMN `' . $field_name . '` ' . $columntype . ' DEFAULT NULL';
        $db->pquery($stmt);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
