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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleInstance = Vtiger_Module::getInstance('Orders');
$InvoiceDetailBlockWithinOrders = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $moduleInstance);
if($InvoiceDetailBlockWithinOrders){
    $fields = array(
        'payment_type' => 1,
        'invoice_format' => 2,
        'invoice_pkg_format' => 3,
        'invoice_document_format' => 4,
        'invoice_delivery_format' => 5,
        'invoice_finance_charge' => 6,
        'payment_terms' => 7,
        'commodity' => 8,
    );
    foreach ($fields as $fieldname => $sequence){
        $field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
        if ($field){
            if ($fieldname == 'commodity'){
                $updateSequence = "UPDATE `vtiger_field` SET `vtiger_field`.`presence` = ? WHERE `vtiger_field`.`fieldid`=?";
                $adb->pquery($updateSequence,array(1,$field->id));
                echo "<br>Update presence $fieldname in LBL_ORDERS_INVOICE block within Orders Module<br>";
            }else{
                $updateSequence = "UPDATE `vtiger_field` SET `vtiger_field`.`sequence` = ? WHERE `vtiger_field`.`fieldid`=?";
                $adb->pquery($updateSequence,array($sequence,$field->id));
                echo "<br>Update sequence $fieldname in LBL_ORDERS_INVOICE block within Orders Module<br>";
            }
        }
    }
}

$OrderDetailBlockWithinOrders = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleInstance);
if ($OrderDetailBlockWithinOrders){
    $fields = array(
        'orders_contacts' => 1,
        'orders_no' => 2,
        'order_name' => 3,
    );
    foreach ($fields as $fieldname => $sequence){
        $field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
        if ($field){
            $updateSequence = "UPDATE `vtiger_field` SET `vtiger_field`.`sequence` = ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($updateSequence,array($sequence,$field->id));
            echo "<br>Update sequence $fieldname in LBL_ORDERS_INFORMATION block within Orders Module<br>";
        }

        if ($fieldname == 'orders_contacts'){
            $updateSequence = "UPDATE `vtiger_field` SET `vtiger_field`.`typeofdata` = ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($updateSequence,array('V~M',$field->id));
            echo "<br>Update sequence $fieldname in LBL_ORDERS_INFORMATION block within Orders is mandatory<br>";
        }

        if ($fieldname == 'order_name'){
            $updateSequence = "UPDATE `vtiger_field` SET `vtiger_field`.`typeofdata` = ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($updateSequence,array('V~O',$field->id));
            echo "<br>Update sequence $fieldname in LBL_ORDERS_INFORMATION block within Orders is not mandatory<br>";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";