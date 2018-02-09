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

$moduleInstance = Vtiger_Module::getInstance('WFCostCenters');

if(!$moduleInstance){
    return;
}


$commentsModule = Vtiger_Module::getInstance('ModComments');
if($commentsModule) {
    $fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules(['WFCostCenters']);
    ModComments::removeWidgetFrom('WFCostCenters'); //remove it before adding it because we can't tell if it already exists
    ModComments::addWidgetTo('WFCostCenters');
}

