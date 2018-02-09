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


$ModuleInstance = Vtiger_Module::getInstance('ContainerTypes');
$field = Vtiger_Field::getInstance('containertypes_contcost',$ModuleInstance);
if ($field)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field->id));
}
$field1 = Vtiger_Field::getInstance('containertypes_sealcost',$ModuleInstance);
if ($field1)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field1->id));
}
$field2 = Vtiger_Field::getInstance('containertypes_repaircost',$ModuleInstance);
if ($field2)
{
    $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field2->id));
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";