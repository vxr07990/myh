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
global $adb;
$moduleInstance = Vtiger_Module::getInstance('QuotingTool');
if ($moduleInstance){
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $adb->pquery("UPDATE `vtiger_tab` SET `isentitytype`='1' WHERE `tabid`=?",array($moduleInstance->id));
    $field = Vtiger_Field::getInstance('filename', $moduleInstance);
    if ($field)
    {
        $moduleInstance->setEntityIdentifier($field);
    }
}
echo '<br>Done - Update Document Designer module<br><br>';

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";