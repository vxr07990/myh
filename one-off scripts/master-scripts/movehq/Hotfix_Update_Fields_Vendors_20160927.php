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
 * The goal is to make the closingdate field non-mandatory and to not appear on the quick create.
 *
 * based on Hotfix_SirvaOppsFieldsMod.php
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

print "<br>Begin modifications to Vendor field: vendor_status. <br>\n";
doWork('Vendors', 'vendor_status', 'typeofdata', 'V~M');
print "<br>END modifications to Vendor fields: vendor_status. <br>\n";

print "<br>Begin modifications to Vendor field: vendor_no. <br>\n";
doWork('Vendors', 'vendor_no', 'displaytype', '3');
print "<br>END modifications to Vendor fields: vendor_no. <br>\n";

print "<br>Begin modifications to Vendor field: assigned_user_id. <br>\n";
doWork('Vendors', 'assigned_user_id', 'displaytype', '2');
print "<br>END modifications to Vendor fields: assigned_user_id. <br>\n";

function doWork($moduleName, $fieldName, $columnChange, $valueChange)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            $currentValue = $field->$columnChange;
            if ($valueChange === $currentValue) {
                print "<br>$moduleName $fieldName $columnChange is Already $valueChange<br>\n";
            } else {
                print "<br>$moduleName $fieldName needs converting to $valueChange<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `" .$columnChange."` = ?"
                        ." WHERE `fieldid` = ? LIMIT 1";
                $db->pquery($stmt, [$valueChange, $field->id]);
                print "<br>$moduleName $fieldName $columnChange is converted to $valueChange<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";