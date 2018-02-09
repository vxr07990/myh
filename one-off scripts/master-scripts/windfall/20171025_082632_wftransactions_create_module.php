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

$moduleName = 'WFTransactions';

$create = [
    $moduleName => [
        'LBL_'.strtoupper($moduleName).'_INVENTORY_INFORMATION' => [
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('transaction_no') => [
                'name' => 'transaction_no',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'transaction_no',
                'uitype' => 4,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(55)',
                'sequence' => 'auto',
                'entityIdentifier' => 1
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_account') => [
                'name' => 'rel_account',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_account',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 4,
                'setRelatedModules' => ['Accounts']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_order') => [
                'name' => 'rel_order',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_order',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 5,
                'setRelatedModules' => ['Orders']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_inventory_number') => [
                'name' => 'rel_inventory_number',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_inventory_number',
                'uitype' => 1,
                'typeofdata' => 'V~M',
                'columntype' => 'varchar(255)',
                'sequence' => 'auto',
                'filterSequence' => 6
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('transaction_tag_color') => [
                'name' => 'transaction_tag_color',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'transaction_tag_color',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'varchar(25)',
                'sequence' => 'auto',
                'setPicklistValues' => ['Blue', 'Green', 'Multi', 'None', 'Orange', 'Red', 'White', 'Yellow']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_article_number') => [
                'name' => 'rel_article_number',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_article_number',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 7,
                'setRelatedModules' => ['WFArticles']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_cost_center') => [
                'name' => 'rel_cost_center',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_cost_center',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'setRelatedModules' => ['WFCostCenters']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_item_condition') => [
                'name' => 'rel_item_condition',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_item_condition',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'setRelatedModules' => ['WFConditions']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_inventory_status') => [
                'name' => 'rel_inventory_status',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_inventory_status',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'setRelatedModules' => ['WFStatus']
            ],
        ],
        'LBL_'.strtoupper($moduleName).'_LOCATION_DETAILS' => [
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_warehouse') => [
                'name' => 'rel_warehouse',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_warehouse',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 8,
                'setRelatedModules' => ['WFWarehouses']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('quantity') => [
                'name' => 'quantity',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'quantity',
                'uitype' => 7,
                'typeofdata' => 'I~M',
                'columntype' => 'int(11)',
                'filterSequence' => 13,
                'sequence' => 'auto'
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_from_location') => [
                'name' => 'rel_from_location',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_from_location',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 11,
                'setRelatedModules' => ['WFLocations']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_to_location') => [
                'name' => 'rel_to_location',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_to_location',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 9,
                'setRelatedModules' => ['WFLocations']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_from_slot_configuration') => [
                'name' => 'rel_from_slot_configuration',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_from_slot_configuration',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'varchar(10)',
                'sequence' => 'auto',
                'filterSequence' => 12,
                //'setRelatedModules' => ['WFSlotConfiguration'],
                'setPicklistValues' => ['L', 'C', 'R', 'LC', 'CR', 'LCR']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_to_slot_configuration') => [
                'name' => 'rel_to_slot_configuration',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_to_slot_configuration',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'varchar(10)',
                'sequence' => 'auto',
                'filterSequence' => 10,
                //'setRelatedModules' => ['WFSlotConfiguration'],
                'setPicklistValues' => ['L', 'C', 'R', 'LC', 'CR', 'LCR']
            ],
        ],
        'LBL_'.strtoupper($moduleName).'_DETAILS' => [
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('activity_date') => [
                'name' => 'activity_date',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'activity_date',
                'uitype' => 5,
                'typeofdata' => 'D~M',
                'columntype' => 'date',
                'sequence' => 'auto',
                'filterSequence' => 2
            ],
            //@NOTE: There is another hotfix that tries to move this field here if it isn't.
            'LBL_ASSIGNED_USER_ID' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'typeofdata' => 'V~M',
                'sequence' => 'auto',
                'filterSequence' => 3
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_activity_code') => [
                'name' => 'rel_activity_code',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_activity_code',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 14,
                'setRelatedModules' => ['WFActivityCodes']
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('rel_operation_task') => [
                'name' => 'rel_operation_task',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'rel_operation_task',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'int(11)',
                'sequence' => 'auto',
                'filterSequence' => 15,
                'setRelatedModules' => ['WFOperationsTasks']
            ],
        ],
        'LBL_'.strtoupper($moduleName).'_PROBLEM_INFORMATION' => [
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('problem_code') => [
                'name' => 'problem_code',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'problem_code',
                'uitype' => 21,
                'typeofdata' => 'V~O',
                'columntype' => 'text',
                'readonly' => 0,
                'sequence' => 'auto',
                'filterSequence' => 1
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('error_state') => [
                'name' => 'error_state',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'error_state',
                'uitype' => 56,
                'typeofdata' => 'V~O',
                'columntype' => 'varchar(3)',
                'displaytype' => 3,
                'readonly' => 0,
                'sequence' => 'auto'
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
                'sequence' => 'auto',
            ],
            'LBL_MODIFIED_TIME' => [
                'name' => 'modifiedtime',
                'table' => 'vtiger_crmentity',
                'column' => 'modifiedtime',
                'columntype' => 'datetime',
                'uitype' => 70,
                'typeofdata' => 'DT~O',
                'displaytype' => 2,
                'sequence' => 'auto',
            ],
            'LBL_CREATED_BY' => [
                'name' => 'createdby',
                'table' => 'vtiger_crmentity',
                'column' => 'smcreatorid',
                'uitype' => 52,
                'typeofdata' => 'V~O',
                'displaytype' => 2,
                'sequence' => 'auto',
            ],
            'LBL_AGENT_OWNER' => [
                'name' => 'agentid',
                'table' => 'vtiger_crmentity',
                'column' => 'agentid',
                'uitype' => 1002,
                'typeofdata' => 'I~M',
                'sequence' => 'auto',
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('validate_only') => [
                'name' => 'validate_only',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'validate_only',
                'uitype' => 21,
                'typeofdata' => 'V~M',
                'columntype' => 'text',
                'readonly' => 0,
                'displaytype' => 2,
                'sequence' => 'auto'
            ],
            'LBL_'.strtoupper($moduleName).'_'.strtoupper('payload_in') => [
                'name' => 'payload_in',
                'table' => 'vtiger_'.strtolower($moduleName),
                'column' => 'payload_in',
                'uitype' => 21,
                'typeofdata' => 'V~M',
                'columntype' => 'text',
                'readonly' => 0,
                'displaytype' => 2,
                'sequence' => 'auto'
            ],
        ],
    ]
];

multicreate($create);


$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance) {
    $filter = Vtiger_Filter::getInstance('All', $moduleInstance);
    if ($filter) {
        $filter->delete();
    }
    $filter            = new Vtiger_Filter();
    $filter->name      = 'All';
    $filter->isdefault = true;
    $moduleInstance->addFilter($filter);
    foreach ($create as $module => $data) {
        foreach ($data as $blockLabel => $fields) {
            foreach ($fields as $fieldLabel => $fieldAttributes) {
                if (isset($fieldAttributes['filterSequence'])) {
                    $field = Vtiger_Field::getInstance($fieldAttributes['name'], $moduleInstance);
                    if ($field) {
                        $filter->addField($field, $fieldAttributes['filterSequence']);
                    }
                }
            }
        }
    }
}


$entity = new CRMEntity();
if (!$entity->isModuleSequenceConfigured($moduleName)) {
    $entity->setModuleSeqNumber('configure', $moduleName, 'TRAN', 1);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
