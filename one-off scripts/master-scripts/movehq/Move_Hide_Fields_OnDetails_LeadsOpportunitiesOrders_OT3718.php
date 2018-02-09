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

$moduleInstance = Vtiger_Module::getInstance('Opportunities');
$blockInstance = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION',$moduleInstance);
if ($blockInstance){
    $fieldname = 'assigned_user_id';
    $field = Vtiger_Field::getInstance($fieldname,$moduleInstance);
    if ($field){
        if ($field->getBlockId() !== $blockInstance->id){
            $updateField = "UPDATE `vtiger_field` SET `vtiger_field`.`block` = ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($updateField,array($blockInstance->id, $field->id));
            echo "<br>move field `assigned_user_id` to LBL_RECORD_UPDATE_INFORMATION block<br>";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";