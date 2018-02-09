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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;


$create = [
    'RecordProtection' => [
        'LBL_RECORDPROTECTION_DETAILS' => [
            'LBL_RECORDPROTECTION_MODULE_NAME' => [
                'name' => 'module_name',
                'table' => 'vtiger_recordprotection',
                'column' => 'module_name',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 16,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'summaryfield' => 1,
                'filterSequence' => 1,
                'entityIdentifier' => 1,
            ],
            'LBL_RECORDPROTECTION_FLAG_NAME' => [
                'name' => 'flag_name',
                'table' => 'vtiger_recordprotection',
                'column' => 'flag_name',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 16,
                'typeofdata' => 'V~M',
                'sequence' => 2,
                'filterSequence' => 2,
            ],
            'LBL_RECORDPROTECTION_ENABLED' => [
                'name' => 'enabled',
                'table' => 'vtiger_recordprotection',
                'column' => 'enabled',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'V~O',
                'sequence' => 3,
                'filterSequence' => 3,
            ],
            'LBL_RECORDPROTECTION_AGENT' => [
                'name' => 'agentid',
                'table' => 'vtiger_recordprotection',
                'column' => 'agentid',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'sequence' => 4,
                'setRelatedModules' => ['AgentManager'],
                'filterSequence' => 4,
            ],
            'LBL_RECORDPROTECTION_PROCESS_STATE' => [
                'name' => 'process_state',
                'table' => 'vtiger_recordprotection',
                'column' => 'process_state',
                'columntype' => 'TINYINT(1)',
                'uitype' => 1,
                'typeofdata' => 'N~O',
                'sequence' => 5,
                'filterSequence' => 5,
                'displaytype' => 3,
            ],
        ],
        'LBL_RECORDUPDATEINFORMATION' => [
            'LBL_CREATED_TIME'       => [
                'name'                => 'createdtime',
                'columntype'          => 'datetime',
                'uitype'              => 70,
                'column'              => 'createdtime',
                'typeofdata'          => 'T~O',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
            ],
            'LBL_MODIFIED_TIME'      => [
                'name'                => 'modifiedtime',
                'columntype'          => 'datetime',
                'column'              => 'modifiedtime',
                'uitype'              => 70,
                'typeofdata'          => 'T~O',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
            ],
            'LBL_CREATED_BY'         => [
                'name'                => 'smcreatorid',
                'columntype'          => 'int(19)',
                'uitype'              => 52,
                'typeofdata'          => 'V~O',
                'column'              => 'smcreatorid',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
            ],
            'LBL_ASSIGNED_USER_ID'  => [
                'name'                => 'smownerid',
                'columntype'          => 'int(19)',
                'uitype'              => 53,
                'typeofdata'          => 'V~M',
                'column'              => 'smownerid',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
            ],
        ],
    ],
];

multicreate($create);



//Set module list for CRM Settings

//Add new block for user-accessible config modules so that they aren't all grouped under Other Settings
$blockLabel = 'LBL_CONFIG_MODULES';

$query = "SELECT * FROM `vtiger_settings_blocks` WHERE label=?";
$res = $adb->pquery($query, [$blockLabel]);
if($res && $adb->num_rows($res) == 0) {
    $query = "INSERT INTO `vtiger_settings_blocks` VALUES (?,?,?)";
    $blockId = $adb->getUniqueID('vtiger_settings_blocks');
    $adb->pquery($query, [$blockId, $blockLabel, $blockId]);
} elseif ($res) {
    $blockId = $res->fields['blockid'];
}

if($blockId) {
    $moduleList = [
        'AgentManager',
        'VanlineManager',
        'TariffManager',
        'MenuCreator',
        'PicklistCustomizer',
        'RecordProtection',
        'Webforms',
        'Workflows',
        'CapacityCalendarCounter',
        'ItemCodes',
        'OPList',
        'ZoneAdmin',
    ];



    foreach($moduleList as $sequence => $moduleName) {
        $query = "SELECT * FROM `vtiger_settings_field` WHERE `name`=?";
        $res   = $adb->pquery($query, [$moduleName]);
        unset($query, $param);
        if($res && $adb->num_rows($res) == 0) {
            //Insert new row
            $field = $adb->getUniqueID('vtiger_settings_field');
            $query = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`, `pinned`) VALUES (?,?,?,?,?,?,?,?,?)";
            $param = [$field, $blockId, $moduleName, '', $moduleName, 'index.php?module='.$moduleName.($moduleName == 'PicklistCustomizer' ? '&view=Edit' : '&view=List'), $sequence, 0, 0];
        } elseif ($res) {
            //Update existing row
            $query = "UPDATE `vtiger_settings_field` SET blockid=?, sequence=?, description=? WHERE `name`=?";
            $param = [$blockId, $sequence, $moduleName, $moduleName];
        }
        if($query) {
            $adb->pquery($query, $param);
        }
    }
}

