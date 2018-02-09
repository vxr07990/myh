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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;


$moduleName = 'Leads';
$ModuleInstance = Vtiger_Module_Model::getInstance($moduleName);

// Leads module exists
if ($ModuleInstance){

    $blockName = 'LBL_LEADS_INFORMATION';

    $block1 = Vtiger_Block::getInstance($blockName, $ModuleInstance);
    if ($block1){
        echo "$blockName have already exists";
        // update all fields in this block to presence is 1 and sequence is 20
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=1, `vtiger_field`.`sequence`=20 WHERE `vtiger_field`.`block`=?";
        $adb->pquery($sql,array($block1->id));
        echo "<li>update presence of all field in LBL_LEADS_INFORMATION to 1</li>";
    }else{
        $block1 = new Vtiger_Block();
        $block1->label = $blockName;
        $ModuleInstance->addBlock($block1);
    }


    $fieldName = 'business_line';
    $business_lineField = Vtiger_Field::getInstance($fieldName, $ModuleInstance);
    if($business_lineField){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$business_lineField->id));
    }

    $fieldName = 'business_line2';
    $business_line2Field = Vtiger_Field::getInstance($fieldName, $ModuleInstance);
    if($business_line2Field){

        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? , `vtiger_field`.`sequence`=?, `vtiger_field`.`typeofdata`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,1,'V~M',$business_line2Field->id));
        echo "<br> Field 'business_line2' is updated <br>";
    }

    $fieldName = 'leadsource';
    $leadsourceField = Vtiger_Field::getInstance($fieldName, $ModuleInstance);
    if ($leadsourceField) {
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=?, `vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,2,$leadsourceField->id));

        echo "<br> Field 'leadsource' is updated <br>";
    }

    $leadstatusField = Vtiger_Field::getInstance('leadstatus', $ModuleInstance);
    if ($leadstatusField) {
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=?,`vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,3,$leadstatusField->id));
        echo "<br> Field 'leadstatus' is updated <br>";
    }

    $errorField = Vtiger_Field::getInstance('reason', $ModuleInstance);
    if ($errorField){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(1,$errorField->id));
    }


    $reasonField = Vtiger_Field::getInstance('reason_cancelled', $ModuleInstance);
    if ($reasonField) {
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=?,`vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,4,$reasonField->id));
        echo "<br> Field 'reason_cancelled' is updated <br>";
    } else {
        $reasonField = new Vtiger_Field();
        $reasonField->label = 'LBL_LEADS_REASON_CANCELLED';
        $reasonField->name = 'reason_cancelled';
        $reasonField->table = 'vtiger_leadscf';
        $reasonField->column = 'reason_cancelled';
        $reasonField->columntype = 'VARCHAR(255)';
        $reasonField->uitype = 16;
        $reasonField->typeofdata = 'V~O';
        $reasonField->sequence = 4;
        $reasonField->setPicklistValues(array('value 1'));
//        $reasonField->displaytype = 3;
        $block1->addField($reasonField);

    }

    $query = "SHOW COLUMNS FROM `vtiger_reason_cancelled` LIKE 'agentmanager_id';";

    $result = $adb->pquery($query);
    if ($adb->num_rows($result)==0){
        $query = "ALTER TABLE `vtiger_reason_cancelled` ADD `agentmanager_id` INT(11)";
        $adb->pquery($query);
        echo "<br>Add column 'agentmanager_id' to table 'vtiger_reason_cancelled' done<br>";
    }else{
        echo "field agentmanager_id on table vtiger_reason_cancelled has already";
    }

    $relatedAccountField = Vtiger_Field::getInstance('related_account', $ModuleInstance);
    if ($relatedAccountField) {
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=?,`vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,5,$relatedAccountField->id));
        echo "<br> Field 'related_account' is updated <br>";
    }

    $agentidField = Vtiger_Field::getInstance('agentid', $ModuleInstance);
    if ($agentidField) {
        echo "<br> Field 'agentid' is already present <br>";
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=?,`vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,6,$agentidField->id));
        echo "<br> Field 'agentid' is updated <br>";
    }


    // check block Record Update Information
    $blockName = 'LBL_LEADS_RECORDUPDATE';
    $block2 = Vtiger_Block::getInstance($blockName, $ModuleInstance);
    if ($block2){
        echo "$blockName have already exists";
    }else{
        $block2 = new Vtiger_Block();
        $block2->label = $blockName;
        $ModuleInstance->addBlock($block2);
        echo "$blockName have created done";
    }

    // create fields
    // create field : Created Day

    $createdtimeField = Vtiger_Field::getInstance('createdtime', $ModuleInstance);
    if ($createdtimeField) {
        echo "<br> Field 'createdtime' is already present <br>";
        $moveToBlockId = $block2->id;
        if ($createdtimeField->getBlockId() != $moveToBlockId){
            $fieldId = $createdtimeField->id;
            $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`block`= ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($sql, array($moveToBlockId,$fieldId));
        }
        echo "Move field 'createdtime' to block LBL_LEADS_RECORDUPDATE";
    }

    $modifiedtimeField = Vtiger_Field::getInstance('modifiedtime', $ModuleInstance);
    if ($modifiedtimeField) {
        echo "<br> Field 'createdtime' is already present <br>";
        $moveToBlockId = $block2->id;
        if ($modifiedtimeField->getBlockId() != $moveToBlockId){
            $fieldId = $modifiedtimeField->id;
            $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`block`= ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($sql, array($moveToBlockId,$fieldId));
        }
        echo "Move field 'modifiedtime' to block LBL_LEADS_RECORDUPDATE";
    }

    $createdbyField = Vtiger_Field::getInstance('created_user_id', $ModuleInstance);
    if ($createdbyField) {
        echo "<br> Field 'createdtime' is already present <br>";
        $moveToBlockId = $block2->id;
        if ($createdbyField->getBlockId() != $moveToBlockId){
            $fieldId = $createdbyField->id;
            $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`block`= ? WHERE `vtiger_field`.`fieldid`=?";
            $adb->pquery($sql, array($moveToBlockId,$fieldId));
        }
        echo "Move field 'createdby' to block LBL_LEADS_RECORDUPDATE";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
