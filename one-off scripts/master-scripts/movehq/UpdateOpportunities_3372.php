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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('Opportunities');
{

    //Delete the HHG Information Block.
    $blockInstance = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_HHG_INFORMATION', $moduleInstance);
    if ($blockInstance){
        $adb->pquery("DELETE FROM `vtiger_blocks` WHERE `blockid` = ? AND `tabid` = ?", array($blockInstance->id,$moduleInstance->id));
    }
    // Move the field 'Competitive (Yes or No) to the Opportunity Details Block.
    $fieldCompetitive = Vtiger_Field::getInstance('is_competitive', $moduleInstance);
    $blockInstance1 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleInstance);
    if ($fieldCompetitive) {
        //update is_competitive field
        $adb->pquery("UPDATE `vtiger_field` SET `uitype`='16' WHERE `fieldid`=?",array($fieldCompetitive->id));
        $fieldCompetitive->setPicklistValues(array('Yes','No'));

        if ($fieldCompetitive->getBlockId() != $blockInstance1->id) {
            $adb->pquery("update `vtiger_field` set `block`=? where `fieldid`=? ;", array($blockInstance1->id, $fieldCompetitive->id));
        }
    }
    //remove the field customer_service_coordinator
    $field1 = Vtiger_Field::getInstance('customer_service_coordinator', $moduleInstance);
    if($field1){
        $adb->pquery("DELETE FROM `vtiger_field` WHERE `fieldid` = ? AND `tabid` = ?", array($field1->id,$moduleInstance->id));
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";