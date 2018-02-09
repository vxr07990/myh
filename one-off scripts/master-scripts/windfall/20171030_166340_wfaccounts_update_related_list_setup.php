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
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

$relatedModuleLabel = [
    'WFConfiguration' => 'Configuration',
    'Contacts' => 'Contacts',
    'Orders' => 'Orders',
    'WFWorkOrders' => 'OrdersTasks',
    'WFInventory' => 'Inventory',
    'WFArticles' => 'Articles',
];

$module = Vtiger_Module::getInstance('WFAccounts');

if(!$module) {
  return;
}

foreach ($relatedModuleLabel as $relatedModuleName => $relatedModuleLabel) {
    $relatedModuleInstance = Vtiger_Module::getInstance($relatedModuleName);
    $module->unsetRelatedList($relatedModuleInstance, $relatedModuleLabel, 'get_related_list');
    $module->unsetRelatedList($relatedModuleInstance, $relatedModuleLabel, 'get_dependents_list');
    $module->setRelatedList($relatedModuleInstance, $relatedModuleLabel, ['ADD'], 'get_dependents_list');
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

