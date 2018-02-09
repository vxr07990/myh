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

$Vtiger_Utils_Log = true;
global $adb;

$moduleNames = [
    'WFLocations',
    'WFSlotConfiguration',
    'WFLocationHistory',
    'WFConfiguration',
    'WFArticles',
    'WFCostCenters',
    'WFLocationTypes',
    'WFStatus',
    'WFConditions',
    'WFInventory',
    'WFInventoryLocations',
    'WFInventoryHistory',
    'WFWorkOrders',
    'WFLineItems',
    'WFLocationTags',
    'WFTransactions',
    'WFSyncCenters',
    'WFLocationOrders',
    'WFWarehouses',
    'WFCarriers',
    'WFAccounts',
    'WFOrders'
];


foreach($moduleNames as $moduleName){
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if(!$moduleInstance){
        echo "$moduleName not found. Skipping Assigned To field add <br />\n";
        continue;
    }
    if($moduleName == 'WFWarehouses'){
        $blockInstance = Vtiger_Block::getInstance('LBL_WFWAREHOUSE_INFORMATION', $moduleInstance);
    } else if ($moduleName == 'WFAccounts') {
        $blockInstance = Vtiger_Block::getInstance('LBL_WFACCOUNTS_DETAIL', $moduleInstance);
    } else if ($moduleName == 'WFOrders') {
        $blockInstance = Vtiger_Block::getInstance('LBL_WFORDER_INFORMATION', $moduleInstance);
    } else if ($moduleName == 'WFCarriers') {
        $blockInstance = Vtiger_Block::getInstance('LBL_WFCARRIERS_INFORMATION', $moduleInstance);
    } else {
        $blockInstance = Vtiger_Block::getInstance('LBL_'.strtoupper($moduleName).'_DETAILS', $moduleInstance);
    }
    if(!$blockInstance){
        echo "Details block not found in $moduleName. Skipping Assigned To field add <br />\n";
        continue;
    }
    $field = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
    if ($field) {
        $sql = "UPDATE `vtiger_field` SET displaytype = 1, typeofdata = 'V~M', block = ? WHERE fieldid = ?";
        $adb->pquery($sql, [$blockInstance->id, $field->id]);
        echo "Field found. Updated display type in $moduleName<br />\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_'.strtoupper($moduleName).'_ASSIGNED_USER_ID';
        $field->name       = 'assigned_user_id';
        $field->table      = 'vtiger_crmentity';
        $field->column     = 'smownerid';
        $field->uitype     = 53;
        $field->typeofdata = 'V~M';
        $blockInstance->addField($field);
        echo "Added field to $moduleName<br />\n";
    }
}


