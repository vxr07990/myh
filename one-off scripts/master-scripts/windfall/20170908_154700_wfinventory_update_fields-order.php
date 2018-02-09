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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$modules = [
  'WFInventory' => 'order_id',
];

foreach($modules as $modulename => $fieldname) {
  $module = Vtiger_Module_Model::getInstance($modulename);

  if(!$module) {
    echo "$modulename does not exist";
    continue;
  }

  $field = Vtiger_Field::getInstance($fieldname,$module);

  if(!$field) {
    echo "$fieldname does not exist";
    continue;
  }

  $sequence = $field->sequence;
  $blockid = $db->getOne("SELECT `block` FROM `vtiger_field` WHERE `fieldid` = $field->id");
  $block = Vtiger_Block::getInstance($blockid);

  $field->delete();

  $field = new Vtiger_Field();
  $field->label = 'LBL_' . strtoupper($modulename) . '_ORDER';
  $field->name = $fieldname;
  $field->table = 'vtiger_' . strtolower($modulename);
  $field->column = $fieldname;
  $field->columntype = 'VARCHAR(100)';
  $field->uitype = 10;
  $field->typeofdata = 'V~M';
  $field->sequence = $sequence;
  $block->addField($field);

  $field->setRelatedModules(['WFOrders']);

  $field = Vtiger_Field::getInstance('inventory_number',$module);

  if($field) {
    $module->setEntityIdentifier($field);
  }
}
