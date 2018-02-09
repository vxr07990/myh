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

$moduleName = 'WFActivityFieldMaps';

$create = [$moduleName => [
    'LBL_'.strtoupper($moduleName).'_DETAILS' => [
        'LBL_'.strtoupper($moduleName).'_'.strtoupper('is_active') => [
            'name' => 'is_active',
            'table' => 'vtiger_'.strtolower($moduleName),
            'column' => 'is_active',
            'uitype' => 56,
            'typeofdata' => 'V~O',
            'columntype' => 'VARCHAR(3)',
            'sequence' => 1,
            'filterSequence' => 1,
            'entityIdentifier' => 1,
            'defaultvalue' => 1
        ],
        'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_activity_field') => [
            'name' => 'rel_activity_field',
            'table' => 'vtiger_'.strtolower($moduleName),
            'column' => 'rel_activity_field',
            'uitype' => 10,
            'typeofdata' => 'V~M',
            'columntype' => 'int(11)',
            'sequence' => 2,
            'filterSequence' => 2,
            'setRelatedModules' => ['WFActivityFields']
        ],
        'LBL_'.strtoupper($moduleName).'_'.strtoupper('related_module') => [
            'name' => 'related_module',
            'table' => 'vtiger_'.strtolower($moduleName),
            'column' => 'related_module',
            'uitype' => 1,
            'typeofdata' => 'V~M',
            'columntype' => 'VARCHAR(255)',
            'sequence' => 2,
            'filterSequence' => 2,
        ],
        'LBL_'.strtoupper($moduleName).'_'.strtoupper('related_module_field') => [
            'name' => 'related_module_field',
            'table' => 'vtiger_'.strtolower($moduleName),
            'column' => 'related_module_field',
            'uitype' => 1,
            'typeofdata' => 'V~M',
            'columntype' => 'VARCHAR(255)',
            'sequence' => 3,
            'filterSequence' => 3,
        ],
        'LBL_'.strtoupper($moduleName).'_'.strtoupper('perform_action') => [
            'name' => 'perform_action',
            'table' => 'vtiger_'.strtolower($moduleName),
            'column' => 'perform_action',
            'uitype' => 16,
            'typeofdata' => 'V~M',
            'columntype' => 'VARCHAR(50)',
            'sequence' => 4,
            'filterSequence' => 4,
            'setPicklistValues' => ['add','subtract','replace'],
            'defaultValue' => 'replace'
        ],
    ],
    'LBL_RECORD_UPDATE_INFORMATION' => [
        'LBL_CREATED_TIME' => [
            'name' => 'createdtime',
            'table' => 'vtiger_crmentity',
            'column' => 'createdtime',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'DT~O',
            'displaytype' => 2,
        ],
        'LBL_MODIFIED_TIME' => [
            'name' => 'modifiedtime',
            'table' => 'vtiger_crmentity',
            'column' => 'modifiedtime',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'DT~O',
            'displaytype' => 2,
        ],
        'LBL_CREATED_BY' => [
            'name' => 'createdby',
            'table' => 'vtiger_crmentity',
            'column' => 'smcreatorid',
            'uitype' => 52,
            'typeofdata' => 'V~O',
            'displaytype' => 2,
        ],
        'LBL_AGENT_OWNER' => [
            'name' => 'agentid',
            'table' => 'vtiger_crmentity',
            'column' => 'agentid',
            'uitype' => 1002,
            'typeofdata' => 'I~M',
            'sequence' => 3,
        ],
        'LBL_ASSIGNED_USER_ID' => [
            'name' => 'assigned_user_id',
            'table' => 'vtiger_crmentity',
            'column' => 'smownerid',
            'uitype' => 53,
            'typeofdata' => 'V~M',
            'sequence' => 4,
        ],
    ],
]
];

multicreate($create);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
