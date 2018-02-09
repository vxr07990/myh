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

echo "Starting Hotfix_GVL_Orders_MakeAPIFieldsMandatory.php<br/>\n";

$moduleName = 'Orders';
$fieldNames = [
  'orders_contacts',
  'invoice_format',
  'invoice_pkg_format',
  'invoice_document_format',
  'invoice_delivery_format',
  'billing_type'
];

foreach ($fieldNames as $fieldName) {
    echo "Attempting to modify $fieldName in $moduleName".PHP_EOL;
    ConvertToMandatory_MAFM($moduleName, $fieldName);
}

function ConvertToMandatory_MAFM($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $typeOfData = $changingField->typeofdata;
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
                print "$typeOfData, " . $changingField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $changingField->id]);
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

echo "Exiting Hotfix_GVL_Orders_MakeAPIFieldsMandatory.php".PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";