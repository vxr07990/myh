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

$moduleInstance = Vtiger_Module::getInstance('Orders');
if($moduleInstance){
    $field1 = Vtiger_Field::getInstance('account_contract', $moduleInstance);
    if ($field1){
        $rs = $adb->pquery("SELECT * FROM `vtiger_field` WHERE `fieldid`=?",array($field1->id));
        if ($adb->num_rows($rs) >0){
            $sequence = $adb->query_result($rs,0,'sequence');
            $sequence = intval($sequence) + 1;
        }
    }
    $accountNumber = Vtiger_Field::getInstance('national_account_number', $moduleInstance);
    $blockInstance = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleInstance);
    if ($blockInstance && $accountNumber){
        $adb->pquery("UPDATE `vtiger_field` SET `block`=?,`sequence`=? WHERE `fieldid`=?",array($blockInstance->id,$sequence,$accountNumber->id));
    }
    //remove Account Detail block
    $blockAccount = Vtiger_Block::getInstance('LBL_ORDER_ACCOUNT_ADDRESS', $moduleInstance);
    if ($blockAccount){
        $fieldAccBlock = $adb->pquery("SELECT * FROM `vtiger_field` WHERE `block`=?",array($blockAccount->id));
        if ($adb->num_rows($fieldAccBlock) > 0 ){
            $listField = [];
            while ($row = $adb->fetchByAssoc($fieldAccBlock)) {
                $listField[] = $row['fieldid'];
            }
            foreach ($listField as $val){
                $adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldid`=? ", array( $val));
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";