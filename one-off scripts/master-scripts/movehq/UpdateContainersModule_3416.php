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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$ModuleInstance = Vtiger_Module::getInstance('Containers');
//containers_billcontcost
$field = Vtiger_Field::getInstance('containers_billcontcost',$ModuleInstance);
if ($field)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field->id));
}

//containers_contcost
$field1 = Vtiger_Field::getInstance('containers_contcost',$ModuleInstance);
if ($field1)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field1->id));
}

//containers_billsealcost
$field2 = Vtiger_Field::getInstance('containers_billsealcost',$ModuleInstance);
if ($field2)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field2->id));
}

//containers_sealcost
$field3 = Vtiger_Field::getInstance('containers_sealcost',$ModuleInstance);
if ($field3)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field3->id));
}

//containers_billrepaircost
$field4 = Vtiger_Field::getInstance('containers_billrepaircost',$ModuleInstance);
if ($field4)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field4->id));
}

//containers_repaircost
$field5 = Vtiger_Field::getInstance('containers_repaircost',$ModuleInstance);
if ($field5)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field5->id));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";