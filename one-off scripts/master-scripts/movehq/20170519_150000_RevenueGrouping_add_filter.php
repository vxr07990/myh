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

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$revenueGroupingModule = Vtiger_Module::getInstance('RevenueGrouping');
if($revenueGroupingModule) {
    $ownerField        = Vtiger_Field::getInstance('agentid', $revenueGroupingModule);
}

if($ownerField) {
    $filter1 = Vtiger_Filter::getInstance('All', $revenueGroupingModule);
    if($filter1) {
        $filter1->delete();
    }

    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $revenueGroupingModule->addFilter($filter1);

    $filter1->addField($ownerField);
} else {
    global $scriptVersionsToUpdate;
    unset($scriptVersionsToUpdate[__FILE__]);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
