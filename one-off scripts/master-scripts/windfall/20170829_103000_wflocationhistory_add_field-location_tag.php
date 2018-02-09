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

global $adb;

$moduleInstance = Vtiger_Module_Model::getInstance('WFLocationHistory');
if(!$moduleInstance){
    return;
}

$create = ['WFLocationHistory' => [
        'LBL_WFLOCATIONHISTORY_DETAILS' => [
            'LBL_WFLOCATIONHISTORY_LOCATION_TAG' => [
                'name' => 'location_tag',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'location_tag',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ],
        ],
    ],
];

$fieldSeq = [
    'datetime',
    'location_tag',
    'location',
    'from_location',
    'to_location',
    'from_slot',
    'to_slot',
    'from_warehouse',
    'to_warehouse',
    'from_status',
    'to_status',
    'user',
    'assigned_user_id'
];

multicreate($create);
reorderFields('WFLocationHistory', $fieldSeq);
