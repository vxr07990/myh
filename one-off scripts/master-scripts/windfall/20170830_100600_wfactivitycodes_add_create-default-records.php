<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$module = Vtiger_Module_Model::getInstance('WFActivityCodes');

$check = Vtiger_Utils::ExecuteQuery("SHOW COLUMNS FROM `vtiger_wfactivitycodes` LIKE '%default%';");

if($check === NULL) {
  $block = Vtiger_Block::getInstance('LBL_WFACTIVITYCODES_DETAILS',$module);
  if(!$block) {
    return;
  }
  $field = new Vtiger_Field();
  $field->label = 'LBL_WFACTIVITYCODES_IS_DEFAULT';
  $field->name = 'is_default';
  $field->table = 'vtiger_wfactivitycodes';
  $field->column = 'is_default';
  $field->columntype = 'INT(1)';
  $field->uitype = 7;
  $field->typeofdata = 'N~O';
  $field->displaytype = 3;
  $field->presence = 0;
  $block->addField($field);

  $actions = [
    'IN' => 'Inventory In',
    'OUT' => 'Inventory Out',
    'MOVE' => 'Inventory Move',
    'VMOVE' => 'Vault Move',
    'VOUT' => 'Vault Out',
    'CIN' => 'Component In',
    'COUT' => 'Component Out',
    'CMOVE' => 'Component Move',
    'PMOVE' => 'Pallet Move',
    'POUT' => 'Pallet Out',
    'MOVEUPDATE' => 'Inventory Move Update',
    'OUTUPDATE' => 'Inventory Out Update',
  ];

  foreach($actions as $short=>$long) {
    $activity = Vtiger_Record_Model::getCleanInstance('WFActivityCodes');
    $activity->set('shortdescription',$short);
    $activity->set('longdescription',$long);
    $activity->set('sync',1);
    $activity->set('is_default',1);
    $activity->set('assigned_user_id',1);
    $activity->set('agentid',1);
    $activity->set('smcreatorid',1);
    $activity->set('createdtime',date('Y-m-d H:i:s'));
    $activity->set('modifiedtime',date('Y-m-d H:i:s'));
    $activity->save();
  }
}
