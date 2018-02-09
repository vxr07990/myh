<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFLocations');
if(!$module) {
  echo "WFLocations does not exist";
  return;
}

$blocks = ['LBL_WFLOCATIONS_INFORMATION' =>
            [
              'tag' => 1,
              'wflocation_warehouse' => 2,
              'wflocation_type' => 3,
              'pre' => 4,
              'post' => 5,
              'name' => 6,
              'description' =>7,
              'wflocations_status' => 8,
              'wflocation_base' => 9,
            ],
            'LBL_WFLOCATIONS_DETAILS' =>
            [
              'create_multiple' => 0,
              'range_from' => 1,
              'range_to' => 2,
              'row' => 3,
              'row_to' => 4,
              'bay' => 5,
              'bay_to' => 6,
              'level' => 7,
              'level_to' => 8,
              'wfslot_configuration' => 9,
              'vault_capacity' => 10,
              'reserved' => 11,
              'offsite' => 12,
              'squarefeet' => 13,
              'cubefeet' => 14,
              'cost' => 15,
              'double_high' => 16,
              'agentid' => 17,
              'assigned_user_id' => 18,
            ],
          ];
foreach($blocks as $blockLabel=>$fields) {
  $blockInstance = Vtiger_Block::getInstance($blockLabel, $module);
  if($blockInstance) {
    foreach($fields as $fieldName=>$seq) {
      $field = Vtiger_Field_Model::getInstance($fieldName, $module);
      if($field) {
          Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockInstance->id, `sequence` = $seq WHERE `fieldid` = $field->id");
      }
    }
  }
}
$field = Vtiger_Field_Model::getInstance('slot', $module);
if($field) {
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `presence` = 1 WHERE `fieldid` = $field->id");
}
