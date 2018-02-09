<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}


print "[RUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$isNew = false;
global $adb;


$WFAccountsModuleInstance = Vtiger_Module::getInstance('WFAccounts');
$WFWarehousesModuleInstance = Vtiger_Module::getInstance('WFWarehouses');
if ($WFAccountsModuleInstance && $WFWarehousesModuleInstance){
    $sql = "Select * From `vtiger_relatedlists` WHERE `vtiger_relatedlists`.`tableid`=? AND WHERE `vtiger_relatedlists`.`related_tableid`=?";
    $result = $adb->pquery($sql,array($WFAccountsModuleInstance->getId(),$WFWarehousesModuleInstance->getId()));
    if ($adb->num_rows($result)==0){
        $WFAccountsModuleInstance->setRelatedList($WFWarehousesModuleInstance, 'Warehouses', array('ADD','SELECT'), 'get_related_list');
        echo "Added WFWarehouses Related list to WFAccounts";
    }

    $result = $adb->pquery($sql,array($WFWarehousesModuleInstance->getId(),$WFAccountsModuleInstance->getId()));
    if ($adb->num_rows($result)==0){
        $WFWarehousesModuleInstance->setRelatedList($WFAccountsModuleInstance, 'Accounts', array('ADD','SELECT'), 'get_related_list');
        echo "Added WFAccounts Related list to WFWarehouses";
    }
}


