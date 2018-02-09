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

$moduleName = 'WFAccounts';

require_once 'vtlib/Vtiger/Module.php';
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('WFAccounts'));

require_once 'modules/ModComments/ModComments.php';
$detailviewblock = ModComments::addWidgetTo('WFAccounts');

$moduleInstance = Vtiger_Module::getInstance($moduleName);

$db = PearDatabase::getInstance();

Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_relatedlists` WHERE tabid= $moduleInstance->id");

$relatedModules = [
    'WFConfiguration' => 'get_related_list',
    'WFAttributes' => 'get_related_list',
    'Contacts' => 'get_related_list',
    'Documents' => 'get_attachments',
    'Orders' => 'get_related_list',
    'WFArticles' => 'get_related_list',
    'WFWorkOrders' => 'get_related_list',
    'WFInventory' => 'get_related_list',
    'WFConditions' => 'get_dependents_list'
];

$seq = 1;

foreach($relatedModules as $relatedModuleName => $relationName){
    $relatedInstance = Vtiger_Module::getInstance($relatedModuleName);
    $actions = 'ADD';
    if($relatedModuleName == 'Documents'){
        $actions .= ', SELECT';
    }
    $db = PearDatabase::getInstance();
    $result = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($moduleInstance->id, $relatedInstance->id));

    $moduleInstance->setRelatedList($relatedInstance, vtranslate($relatedModuleName,$relatedModuleName), Array($actions), $relationName, $seq);
    $seq++;
}
