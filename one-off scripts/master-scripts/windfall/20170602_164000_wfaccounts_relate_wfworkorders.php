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
$WFWorkOrdersModuleInstance = Vtiger_Module::getInstance('WFWorkOrders');
if ($WFAccountsModuleInstance && $WFWorkOrdersModuleInstance){
    $sql = "Select * From `vtiger_relatedlists` WHERE `vtiger_relatedlists`.`tableid`=? AND WHERE `vtiger_relatedlists`.`related_tableid`=?";
    $result = $adb->pquery($sql,array($WFAccountsModuleInstance->getId(),$WFWorkOrdersModuleInstance->getId()));
    if ($adb->num_rows($result)==0){
        $WFAccountsModuleInstance->setRelatedList($WFWorkOrdersModuleInstance, 'WorkOrders', array('ADD'), 'get_dependents_list');
        echo "Added WFWorkOrders Related list to WFAccounts";
    }else{
        echo "Can not add WFWorkOrders Related list to WFAccounts, it's has already exists";
    }
}else{
    echo "Can not add WFWorkOrders Related list to WFAccounts, it's something wrong";
}
