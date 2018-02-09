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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module_Model::getInstance('WFInventory');

$filterNames = ['All', 'All Inventory'];

foreach ($filterNames as $filterName) {
    $filter = Vtiger_Filter::getInstance($filterName, $module);
    if ($filter) {
        $filter->delete(); //IF exists delete this filter to add the new columns
    }
}



$fields = [
  1 => 'order_id',
  2 => 'inventory_number',
  3 => 'article',
  4 => 'description',
  5 => 'width',
  6 => 'depth',
  7 => 'height',
  8 => 'weight',
  9 => 'sq_ft',
  11 => 'costcenter',
];


$filter = new Vtiger_Filter();
$filter->name = 'All Inventory';
$filter->isdefault = true;
$module->addFilter($filter);

foreach($fields as $seq=>$fieldname) {
  $field = Vtiger_Field::getInstance($fieldname,$module);
  $filter->addField($field,$seq);
}
