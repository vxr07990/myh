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

$module = Vtiger_Module_Model::getInstance('WFOrders');
$field = Vtiger_Field::getInstance('warehouse_status',$module);

$filters = [
    'All' => [
        'fields' => [
            'wforder_number' => 1,
            'warehouse_status' => 2,
            'business_line' => 3,
            'commodities' => 4,
            'wforder_weight' => 5,
            'wforder_firstday' => 6,
        ],
    ],
    'In Storage Orders' => [
        'fields' => [
            'wforder_number' => 1,
            'business_line' => 2,
            'commodities' => 3,
            'wforder_weight' => 4,
            'wforder_firstday' => 5,
        ],
        'rules' => [
            'field' => $field,
            'condition' => 'EQUALS',
            'value' => 'In Storage'
        ],
        'default' => true,
    ],
    'Out of Storage Orders' => [
        'fields' => [
            'wforder_number' => 1,
            'business_line' => 2,
            'commodities' => 3,
            'wforder_weight' => 4,
            'wforder_firstday' => 5,
        ],
        'rules' => [
            'field' => $field,
            'condition' => 'EQUALS',
            'value' => 'Out of Storage'
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
