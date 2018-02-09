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


$moduleInstance = Vtiger_Module_Model::getInstance('WFInventoryHistory');
foreach(['from_customer','to_customer','from_warehouse','to_warehouse','from_slot', 'to_slot', 'order', 'wfinventoryhistory_source', 'createdtime', 'out_to', 'comments'] as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
    if($field) {
        $field->delete();
    }
}
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_wfinventoryhistory` DROP COLUMN `from_customer`, DROP COLUMN `to_customer`, 
                            DROP COLUMN `from_warehouse`, DROP COLUMN `to_warehouse`, DROP COLUMN `from_slot`, DROP COLUMN `to_slot`, 
                            DROP COLUMN `order`, DROP COLUMN `wfinventoryhistory_source`, DROP COLUMN `createdtime`, DROP COLUMN `out_to`, DROP COLUMN `comments`;");

$filter = Vtiger_Filter::getInstance('All', $moduleInstance);

if($filter){
    $filter->delete();
    $filter = new Vtiger_Filter();
    $filter->name = 'All';
    $filter->isdefault = true;
    $moduleInstance->addFilter($filter);
}

$create = [
    'WFInventoryHistory' => [
        'LBL_WFINVENTORYHISTORY_DETAILS' => [
            'LBL_WFINVENTORYHISTORY_DATE' => [
                'name' => 'date',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'date',
                'columntype' => 'DATE',
                'uitype' => 5,
                'typeofdata' => 'D~O',
                'sequence' => 1,
                'resetFilter' => 1,
                'summaryfield' => 1,
                'filterSequence' => 1,
            ],
            'LBL_WFINVENTORYHISTORY_TIME' => [
                'name' => 'time',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'time',
                'columntype' => 'TIME',
                'uitype' => 14,
                'typeofdata' => 'T~O',
                'sequence' => 2,
                'summaryfield' => 1,
                'filterSequence' => 2,
            ],
            'LBL_WFINVENTORYHISTORY_ASSIGNED_TO' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'typeofdata' => 'V~O',
                'sequence' => 3,
                'summaryfield' => 1,
                'filterSequence' => 3,
            ],
            'LBL_WFINVENTORYHISTORY_ACTIVITY_CODE' => [
                'name' => 'activity_code',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'activity_code',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 4,
                'summaryfield' => 1,
                'filterSequence' => 4,
            ],
            'LBL_WFINVENTORYHISTORY_OUT_REF' => [
                'name' => 'out_ref',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'out_ref',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 16,
                'typeofdata' => 'V~O',
                'sequence' => 5,
                'summaryfield' => 1,
                'filterSequence' => 5,
                'setPicklistValues' => ['Placeholder', 'Values'],
            ],
            'LBL_WFINVENTORYHISTORY_REF_DETAIL' => [
                'name' => 'ref_detail',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'ref_detail',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 6,
                'summaryfield' => 1,
                'filterSequence' => 6,
            ],
            'LBL_WFINVENTORYHISTORY_TO_LOCATION' => [
                'name' => 'to_location',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'to_location',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'sequence' => 7,
                'summaryfield' => 1,
                'setRelatedModules' => ['WFLocations'],
                'filterSequence' => 7,
            ],
            'LBL_WFINVENTORYHISTORY_NEW_SLOT' => [
                'name' => 'new_slot',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'new_slot',
                'columntype' => 'varchar(100)',
                'uitype' => 16,
                'typeofdata' => 'V~O',
                'summaryfield' => 1,
                'sequence' => 8,
                'filterSequence' => 8,
                'setPicklistValues' => ['L', 'C', 'R', 'LC', 'CR', 'LCR'],
            ],
            'LBL_WFINVENTORYHISTORY_FROM_LOCATION' => [
                'name' => 'from_location',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'from_location',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'sequence' => 9,
                'summaryfield' => 1,
                'setRelatedModules' => ['WFLocations'],
                'filterSequence' => 9,
            ],
            'LBL_WFINVENTORYHISTORY_OLD_SLOT' => [
                'name' => 'old_slot',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'old_slot',
                'columntype' => 'varchar(100)',
                'uitype' => 16,
                'typeofdata' => 'V~O',
                'summaryfield' => 1,
                'sequence' => 10,
                'filterSequence' => 10,
                'setPicklistValues' => ['L', 'C', 'R', 'LC', 'CR', 'LCR'],
            ],
            'LBL_WFINVENTORYHISTORY_QUANTITY' => [
                'name' => 'quantity',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'quantity',
                'columntype' => 'INT(10)',
                'uitype' => 7,
                'typeofdata' => 'I~O~MIN=0',
                'summaryfield' => 1,
                'sequence' => 11,
                'filterSequence' => 11,
            ],
            'LBL_WFINVENTORYHISTORY_ORDERTASK' => [
                'name' => 'ordertask',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'ordertask',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'sequence' => 16,
                'summaryfield' => 1,
                'setRelatedModules' => ['WFWorkOrders'],
                'sequence' => 12,
                'filterSequence' => 12,
            ],
            'LBL_WFINVENTORYHISTORY_INVENTORY' => [
                'name' => 'inventory',
                'table' => 'vtiger_wfinventoryhistory',
                'column' => 'inventory',
                'columntype' => 'varchar(100)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'displaytype' => 3,
                'setRelatedModules' => ['WFInventory'],
            ],

        ],
    ],
];

multicreate($create);


$fieldSeq = [
    'date',
    'time',
    'assigned_user_id',
    'activity_code',
    'out_ref',
    'ref_detail',
    'to_location',
    'new_slot',
    'from_location',
    'old_slot',
    'quantity',
    'ordertask',
    'inventory'
];

$seq = 1;

foreach ($fieldSeq as $name) {
    if ($name && $field = Vtiger_Field::getInstance($name, $moduleInstance)) {
        $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
        $result = $db->pquery($sql, [$seq, $block->id]);
        if ($result) {
            while ($row = $result->fetchRow()) {
                $push_to_end[] = $row['fieldname'];
            }
        }
        $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ?';
        if($name == 'inventory'){
            $updateStmt .= ', `displaytype` = 3';
        }
        if($name == 'quantity'){
            $updateStmt .= ", `typeofdata` = 'I~O~MIN=0'";
        }
        $updateStmt .=' WHERE `fieldid` = ?';
        $db->pquery($updateStmt, [$seq++, $field->id]);
    }
    unset($field);
}
