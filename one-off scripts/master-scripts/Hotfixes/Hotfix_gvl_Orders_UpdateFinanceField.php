<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';


$moduleName = 'Orders';
$blockName = 'LBL_ORDERS_INVOICE';
$fieldName = 'invoice_finance_charge';
$columntype = 'DECIMAL(5,2)';
$targetUIType = 9;
$targetTypeofData = 'N~O';
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
                    $financeChargeField = Vtiger_Field::getInstance($fieldName, $module);
                    if ($financeChargeField) {
                        $typeOfData = $financeChargeField->typeofdata;
                        if ($typeOfData == $targetTypeofData) {
                            print "Type of data matches<br>\n";
                        } else {
                            print "<br>$moduleName $fieldName needs typeofdata updated<br>\n";
                            $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                                    //. " `quickcreate` = 1"
                                    ." WHERE `fieldid` = ? LIMIT 1";
                            print "$stmt\n";
                            print "$targetTypeofData, ".$financeChargeField->id."<br />\n";
                            $db->pquery($stmt, [$targetTypeofData, $financeChargeField->id]);
                            print "<br>$moduleName $fieldName is now updated.<br>\n";
                        }
                        $uiType = $financeChargeField->uitype;
                        if ($uiType == $targetUIType) {
                            print "Type of data matches<br>\n";
                        } else {
                            print "<br>$moduleName $fieldName needs uitype updated<br>\n";
                            $stmt = "UPDATE `vtiger_field` SET `uitype` = ?"
                                    //. " `quickcreate` = 1"
                                    ." WHERE `fieldid` = ? LIMIT 1";
                            print "$stmt\n";
                            print "$targetUIType, ".$financeChargeField->id."<br />\n";
                            $db->pquery($stmt, [$targetUIType, $financeChargeField->id]);
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
$accountsFinanceCharge = 'finance_charge';
$accountsModule = Vtiger_Module::getInstance('Accounts');
if ($accountsModule) {
    $db         = PearDatabase::getInstance();
    $found      = false;
    $stmt       = 'EXPLAIN `vtiger_account_invoicesettings` `'.$accountsFinanceCharge.'`';
    if ($res = $db->pquery($stmt)) {
        while ($value = $res->fetchRow()) {
            if ($value['Field'] == $accountsFinanceCharge) {
                $found = true;
                if (strtolower($value['Type']) != strtolower($columntype)) {
                    echo "Updating ".$accountsFinanceCharge." to be a ".$columntype." type.<br />\n";
                    $db   = PearDatabase::getInstance();
                    $stmt = 'ALTER TABLE `vtiger_account_invoicesettings` MODIFY COLUMN `'.$accountsFinanceCharge.'` '.$columntype.' DEFAULT NULL';
                    $db->pquery($stmt);
                }
                //we're only affecting the $field->column so if we find it just break
                break;
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";