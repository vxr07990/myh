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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

$moduleName = 'WFOrders';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!$moduleInstance){
    return;
}



$WeightHistory = Vtiger_Module::getInstance('WFWeightHistory');
if ($WeightHistory) {
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('WFOrders'),getTabid('WFWeightHistory')));
    if($db->num_rows($rsRelated) == 0){
        $moduleInstance->setRelatedList($WeightHistory, 'Weight History', '', 'get_dependents_list');
    }
}
