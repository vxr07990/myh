<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}
print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFLocationHistory');
$filter = Vtiger_Filter::getInstance('All', $module);

if ($filter) {
    $filter->delete();
}

$filter            = new Vtiger_Filter();
$filter->name      = 'All';
$filter->isdefault = true;
$module->addFilter($filter);
$fields = ['location_tag'   => 0,
           'datetime'       => 1,
           'user'           => 2,
           'from_warehouse' => 3,
           'from_location'      => 4,
           'from_slot'      => 5,
           'to_location'    => 6,
           'to_slot'        => 7
           ];
foreach ($fields as $field => $seq) {
    $fieldInstance = Vtiger_Field_Model::getInstance($field, $module);
    if ($fieldInstance) {
        echo "Adding $field->name to filter";
        $filter->addField($fieldInstance, $seq);
    }
}
