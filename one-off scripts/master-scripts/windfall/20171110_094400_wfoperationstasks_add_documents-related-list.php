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
$moduleInstance = Vtiger_Module::getInstance('WFOperationsTasks');

if(!$moduleInstance){
    return;
}

$Documents = Vtiger_Module::getInstance('Documents');
if ($Documents) {
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('WFOperationTasks'),getTabid('Documents')));
    if($db->num_rows($rsRelated) == 0){
        $moduleInstance->setRelatedList($Documents, 'Documents', ['ADD','SELECT'], 'get_dependents_list');
    }
}
