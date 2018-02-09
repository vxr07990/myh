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
$fieldNames = [
    'orders_account',       'orders_ldate',
    'orders_ddate',     'invoice_finance_charge',
    'payment_terms',        'orders_gweight',
    'orders_minweight'
    ];



print "<h2>Begin modifications to Orders fields</h2>\n";
foreach ($fieldNames as $fieldName) {
    AddMandatoryOMFM($moduleName, $fieldName);
}
print "<h2>END modifications to Orders fields</h2>\n";

function AddMandatoryOMFM($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $ordersField = Vtiger_Field::getInstance($fieldName, $module);
        if ($ordersField) {
            $typeOfData = $ordersField->typeofdata;
            $isMatch = preg_match('/~O/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~O/', '~M', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $ordersField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $ordersField->id]);
                print "<br>$moduleName $fieldName is converted to mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is already mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";