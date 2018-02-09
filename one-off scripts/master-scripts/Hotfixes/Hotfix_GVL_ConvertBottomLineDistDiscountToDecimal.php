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


//<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
// Some zip codes are int(10), should be varchar to store leading zeroes or nonnumeric characters

$fieldTables = ['vtiger_quotes', 'vtiger_contracts'];
$dataType = 'decimal(7,2)';
$db = PearDatabase::getInstance();
foreach (['Actuals', 'Estimates', 'Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if ($module) {
        $fieldName = 'bottom_line_distribution_discount';
        foreach ($fieldTables as $tableName) {
            echo "$moduleName Module exists checking $fieldName field<br />\n";
            $field3 = Vtiger_Field::getInstance($fieldName, $module);
            if ($field3) {
                $stmt = 'EXPLAIN `'.$tableName.'` `'.$fieldName.'`';
                if ($res = $db->pquery($stmt)) {
                    while ($value = $res->fetchRow()) {
                        if ($value['Field'] == $fieldName) {
                            if (strtolower($value['Type']) != $dataType) {
                                echo "Updating $fieldName to be a $dataType type.<br />\n";
                                $db   = PearDatabase::getInstance();
                                $stmt = 'ALTER TABLE `'.$tableName.'` MODIFY COLUMN `'.$fieldName.'`'.$dataType.' DEFAULT NULL';
                                $db->pquery($stmt);
                            }
                            //we're only affecting the $fieldName so if we find it just break
                            break;
                        }
                    }
                } else {
                    echo "NO $fieldName column in The actual table?<br />\n";
                }
            } else {
                echo "NO $fieldName field in $moduleName module<br />\n";
            }
        }
        echo "Done with $moduleName fixes<br />\n";
    } else {
        echo "NO $moduleName MODULE?<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";