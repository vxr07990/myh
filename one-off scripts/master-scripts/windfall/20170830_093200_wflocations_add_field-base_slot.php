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

$moduleInstance = Vtiger_Module_Model::getInstance('WFLocations');
if(!$moduleInstance){
    return;
}

$create = ['WFLocations' => [
            'LBL_WFLOCATIONS_INFORMATION' => [
                'LBL_WFLOCATIONS_BASE_SLOT' => [
                    'name' => 'base_slot',
                    'table' => 'vtiger_wflocations',
                    'column' => 'base_slot',
                    'columntype' => 'VARCHAR(100)',
                    'uitype' => 3333,
                    'typeofdata' => 'V~M',
                ],
            ],
        ],
];


multicreate($create);
