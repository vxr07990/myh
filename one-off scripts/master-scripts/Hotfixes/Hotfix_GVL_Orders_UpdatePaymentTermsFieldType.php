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

$moduleName = 'Orders';
$blockName = 'LBL_ORDERS_INVOICE';
$fieldName = 'payment_terms';
$columntype = 'VARCHAR(255)';
$targetTypeofData = 'V~O';
$module = Vtiger_Module::getInstance($moduleName);

if ($module) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $db         = PearDatabase::getInstance();
        $found      = false;
        $stmt       = 'EXPLAIN `'.$field->table.'` `'.$field->column.'`';
        if ($res = $db->pquery($stmt)) {
            while ($value = $res->fetchRow()) {
                if ($value['Field'] == $field->column) {
                    $found = true;
                    if (strtolower($value['Type']) != strtolower($columntype)) {
                        echo "Updating ".$field->column." to be a ".$columntype." type.<br />\n";
                        $db   = PearDatabase::getInstance();
                        $stmt = 'ALTER TABLE `'.$field->table.'` MODIFY COLUMN `'.$field->column.'` '.$columntype.' DEFAULT NULL';
                        $db->pquery($stmt);
                    }
                    $changingField = Vtiger_Field::getInstance($fieldName, $module);
                    if ($changingField) {
                        $typeOfData = $changingField->typeofdata;
                        if ($typeOfData == $targetTypeofData) {
                            print "Type of data matches<br>\n";
                        } else {
                            print "<br>$moduleName $fieldName needs typeofdata updated<br>\n";
                            $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                                    //. " `quickcreate` = 1"
                                    ." WHERE `fieldid` = ? LIMIT 1";
                            print "$stmt\n";
                            print "$targetTypeofData, ".$changingField->id."<br />\n";
                            $db->pquery($stmt, [$targetTypeofData, $changingField->id]);
                            print "<br>$moduleName $fieldName is now updated.<br>\n";
                        }
                    }
                    //we're only affecting the $field->column so if we find it just break
                    break;
                }
            }
        } else {
            echo "NO ".$field->column." column in The actual table?<br />\n";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";