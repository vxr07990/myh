<?php
if (function_exists("call_ms_function_ver")) {
    $version = 5;
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

$entity = new CRMEntity();
$entity->setModuleSeqNumber('configure', $moduleInstance->name, 'IH', 1);

$create = [
  'WFInventoryHistory' => [
    'LBL_WFINVENTORYHISTORY_DETAILS' => [
      'LBL_DATECREATED' => [
        'name' => 'createdtime',
        'table' => 'vtiger_crmentity',
        'column' => 'createdtime',
        'uitype' => 70,
        'typeofdata' => 'DT~O',
        'sequence' => 2,
        'summaryfield' => 1,
        'displaytype' => 2,
        'filterSequence' => 1,
      ],
      'LBL_WFINVENTORYHISTORY_ASSIGNED_TO' => [
        'name' => 'assigned_user_id',
        'table' => 'vtiger_crmentity',
        'column' => 'smownerid',
        'uitype' => 53,
        'typeofdata' => 'V~O',
        'sequence' => 3,
        'summaryfield' => 1,
        'filterSequence' => 2,
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
        'filterSequence' => 3,
      ],
      'LBL_WFINVENTORYHISTORY_FROM_CUSTOMER' => [
        'name' => 'from_customer',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'from_customer',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 5,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFAccounts'],
        'filterSequence' => 4,
      ],
      'LBL_WFINVENTORYHISTORY_FROM_WAREHOUSE' => [
        'name' => 'from_warehouse',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'from_warehouse',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 7,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFWarehouses'],
        'filterSequence' => 5,
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
        'filterSequence' => 6,
      ],
      'LBL_WFINVENTORYHISTORY_FROM_SLOT' => [
        'name' => 'from_slot',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'from_slot',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 11,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFSlotConfiguration'],
        'filterSequence' => 7,
      ],
      'LBL_WFINVENTORYHISTORY_TO_CUSTOMER' => [
        'name' => 'to_customer',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'to_customer',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 6,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFAccounts'],
        'filterSequence' => 8,
      ],
      'LBL_WFINVENTORYHISTORY_TO_WAREHOUSE' => [
        'name' => 'to_warehouse',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'to_warehouse',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 8,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFWarehouses'],
        'filterSequence' => 9,
      ],
      'LBL_WFINVENTORYHISTORY_TO_LOCATION' => [
        'name' => 'to_location',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'to_location',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 10,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFLocations'],
        'filterSequence' => 10,
      ],
      'LBL_WFINVENTORYHISTORY_TO_SLOT' => [
        'name' => 'to_slot',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'to_slot',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 12,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFSlotConfiguration'],
        'filterSequence' => 11,
      ],
      'LBL_WFINVENTORYHISTORY_QUANTITY' => [
        'name' => 'quantity',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'quantity',
        'columntype' => 'INT(10)',
        'uitype' => 7,
        'typeofdata' => 'I~O',
        'summaryfield' => 1,
        'sequence' => 13,
        'filterSequence' => 12,
      ],
      'LBL_WFINVENTORYHISTORY_OUT_TO' => [
        'name' => 'out_to',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'out_to',
        'columntype' => 'TEXT',
        'uitype' => 19,
        'typeofdata' => 'V~O',
        'summaryfield' => 1,
        'sequence' => 14,
        'filterSequence' => 13,
      ],
      'LBL_WFINVENTORYHISTORY_ORDER' => [
        'name' => 'order',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'order',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 15,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFOrders'],
        'filterSequence' => 14,
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
        'filterSequence' => 15,
      ],
      'LBL_WFINVENTORYHISTORY_SOURCE' => [
        'name' => 'wfinventoryhistory_source',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'wfinventoryhistory_source',
        'columntype' => 'varchar(100)',
        'uitype' => 16,
        'typeofdata' => 'V~O',
        'summaryfield' => 1,
        'sequence' => 17,
        'setPicklistValues' => ['mobile', 'movehq'],
        'filterSequence' => 16,
      ],
      'LBL_WFINVENTORYHISTORY_INVENTORY' => [
        'name' => 'inventory',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'inventory',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 18,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFInventory'],
        'filterSequence' => 17,
      ],
      'LBL_WFINVENTORYHISTORY_INVENTORY' => [
        'name' => 'inventory',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'inventory',
        'columntype' => 'varchar(100)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'summaryfield' => 1,
        'setRelatedModules' => ['WFInventory'],
      ],
      'LBL_WFINVENTORYHISTORY_COMMENT' => [
        'name' => 'comments',
        'table' => 'vtiger_wfinventoryhistory',
        'column' => 'comments',
        'columntype' => 'varchar(255)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'summaryfield' => 1,
      ],
      'LBL_WFINVENTORYHISTORY_ASSIGNED_USER_ID' => [
        'name'       => 'assigned_user_id',
        'table'      => 'vtiger_crmentity',
        'column'     => 'smownerid',
        'uitype'     => 53,
        'typeofdata' => 'V~M',
      ],
    ],
  ],
];

multicreate($create);

// Add WFInventory related list
$moduleInstance = Vtiger_Module::getInstance('WFInventoryHistory');
$InventoryInstance = Vtiger_Module::getInstance('WFInventory');
$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? AND `name`=?", array($InventoryInstance->id, $moduleInstance->id, 'get_related_list'));
if ($result && $adb->num_rows($result) == 0) {
    $InventoryInstance->setRelatedList(Vtiger_Module::getInstance('WFInventoryHistory'), 'WFInventoryHistory', array(''), 'get_related_list');
}
