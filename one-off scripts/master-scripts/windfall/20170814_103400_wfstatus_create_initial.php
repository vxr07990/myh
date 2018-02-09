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

$moduleInstance = Vtiger_Module_Model::getInstance('WFStatus');


$create = ['WFStatus' => [
    'LBL_WFSTATUS_DETAILS' => [

        'LBL_WFSTATUS_CODE' => [
            'name' => 'wfstatus_code',
            'table' => 'vtiger_wfstatus',
            'column' => 'wfstatus_code',
            'uitype' => 1,
            'typeofdata' => 'V~M',
            'columntype' => 'VARCHAR(100)',
            'entityIdentifier' => 1,
            'sequence' => 1,
            'filterSequence' => 1,
        ],
        'LBL_WFSTATUS_DESCRIPTION' => [
            'name' => 'wfstatus_description',
            'table' => 'vtiger_wfstatus',
            'column' => 'wfstatus_description',
            'uitype' => 19,
            'typeofdata' => 'V~M',
            'columntype' => 'TEXT',
            'sequence' => 2,
            'filterSequence' => 2,
        ],
        'LBL_WFLOCATIONTYPES_OWNER' => [
            'name' => 'agentid',
            'table' => 'vtiger_crmentity',
            'column' => 'agentid',
            'uitype' => 1002,
            'typeofdata' => 'I~M',
            'sequence' => 3,
        ],
        'LBL_WFSTATUS_ASSIGNED_USER_ID' => [
            'name' => 'assigned_user_id',
            'table' => 'vtiger_crmentity',
            'column' => 'smownerid',
            'uitype' => 53,
            'typeofdata' => 'V~M',
            'sequence' => 4,
        ],
        'LBL_WFSTATUS_IS_DEFAULT' => [
            'name' => 'is_default',
            'table' => 'vtiger_wfstatus',
            'column' => 'is_default',
            'uitype' => 56,
            'typeofdata' => 'V~O',
            'columntype' => 'VARCHAR(3)',
            'sequence' => 5,
            'displaytype' => 3,
        ],
    ],
    'LBL_RECORD_UPDATE_INFORMATION' => [
        'LBL_DATECREATED' => [
            'name' => 'createdtime',
            'table' => 'vtiger_crmentity',
            'column' => 'createdtime',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'DT~O',
            'displaytype' => 2,
        ],
        'LBL_MODIFIEDTIME' => [
            'name' => 'modifiedtime',
            'table' => 'vtiger_crmentity',
            'column' => 'createdtime',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'DT~O',
            'displaytype' => 2,
        ],
        'LBL_WFSTATUS_CREATEDBY' => [
            'name' => 'createdby',
            'table' => 'vtiger_crmentity',
            'column' => 'smcreatorid',
            'uitype' => 52,
            'typeofdata' => 'V~O',
            'displaytype' => 2,
        ],
    ],
]
];

multicreate($create);

$block = Vtiger_Block_Model::getInstance('LBL_WFSTATUS_DETAILS', $moduleInstance);

//Update sequence

$fieldSeq = [
    'wfstatus_code',
    'wfstatus_description',
    'agentid',
    'assigned_user_id',
];

$i = 0;

foreach($fieldSeq as $field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $moduleInstance);
    if ($fieldInstance) {
        $i++;
        $adb->pquery("UPDATE `vtiger_field` SET sequence = ? WHERE fieldname = ? AND block = ?", [$i, $field, $block->id]);
    }
}



