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


$create = [
    'WFImages' => [
        'LBL_WFIMAGES_DETAILS' => [
            'LBL_WFIMAGES_INVENTORY' => [
                'name' => 'inventory_number',
                'table' => 'vtiger_wfimages',
                'column' => 'inventory_number',
                'columntype' => 'VARCHAR(255)',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'summaryfield' => 1,
                'setRelatedModules' => ['WFInventory'],
                'filterSequence' => 1,
            ],
            'LBL_WFIMAGES_IMAGENAME' => [
                'name' => 'imagename',
                'table' => 'vtiger_wfimages',
                'column' => 'imagename',
                'columntype' => 'VARCHAR(255)',
                'uitype' => 69,
                'typeofdata' => 'V~M',
                'sequence' => 2,
                'summaryfield' => 1,
                'filterSequence' => 2,
                'entityIdentifier' => 1,
            ],
            'LBL_WFCONDITIONS_ASSIGNED_USER_ID' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'V~M',
                'sequence' => 3,
                'summaryfield' => 1,
                'filterSequence' => 3,
            ],
            'LBL_AGENT_OWNER' => [
                'name' => 'agentid',
                'table' => 'vtiger_crmentity',
                'column' => 'agentid',
                'uitype' => 1002,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'V~M',
                'sequence' => 4,
                'summaryfield' => 1,
                'filterSequence' => 4,
            ],
            'LBL_DATECREATED' => [
                'name' => 'createdtime',
                'table' => 'vtiger_crmentity',
                'column' => 'createdtime',
                'uitype' => 70,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'DT~O',
                'sequence' => 5,
                'summaryfield' => 1,
                'displaytype' => 2,
                'filterSequence' => 5,
            ],
        ],
    ],
];

multicreate($create);

$WFImages = Vtiger_Module::getInstance(('WFImages'));

$relationshipModules = ['WFInventory', 'WFArticles'];

foreach($relationshipModules as $moduleName) {
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if ($moduleInstance) {
        $moduleInstance->setRelatedList($WFImages, 'Images', ['ADD', 'SELECT'], 'get_dependents_list');
    }
}

$parentLabel = 'COMPANY_ADMIN_TAB';
$db = PearDatabase::getInstance();
if ($db) {
    $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
    $db->pquery($stmt, [$parentLabel, $WFImages->id]);
} else {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='" . $parentLabel . "' WHERE tabid=" . $WFImages->id);
}
