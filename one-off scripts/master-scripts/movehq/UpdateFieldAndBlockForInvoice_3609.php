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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');
global  $adb;

function createFieldsAndBlocks_3609($moduleInstance,$listFieldsInfo){
    foreach($listFieldsInfo as $blockLabel => $listField){
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if(!$blockInstance){
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach($listField as $fieldName =>$fieldInfo){
            echo "\n<br>BEGINNING create $fieldName field\n<br>";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
            if($fieldModel){
                echo "\n<br>$fieldName field already exists.\n<br>";
            }else{
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = 'vtiger_'.strtolower($moduleInstance->name);
                $fieldModel->columnname = $fieldName;
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option =>$value){
                    if(!in_array($option,array('picklistvalues','related_modules'))){
                        $fieldModel->$option = $value;
                    }
                }
                $blockInstance->addField($fieldModel);
                if(isset($fieldInfo['picklistvalues'])){
                    $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                }
                if(isset($fieldInfo['related_modules'])){
                    $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                }
                if(isset($fieldInfo['isentityidentifier'])){
                    $moduleInstance->setEntityIdentifier($fieldModel);
                }
                echo "done!\n<br>";
            }
        }
    }
}

$listFieldsInfo = [
    'LBL_INVOICE_INFORMATION'=>[
        'actualsid'=>[
            'label'=>'Related to Actual',
            'columntype'=>'int(11)',
            'uitype'=>10,
            'typeofdata'=>'I~M',
            'related_modules'=>['Actuals'],
        ]
    ]
];
$moduleInstance = Vtiger_Module::getInstance('Invoice');
if($moduleInstance){
    createFieldsAndBlocks_3609($moduleInstance,$listFieldsInfo);
}
$invoiceTabid = getTabid('Invoice');

//Remove Fields
$adb->pquery("UPDATE vtiger_field SET presence = 1 
              WHERE fieldname IN ('salesorder_id','exciseduty','salescommission','ship_street','ship_city','ship_state','ship_code','ship_country','ship_pobox') 
                    AND tabid = ?",[$invoiceTabid]);

//Update label fields
$adb->pquery("UPDATE vtiger_field SET fieldlabel = 'Billing Address 1' WHERE fieldname = 'bill_street' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET fieldlabel = 'Billing Address 2' WHERE fieldname = 'bill_pobox' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET fieldlabel = 'Customer Number' WHERE fieldname = 'customerno' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET fieldlabel = 'PO Number' WHERE fieldname = 'vtiger_purchaseorder' AND tabid = ?",[$invoiceTabid]);

//Re-order fields
$adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'actualsid' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'contact_id' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'account_id' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'invoicedate' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'duedate' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 7 WHERE fieldname = 'vtiger_purchaseorder' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 8 WHERE fieldname = 'customerno' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 9 WHERE fieldname = 'invoicestatus' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 10 WHERE fieldname = 'assigned_user_id' AND tabid = ?",[$invoiceTabid]);
$adb->pquery("UPDATE vtiger_field SET sequence = 11 WHERE fieldname = 'agentid' AND tabid = ?",[$invoiceTabid]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";