<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

$module = Vtiger_Module_Model::getInstance('WFConditions');

$create = [
    'WFConditions' => [
        'LBL_WFCONDITIONS_DETAILS' => [
            'LBL_WFCONDITIONS_ACCOUNT' => [
                'name' => 'wfconditions_account',
                'table' => 'vtiger_wfconditions',
                'column' => 'wfconditions_account',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'sequence' => 2,
                'summaryfield' => 1,
                'setRelatedModules' => ['WFAccounts'],
                'filterSequence' => 2,
            ],
            'LBL_WFCONDITIONS_DESCRIPTION' => [
                'name' => 'description',
                'table' => 'vtiger_wfconditions',
                'column' => 'description',
                'columntype' => 'TEXT',
                'uitype' => 19,
                'typeofdata' => 'V~M',
                'sequence' => 3,
                'summaryfield' => 1,
                'filterSequence' => 3,
            ],
            'LBL_WFCONDITIONS_ABBREVIATION' => [
                'name' => 'abbreviation',
                'table' => 'vtiger_wfconditions',
                'column' => 'abbreviation',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 1,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'summaryfield' => 1,
                'filterSequence' => 1,
                'entityIdentifier' => 1,
            ],
            'LBL_WFCONDITIONS_ASSIGNED_USER_ID' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'V~M',
                'sequence' => 4,
                'summaryfield' => 1,
                'filterSequence' => 4,
            ],
            'LBL_AGENT_OWNER' => [
                'name' => 'agentid',
                'table' => 'vtiger_crmentity',
                'column' => 'agentid',
                'uitype' => 1002,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'V~M',
                'sequence' => 5,
                'summaryfield' => 1,
                'filterSequence' => 5,
            ],
            'LBL_DATECREATED' => [
                'name' => 'createdtime',
                'table' => 'vtiger_crmentity',
                'column' => 'createdtime',
                'uitype' => 70,
                'columntype' => 'VARCHAR(100)',
                'typeofdata' => 'DT~O',
                'sequence' => 6,
                'summaryfield' => 1,
                'displaytype' => 2,
                'filterSequence' => 6,
            ],
            'LBL_IS_DEFAULT' => [
                'name' => 'is_default',
                'table' => 'vtiger_wfconditions',
                'column' => 'is_default',
                'uitype' => 70,
                'columntype' => 'VARCHAR(3)',
                'typeofdata' => 'V~O',
                'sequence' => 7,
                'summaryfield' => 1,
                'displaytype' => 2,
                'filterSequence' => 7,
            ],

        ],
    ],
];

multicreate($create);

// Reorder Fields
$orderOfFields = ['abbreviation','wfconditions_account', 'description', 'agentid', 'assigned_user_id', 'createdtime', 'is_default'];


$filter = Vtiger_Filter::getInstance('All', $module);

if($filter){
    $filter->delete();
}


$filter = new Vtiger_Filter();
$filter->name = 'All';
$filter->isdefault = true;
$module->addFilter($filter);

$db = PearDatabase::getInstance();

$count = 0;
foreach ($orderOfFields as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $count++;
        $params = [$count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        $filter->addField($field, $count);
        echo '<p>UPDATED '.$val.' to the sequence</p>';
    } else {
        echo '<p>'.$val.' Field doesn\'t exists</p>';
    }
}
