<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
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
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

$moduleName = 'WFOrders';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!$moduleInstance){
    return;
}

$tableid = $moduleInstance->getId();
$sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
$result = $adb->pquery($sql, array($tableid));
if ($adb->num_rows($result) == 0) {
    $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", ['1', $tableid]);
}

$commentsModule = Vtiger_Module::getInstance('ModComments');
if($commentsModule) {
    $fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules([$moduleName]);
    ModComments::removeWidgetFrom($moduleName); //remove it before adding it because we can't tell if it already exists
    ModComments::addWidgetTo($moduleName);
}

$Documents = Vtiger_Module::getInstance('Documents');
if ($Documents) {
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('WFOrders'),getTabid('Documents')));
    if($db->num_rows($rsRelated) == 0){
        $moduleInstance->setRelatedList($Documents, 'Documents', ['ADD','SELECT'], 'get_dependents_list');
    }
}

$WFInventory = Vtiger_Module::getInstance('WFInventory');
if ($WFInventory) {
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('WFOrders'),getTabid('WFInventory')));
    if($db->num_rows($rsRelated) == 0){
        $moduleInstance->setRelatedList($WFInventory, 'Inventory', '', 'get_dependents_list');
    }
}
