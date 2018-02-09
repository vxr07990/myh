<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$params = array();
$params[] = '1';

$moduleName = "Estimates";
$fieldNeedUpdatePresence = array();
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance){
    $blockLabel = 'LBL_QUOTES_CONTACTDETAILS';
    $blockInstance = Vtiger_Block::getInstance($blockLabel,$moduleInstance);
    if ($blockInstance){
        foreach ($moduleInstance->getFields() as $field){
            if($field->block->id == $blockInstance->id){
                $params[] = $field->id;
                $fieldNeedUpdatePresence[] = $field->id;
            }
        }
    }

    if (count($fieldNeedUpdatePresence)>0){
        $sqlUpdate = "UPDATE `vtiger_field` SET `vtiger_field`.`presence` = ? WHERE `vtiger_field`.`fieldid` IN (".generateQuestionMarks($fieldNeedUpdatePresence).")";
        $adb->pquery($sqlUpdate, $params);
        echo "UPDATE `presence` of fields on Contact Details block in Estimates Module to 1";
    }

}

echo "<br>DONE!<br>";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";