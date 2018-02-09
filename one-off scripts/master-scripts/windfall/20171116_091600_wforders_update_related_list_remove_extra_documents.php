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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('WFOrders');
if(!$moduleInstance){
    return;
}

$db = PearDatabase::getInstance();

$Document = Vtiger_Module_Model::getInstance('Documents');
if($Document){
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",[getTabid('WFOrders'),getTabid('Documents')]);
    if($db->num_rows($rsRelated) > 0){
        $moduleInstance->unSetRelatedList($Document, 'Documents', 'get_dependents_list');
        $moduleInstance->setRelatedList($Document, 'Documents', ['SELECT,ADD'], 'get_dependents_list');
    }
}
