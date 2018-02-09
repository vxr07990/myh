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


//based on something else... one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_fixLeadSourceManagerAgentID_ProgTerm.php
//this is changed all the fuel surcharges to account for the 4 digit decimal possibility.
//may not be required for the _percent ones but no sense in not ensuring they are all the same.
//setting to 11,4 since it was 10,3 that just adds one more decimal digit.
//RARGH:
//OK because that is setting 0 when I want blank we're making this varchar (11) that's all
$db = PearDatabase::getInstance();
foreach (['Estimates', 'Quotes'] as $moduleName) {
    $EstimatesModule = Vtiger_Module::getInstance($moduleName);
    if ($EstimatesModule) {
        $fields = [
            'sit_origin_fuel_percent',
            'sit_dest_fuel_percent',
            'sit_origin_fuel_surcharge',
            'sit_dest_fuel_surcharge',
            'accesorial_fuel_surcharge',
        ];
//EXPLAIN `vtiger_quotes` `sit_origin_fuel_percent`;
//EXPLAIN `vtiger_quotes` `sit_dest_fuel_percent`;
//EXPLAIN `vtiger_quotes` `sit_origin_fuel_surcharge`;
//EXPLAIN `vtiger_quotes` `sit_dest_fuel_surcharge`;
//EXPLAIN `vtiger_quotes` `accesorial_fuel_surcharge`;
        foreach ($fields as $fieldName) {
            echo "$moduleName Module exists checking $fieldName field<br />\n";
            $field3 = Vtiger_Field::getInstance($fieldName, $EstimatesModule);
            if ($field3) {
                //hell you have to fix the created table!  ... sigh.
                $stmt = 'EXPLAIN `vtiger_quotes` `'.$fieldName.'`';
                if ($res = $db->pquery($stmt)) {
                    while ($value = $res->fetchRow()) {
                        if ($value['Field'] == $fieldName) {
                            if (strtolower($value['Type']) != 'varchar(11)') {
                                echo "Updating $fieldName to be a varchar(11) type.<br />\n";
                                $db   = PearDatabase::getInstance();
                                $stmt = 'ALTER TABLE `vtiger_quotes` MODIFY COLUMN `'.$fieldName.'` varchar(11) DEFAULT NULL';
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