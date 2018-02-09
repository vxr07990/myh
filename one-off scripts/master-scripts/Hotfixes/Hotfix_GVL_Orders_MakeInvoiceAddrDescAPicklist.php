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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Orders';
$fieldNames = ['bill_addrdesc'];
$targetUiType = 16;


print "<h2>Begin change to picklist</h2>\n";
foreach ($fieldNames as $fieldName) {
    ChangeToPicklistMIADAP($moduleName, $fieldName, $targetUiType);
}
print "<h2>Script to modify fields completed.</h2>\n";

function ChangeToPicklistMIADAP($moduleName, $fieldName, $targetUiType)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $uiType = $changingField->uitype;
            if ($uiType != $targetUiType) {
                print "<br>$moduleName $fieldName needs converting to picklist<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `uitype` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                $db->pquery($stmt, [$targetUiType, $changingField->id]);
                print "$moduleName $fieldName is converted to a picklist<br>\n";
            } else {
                print "$moduleName $fieldName is already a picklist. <br/>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";