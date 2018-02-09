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

$moduleInstance = Vtiger_Module_Model::getInstance('WFWeightHistory');

if($moduleInstance){
    return;
}

$create = ['WFWeightHistory' => [
    'LBL_WFWEIGHTHISTORY_DETAILS' => [
        'LBL_WFWEIGHTHISTORY_WEIGHT' => [
            'name' => 'wfweighthistory_weight',
            'table' => 'vtiger_wfweighthistory',
            'column' => 'wfweighthistory_weight',
            'columntype' => 'INT(19)',
            'uitype' => 2,
            'typeofdata' => 'I~O',
            'sequence' => 1,
            'filterSequence' => 1,
        ],
        'LBL_WFWEIGHTHISTORY_DATE' => [
            'name' => 'weight_date',
            'table' => 'vtiger_wfweighthistory',
            'column' => 'weight_date',
            'columntype' => 'DATE',
            'uitype' => 5,
            'sequence' => 2,
            'filterSequence' => 2,
            'typeofdata' => 'D~M',
            'entityIdentifier' => true,
        ],
        'LBL_WFLOCATIONHISTORY_ASSIGNEDTO' => [
            'name' => 'assigned_user_id',
            'table' => 'vtiger_crmentity',
            'column' => 'smownerid',
            'uitype' => 53,
            'typeofdata' => 'V~M',
            'sequence' => 3,
        ],
        'LBL_AGENT_OWNER' => [
            'name' => 'agentid',
            'table' => 'vtiger_crmentity',
            'column' => 'agentid',
            'uitype' => 1002,
            'columntype' => 'VARCHAR(100)',
            'typeofdata' => 'V~M',
            'sequence' => 4,
        ],

    ],
]
];

multicreate($create);
