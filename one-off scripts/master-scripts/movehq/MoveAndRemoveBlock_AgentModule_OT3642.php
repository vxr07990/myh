<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

$AgentsInstance = Vtiger_Module::getInstance('Agents');
$blockRemove = Vtiger_Block::getInstance('LBL_AGENTS_FIELDS_TO_REMOVE',$AgentsInstance);
if ($blockRemove){
    $params = array();
    $params[] = 1;

    $sql = "SELECT *
            FROM `vtiger_field`
            WHERE `vtiger_field`.`block` = ?";
    $result = $adb->pquery($sql,array($blockRemove->id));
    if ($adb->num_rows($result)>0){
        $fields = array();
        while ($data = $adb->fetch_array($result)){
            $fields[] = $data['fieldid'];
            $params[] = $data['fieldid'];
        }
        $generateQuestionMarks = generateQuestionMarks($fields);

        if (count($generateQuestionMarks)>0){
            $sql = "UPDATE `vtiger_field` 
                SET `vtiger_field`.`presence` = ? 
                WHERE `vtiger_field`.`fieldid` IN ($generateQuestionMarks)";
            $adb->pquery($sql,$params);
        }
    }
    echo "<li>LBL_AGENTS_FIELDS_TO_REMOVE have already remove done</li>";
}

$block1 = Vtiger_Block::getInstance('LBL_AGENTS_RECORDUPDATE',$AgentsInstance);
$block2 = Vtiger_Block::getInstance('LBL_AGENTS_WAREINFO',$AgentsInstance);

if ($block1 && $block2){
    if ($block1->sequence < $block2->sequence){

        $newSequenceBlock1 = $block2->sequence;
        $newSequenceBlock2 = $block1->sequence;

        $updateBlock1 = "UPDATE `vtiger_blocks` SET `sequence` = ? WHERE blockid = ?";
        $adb->pquery($updateBlock1, array($newSequenceBlock1, $block1->id));

        $updateBlock2 = "UPDATE `vtiger_blocks` SET `sequence` = ? WHERE blockid = ?";
        $adb->pquery($updateBlock2, array($newSequenceBlock2, $block2->id));

        echo "<li>Order sequence block 'Record Update Information' beneath 'Warehouse Information'</li>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";