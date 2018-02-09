<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
$Vtiger_Utils_Log = true;
// TO DO
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleName = "Orders";
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance){
    // update presence of field to 1 to hidden
    $params = array();
    $needUpdate = array();
    $params[] = '1';
    $arrayFieldNeedRemove = array(
        'invoice_format', // "Invoice Template"
        'invoice_pkg_format', // "Invoice Packe"
        'invoice_document_format', // "Document Format"
        'invoice_finance_charge',  // "Finance Charge"
        'payment_terms', // "Payment Terms"
    );

    foreach ($arrayFieldNeedRemove as $key => $fieldName){
        $fieldInstance = Vtiger_Field::getInstance($fieldName,$moduleInstance);
        if ($fieldInstance && $fieldInstance->presence == '2'){
            $params[] = $fieldInstance->id;
            $needUpdate[] = $fieldInstance->id;
        }
    }

    if (count($needUpdate)>0){
        $sqlUpdate = "UPDATE `vtiger_field` SET `vtiger_field`.`presence` = ? WHERE `vtiger_field`.`fieldid` IN (".generateQuestionMarks($needUpdate).")";
        $adb->pquery($sqlUpdate, $params);

        echo "UPDATE `presence` of fields in Orders Module to 1";
    }

    // update sequence of field to order field on block in module
    $fieldNeedToUpdateSequence = array(
        'bill_street'               => '3', //Billing Address 1
        'bill_pobox'                => '4', //Billing Address 2
        'payment_type'              => '12', //Payment Type
        'bill_addrdesc'             => '2', //Billing Address Description
        'bill_company'              => '1', //Billing To
        'bill_city'                 => '5', //Billing City
        'invoice_delivery_format'   => '11', //Invoice Delivery
        'bill_state'                => '6', //Billing State
        'bill_code'                 => '7', //Billing Zip
        'bill_country'              => '8', //Billing Country
        'invoice_phone'             => '9', //Phone Number
        'invoice_email'             => '10', //Email
    );

    foreach ( $fieldNeedToUpdateSequence as $fieldName => $sequence){
        $fieldInstance = Vtiger_Field::getInstance($fieldName,$moduleInstance);
        if ($fieldInstance){
            $sqlUpdate = "UPDATE `vtiger_field` SET `vtiger_field`.`sequence` = ? WHERE `vtiger_field`.`fieldid` = ?";
            $adb->pquery($sqlUpdate, array($sequence,$fieldInstance->id));
            echo "<br>Updated sequence of $fieldName field to $sequence within $moduleName module<br>";
        }
    }
}

echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";