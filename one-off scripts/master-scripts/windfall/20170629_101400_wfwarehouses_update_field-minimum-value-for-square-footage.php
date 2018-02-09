<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/29/2017
 * Time: 10:14 AM
 */
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

$moduleFields = ['WFWarehouses' => [
        'square_footage',
    ],
];




print "<h2>Begin modifications to number fields  </h2>\n";
foreach ($moduleFields as $moduleName=>$fieldNames) {
    foreach($fieldNames as $fieldName) {
        AddMinimumValueWUFMVFSF($moduleName, $fieldName);
    }
}
print "<h2>END modifications to number fields </h2>\n";

function AddMinimumValueWUFMVFSF($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $typeOfData = $changingField->typeofdata;
            $hasMin = preg_match('/~MIN/', $typeOfData);
            if ($hasMin === false) {
                print "ERROR: couldn't preg_match?";
            } elseif (!$hasMin) {
                $typeOfData = $typeOfData.'~MIN=0';
                print "<br>$moduleName $fieldName needs minimum value<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $changingField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $changingField->id]);
                print "<br>$moduleName $fieldName updated with minimum<br>\n";
            } else {
                print "<br>$moduleName $fieldName already has set minimum<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
