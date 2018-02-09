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
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFLocations');

//Clear current filters

$currentFilters = ['Active Locations', 'Inactive Locations', 'All', 'All Active Locations', 'All Inactive Locations'];

foreach($currentFilters as $currentFilter) {
    $filter = Vtiger_Filter::getInstance($currentFilter, $module);
    if ($filter) {
        $filter->delete();
    }
}


foreach(['Active Locations','Inactive Locations'] as $filterName) {
    $filter = new Vtiger_Filter();
    $filter->name = $filterName;
    if($filterName == 'Active Locations') {
        $filter->isdefault = true;
    }
    $module->addFilter($filter);

    $fieldList = ['wflocation_type',
                  'tag',
                  'wfslot_configuration',
                  'wflocation_base',
                  'squarefeet',
                  'offsite',
                  'reserved',
                  'wflocation_warehouse',
    ];
    foreach($fieldList as $seq=>$fieldName) {
        $field = Vtiger_Field_Model::getInstance($fieldName, $module);
        $filter->addField($field,$seq);
    }

    $field = Vtiger_Field_Model::getInstance('wflocations_status', $module);

    if($filterName == 'Active Locations') {
        $filter->addRule($field,'EQUALS','Active',0,1,'');
    } else {
        $filter->addRule($field,'EQUALS','Inactive',0,1,'');
    }
}


$filter = new Vtiger_Filter();
$filter->name = 'All';
$module->addFilter($filter);

$fieldList = ['wflocation_type',
              'tag',
              'wfslot_configuration',
              'wflocations_status',
              'wflocation_base',
              'squarefeet',
              'offsite',
              'reserved',
              'wflocation_warehouse',
];
foreach($fieldList as $seq=>$fieldName) {
    $field = Vtiger_Field_Model::getInstance($fieldName, $module);
    $filter->addField($field,$seq);
}
