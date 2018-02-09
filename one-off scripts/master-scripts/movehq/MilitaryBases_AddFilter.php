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

$militaryBasesModule = Vtiger_Module::getInstance('MilitaryBases');
if($militaryBasesModule) {
    $gblocField    = Vtiger_Field::getInstance('gbloc', $militaryBasesModule);
    $ownerField    = Vtiger_Field::getInstance('agentid', $militaryBasesModule);
    $activeField   = Vtiger_Field::getInstance('active', $militaryBasesModule);
    $locationField = Vtiger_Field::getInstance('location', $militaryBasesModule);
}

if($gblocField && $ownerField && $activeField && $locationField) {
    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $militaryBasesModule->addFilter($filter1);

    $filter1->addField($gblocField)->addField($locationField, 1)->addField($activeField, 2)->addfield($ownerField, 3);
} else {
    global $scriptVersionsToUpdate;
    unset($scriptVersionsToUpdate[__FILE__]);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
