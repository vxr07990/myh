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



/*
 * Hotfix_Add_LocalDispatch_Link.php
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if ($moduleInstance) {
    require_once('vtlib/Vtiger/Link.php');
    Vtiger_Link::addLink($moduleInstance->id, 'LISTVIEWMASSACTION', 'Copy Resources From Task', 'javascript:OrdersTask_LocalDispatch_Js.showCopyModal();', '', '', '');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";