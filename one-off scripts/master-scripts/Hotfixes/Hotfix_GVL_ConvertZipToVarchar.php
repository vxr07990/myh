<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
// Some zip codes are int(10), should be varchar to store leading zeroes or nonnumeric characters

$db = PearDatabase::getInstance();
foreach (['Agents', 'Vanlines', 'Employees','Estimates','Actuals'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if ($module) {
        $fields = [
            'agent_zip',
            'vanline_zip',
            'zip',
            'sit_origin_zip',
            'sit_dest_zip'
        ];
        foreach ($fields as $fieldName) {
            echo "$moduleName Module exists checking $fieldName field<br />\n";
            $field3 = Vtiger_Field::getInstance($fieldName, $module);
            if ($field3) {
                $tableName = $field3->table;
                $typeofdata = $field3->typeofdata;
                if(($index = strpos($typeofdata, 'I~')) !== false) {
                    $typeofdata[$index] = 'V';
                }
                echo "Updating $fieldName to be a varchar(50) type.<br />\n";
                $db   = PearDatabase::getInstance();
                $stmt = "ALTER TABLE `$tableName` MODIFY COLUMN `".$field3->column.'` varchar(50) DEFAULT NULL';
                $db->pquery($stmt);
                $stmt = 'UPDATE `vtiger_field` SET uitype = 1, typeofdata = "'.$typeofdata.'" WHERE fieldid = '.$field3->id;
                $db->query($stmt);
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
