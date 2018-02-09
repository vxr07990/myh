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

$module = Vtiger_Module_Model::getInstance('WFArticles');
$field = Vtiger_Field::getInstance('article_status',$module);

$filters = [
  'All' => [
    'fields' => [
      'wfaccount' => 1,
      'article_num' => 2,
      'category' => 3,
      'type' => 4,
      'description' => 5,
      'manufacturer' => 6,
      'part_num' => 7,
      'depth' => 8,
      'height' => 9,
      'sq_ft' => 10,
    ],
  ],
  'Active Articles' => [
    'fields' => [
      'wfaccount' => 1,
      'article_num' => 2,
      'category' => 3,
      'type' => 4,
      'description' => 5,
      'manufacturer' => 6,
      'part_num' => 7,
      'depth' => 8,
      'height' => 9,
      'sq_ft' => 10,
    ],
    'rules' => [
      'field' => $field,
      'condition' => 'EQUALS',
      'value' => 'Inactive'
    ],
    'default' => true,
  ],
  'Inactive Articles' => [
    'fields' => [
      'wfaccount' => 1,
      'article_num' => 2,
      'category' => 3,
      'type' => 4,
      'description' => 5,
      'manufacturer' => 6,
      'part_num' => 7,
      'depth' => 8,
      'height' => 9,
      'sq_ft' => 10,
    ],
    'rules' => [
      'field' => $field,
      'condition' => 'EQUALS',
      'value' => 'Inactive'
    ],
  ],
];

foreach($filters as $filterName=>$filterAtts) {
  $filter = Vtiger_Filter::getInstance($filterName, $module);
  if($filter) {
    $filter->delete();
  }

  $filter = new Vtiger_Filter();
  $filter->name = $filterName;

  if(isset($filterAtts['default']) && $filterAtts['default']) {
    $filter->isdefault = true;
  }

  $module->addFilter($filter);

  if(isset($filterAtts['rules'])) {
    $filter->addRule($filterAtts['rules']['field'],$filterAtts['rules']['condition'],$filterAtts['rules']['value']);
  }
  foreach($filterAtts['fields'] as $fieldName=>$seq) {
    $field = Vtiger_Field::getInstance($fieldName,$module);
    if($field) {
      $filter->addField($field,$seq);
    }
  }
}
