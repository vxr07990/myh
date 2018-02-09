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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');
global $adb;


function createFieldsAndBlocks_3464($moduleInstance,$listFieldsInfo){
    foreach($listFieldsInfo as $blockLabel => $listField){
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if(!$blockInstance){
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach($listField as $fieldName =>$fieldInfo){
            echo "\nBEGINNING create $fieldName field\n";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
            if($fieldModel){
                echo "\n$fieldName field already exists.\n";
            }else{
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = $fieldInfo['tablename'];
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
                echo "done!\n";
            }
        }
    }
}
$reasonPickList = [
    'Move date has passed',
    'Capacity/Scheduling',
    'Pricing',
    'No longer moving',
    'Moving themselves',
    'No contact',
    'Past experience',
    'National account move',
    'Incomplete customer info',
    'Out of time',
    'Appointment cancelled',
    'Not serviceable',
    'Move too small',
    'Other'
];
$arrModuleStructure = [
    'LBL_ORDERS_INFORMATION'=>[
        'order_name'=>[
            'label'=>'Order Name',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~M',
        ],
        'order_reason'=>[
            'label'=>'Reason',
            'columntype'=>'varchar(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'picklistvalues'=>$reasonPickList,
        ],
        'projectid'=>[
            'label'=>'Project',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
        ],
    ],
    'LBL_ORDER_ACCOUNT_ADDRESS'=>[
        'national_account_number'=>[
            'label'=>'National Account Number',
            'columntype'=>'varchar(150)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_address1'=>[
            'label'=>'Address 1',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_address2'=>[
            'label'=>'Address 2',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_city'=>[
            'label'=>'City',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_state'=>[
            'label'=>'State',
            'columntype'=>'varchar(100)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_zip_code'=>[
            'label'=>'Zip code',
            'columntype'=>'varchar(100)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'account_country'=>[
            'label'=>'Country',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
    ],
    'LBL_RECORD_UPDATE_INFORMATION'=>[
        'createdtime' =>[
            'label'=>'Created Date',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'tablename'=>'vtiger_crmentity'
        ],
        'modifiedtime' =>[
            'label'=>'Modified Date',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'tablename'=>'vtiger_crmentity'
        ],
        'created_user_id' =>[
            'label'=>'Created By',
            'columntype'=>'int(19)',
            'column'=>'smcreatorid',
            'uitype'=>52,
            'typeofdata'=>'I~O',
            'tablename'=>'vtiger_crmentity',
            'displaytype'=>2
        ],

    ]
];
$orderModuleInstance = Vtiger_Module::getInstance('Orders');
if($orderModuleInstance){
    createFieldsAndBlocks_3464($orderModuleInstance,$arrModuleStructure);
}

$orderTabId = getTabid("Orders");
$orderInformationBlock = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $orderModuleInstance);
$orderDateBlock = Vtiger_Block::getInstance('LBL_ORDERS_DATES', $orderModuleInstance);
$orderRecordUpdateBlock = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $orderModuleInstance);

//Hide field
$adb->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldname = 'orders_otherstatus' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldname = 'targetenddate' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldname = 'orders_miles' AND tabid = ?",[$orderTabId]);
//Move field

$adb->pquery("UPDATE vtiger_field SET block = ? WHERE fieldname = 'createdtime' AND tabid = ?",[$orderRecordUpdateBlock->id,$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET block = ? WHERE fieldname = 'modifiedtime' AND tabid = ?",[$orderRecordUpdateBlock->id,$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET block = ? WHERE fieldname = 'received_date' AND tabid = ?",[$orderDateBlock->id,$orderTabId]);

//Update block sequence for Account Block
$adb->pquery("UPDATE vtiger_blocks SET sequence = (sequence + 1) WHERE tabid = ? AND sequence > ?",[$orderTabId,$orderInformationBlock->sequence]);
$adb->pquery("UPDATE vtiger_blocks SET sequence = ? WHERE tabid = ? AND blocklabel = 'LBL_ORDER_ACCOUNT_ADDRESS' ",[$orderInformationBlock->sequence + 1,$orderTabId]);

$sqlBlock = "SELECT * FROM vtiger_blocks WHERE tabid = ? ORDER BY sequence ASC";
$rsBlocks = $adb->pquery($sqlBlock,[getTabid('Orders')]);
$seq = 0;
while ($row = $adb->fetchByAssoc($rsBlocks)){
    $seq ++;
    $adb->pquery("UPDATE vtiger_blocks SET  sequence = ? WHERE blockid = ?",[$seq,$row['blockid']]);
}

//Update block sequence for Order Details
$adb->pquery("UPDATE vtiger_field SET sequence = 100 WHERE block = ? AND tabid = ?",[$orderInformationBlock->id,$orderTabId]);

$adb->pquery("UPDATE vtiger_field SET sequence = 1 WHERE fieldname = 'order_name' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'orders_no' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'orders_contacts' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'orders_ponumber' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'orders_bolnumber' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'orders_vanlineregnum' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 7 WHERE fieldname = 'ordersstatus' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 8 WHERE fieldname = 'order_reason' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 9 WHERE fieldname = 'business_line2' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 10 WHERE fieldname = 'billing_type' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 11 WHERE fieldname = 'authority' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 12 WHERE fieldname = 'orders_opportunities' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 13 WHERE fieldname = 'orders_relatedorders' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 14 WHERE fieldname = 'projectid' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 15 WHERE fieldname = 'orders_account' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 16 WHERE fieldname = 'account_contract' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 17 WHERE fieldname = 'orders_elinehaul' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 18 WHERE fieldname = 'orders_etotal' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 19 WHERE fieldname = 'orders_discount' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 20 WHERE fieldname = 'tariff_id' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 21 WHERE fieldname = 'orders_sit' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 22 WHERE fieldname = 'competitive' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 23 WHERE fieldname = 'agentid' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 24 WHERE fieldname = 'assigned_user_id' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 25 WHERE fieldname = 'business_line' AND tabid = ?",[$orderTabId]);

// Update field attributes
$adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldname = 'business_line2' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldname = 'billing_type' AND tabid = ?",[$orderTabId]);
$adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldname = 'authority' AND tabid = ?",[$orderTabId]);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";