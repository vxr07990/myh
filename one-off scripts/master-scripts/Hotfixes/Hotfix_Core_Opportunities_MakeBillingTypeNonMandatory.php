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

$moduleName = 'Opportunities';
$fieldNames = ['billing_type'];

foreach ($fieldNames as $fieldName) {
    RemoveMandatoryCOMBTNM($moduleName, $fieldName);
}
print "<h2>END modifications to Opportunities fields </h2>\n";

function RemoveMandatoryCOMBTNM($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $dateField = Vtiger_Field::getInstance($fieldName, $module);
        if ($dateField) {
            $typeOfData = $dateField->typeofdata;
            $isMatch = preg_match('/~M/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~M/', '~O', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to non-mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $dateField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $dateField->id]);
                print "<br>$moduleName $fieldName is converted to non-mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is already non-mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";