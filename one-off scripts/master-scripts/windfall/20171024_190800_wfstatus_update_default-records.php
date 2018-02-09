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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();

$codes = [
  "PPU" => "Pending Pick Up",
  "PD" => "Pending Delivery",
  "R" => "Reserved",
  "IU" => "In Use",
  "IS" => "In Storage",
  "OP" => "Off Property"
];

foreach($codes as $short=>$long) {
    $status = Vtiger_Record_Model::getCleanInstance('WFStatus');
    $status->set('wfstatus_code',$short);
    $status->set('wfstatus_description',$long);
    $status->set('is_default',1);
    $status->set('assigned_user_id',1);
    $status->set('agentid',1);
    $status->set('smcreatorid',1);
    $status->set('createdtime',date('Y-m-d H:i:s'));
    $status->set('modifiedtime',date('Y-m-d H:i:s'));
    $status->save();
}

$inventory_items = $db->pquery('SELECT * FROM `vtiger_wfinventory` WHERE `status` IS NOT NULL;');

while($row = $inventory_items->fetchRow()) {
  $id = $row['wfinventoryid'];
  $old_status = $row['wfstatus'];

  $old_status = $db->pquery('SELECT * FROM `vtiger_wfstatus` WHERE `wfstatusid` = ?',[$old_status]);
  $old_status = $old_status->getRow();

  $new_status = $db->pquery('SELECT MAX(`wfstatusid`) FROM `vtiger_wfstatus` WHERE `wfstatus_code` = ?',[$old_status['wfstatus_code']]);

  $db->pquery('UPDATE `vtiger_wfinventory` SET `status` = ? WHERE `wfinventoryid` = ?',[$new_status->getOne(),$row['wfinventoryid']]);
}

$bad_statuses = $db->pquery("
  SELECT * FROM `vtiger_wfstatus`
  LEFT JOIN `vtiger_crmentity` on `vtiger_wfstatus`.`wfstatusid` = `vtiger_crmentity`.`crmid`
  WHERE `vtiger_wfstatus`.`is_default` = ? AND `vtiger_crmentity`.`agentid` != ?", [1,1]);

while($row = $bad_statuses->fetchRow()) {
  $record = Vtiger_Record_Model::getInstanceById($row['wfstatusid'],'WFStatus');
  if($record) {
    $record->delete();
  }
}
