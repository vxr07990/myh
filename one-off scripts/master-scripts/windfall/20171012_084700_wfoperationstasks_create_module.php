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

$create = [
  'WFOperationsTasks' => [
    'LBL_WFOPERATIONSTASKS_TASKS_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_ORDERNUMBER' => [
        'name' => 'wforder',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'wforder',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 1,
        'setRelatedModules' => ['WFOrders'],
      ],
      'LBL_WFOPERATIONSTASKS_NUMBER' => [
        'name' => 'number',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'number',
        'columntype' => 'INT(150)',
        'uitype' => 4,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 2,
        'entityIdentifier' => 1,
      ],
      'LBL_WFOPERATIONSTASKS_ACCOUNT' => [
        'name' => 'account',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'account',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 3,
        'setRelatedModules' => ['WFAccounts'],
      ],
      'LBL_WFOPERATIONSTASKS_BUSINESS_LINE' => [
        'name' => 'business_line',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'business_line',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 4,
      ],
      'LBL_WFOPERATIONSTASKS_COMMODITIES' => [
        'name' => 'commodities',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'commodities',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 5,
      ],
      'LBL_WFOPERATIONSTASKS_TASK_TYPE' => [
        'name' => 'task_type',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'task_type',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 6,
        'setPicklistValues' => ['Warehouse','Local Operation'],
      ],
      'LBL_WFOPERATIONSTASKS_TASK_CODE' => [
        'name' => 'task_code',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'task_code',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 7,
        'setPicklistValues' => ['Out','Move','In','Cross Dock','Access'],
      ],
      'LBL_WFLOCATIONS_AGENTID' => [
        'name'              => 'agentid',
        'columntype'        => 'INT(19)',
        'uitype'            => 1002,
        'typeofdata'        => 'I~M',
        'table'             => 'vtiger_crmentity',
        'sequence' => 8,
      ],
      'LBL_ASSIGNED_USER_ID'  => [
        'name'                => 'smownerid',
        'columntype'          => 'int(19)',
        'uitype'              => 53,
        'typeofdata'          => 'I~M',
        'column'              => 'smownerid',
        'table'               => 'vtiger_crmentity',
        'sequence' => 9,
      ],
    ],
    'LBL_WFOPERATIONSTASKS_TASKINFORMATION_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_WAREHOUSE' => [
        'name' => 'warehouse',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'warehouse',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 1,
        'setRelatedModules' => ['WFWarehouses'],
      ],
      'LBL_WFOPERATIONSTASKS_STATUS' => [
        'name' => 'wfoperationstasks_status',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'wfoperationstasks_status',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 2,
        'setPicklistValues' => ['Created','Denied','Waiting','Submitted','Processing','Closed'],
      ],
      'LBL_WFOPERATIONSTASKS_SYNC' => [
        'name' => 'sync',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'sync',
        'columntype' => 'TINYINT(1)',
        'uitype' => 56,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 3,
      ],
      'LBL_WFOPERATIONSTASKS_TAG_COLOR' => [
        'name' => 'tag_color',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'tag_color',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 4,
        'setPicklistValues' => ['Blue','Green','Multi','None','Orange','Red','White','Yellow'],
      ],
      'LBL_WFOPERATIONSTASKS_WAREHOUSE_NOTES' => [
        'name' => 'notes',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'notes',
        'columntype' => 'TEXT',
        'uitype' => 19,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 5,
      ],
    ],
    'LBL_WFOPERATIONSTASKS_TASKDETAILS_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_PRIORITY' => [
        'name' => 'priority',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'priority',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 16,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 1,
        'setPicklistValues' => ['Urgent','High','Medium','Low'],
      ],
      'LBL_WFOPERATIONSTASKS_BOL_NUMBER' => [
        'name' => 'bol_num',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'bol_num',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 2,
      ],
      'LBL_WFOPERATIONSTASKS_PO_NUMBER' => [
        'name' => 'po_num',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'po_num',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 3,
      ],
      'LBL_WFOPERATIONSTASKS_JOB_NUMBER' => [
        'name' => 'job_number',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'job_number',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 4,
      ],
      'LBL_WFOPERATIONSTASKS_COSTCENTER' => [
        'name' => 'warehouse',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'warehouse',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 1,
        'setRelatedModules' => ['WFCostcenters'],
      ],
    ],
    'LBL_WFOPERATIONSTASKS_WEBDETAILS_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_APPROVED_BY' => [
        'name' => 'approved_by',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'approved_by',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'presence' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 1,
      ],
      'LBL_WFOPERATIONSTASKS_TAKEN_BY' => [
        'name' => 'taken_by',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'taken_by',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 2,
      ],
      'LBL_WFOPERATIONSTASK_REQUESTER_NAME' => [
        'name' => 'requester_name',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'requester_name',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 3,
      ],
      'LBL_WFOPERATIONSTASKS_REQUESTER_PHONE' => [
        'name' => 'requester_phone',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'requester_phone',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 4,
      ],
    ],
    'LBL_WFOPERATIONSTASKS_SHIPPINGINFORMATION_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_SERVICE_ADDRESS' => [
        'name' => 'service_address',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'service_address',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'presence' => 1,
        'sequence' => 1,
        'setRelatedModules' => ['ServiceAddresses'],
      ],
      'LBL_WFOPERATIONSTASKS_CONTACT' => [
        'name' => 'contact',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'contact',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 2,
      ],
      'LBL_WFOPERATIONSTASKS_PHONE' => [
        'name' => 'phone',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'phone',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 3,
      ],
      'LBL_WFOPERATIONSTASKS_ADDRESS' => [
        'name' => 'address',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'address',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 4,
      ],
      'LBL_WFOPERATIONSTASKS_ADDRESS_TWO' => [
        'name' => 'address_two',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'address_two',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 5,
      ],
      'LBL_WFOPERATIONSTASKS_CITY' => [
        'name' => 'city',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'city',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 6,
      ],
      'LBL_WFOPERATIONSTASKS_STATE' => [
        'name' => 'state',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'state',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 7,
      ],
      'LBL_WFOPERATIONSTASKS_COUNTRY' => [
        'name' => 'country',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'country',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 8,
      ],
      'LBL_WFOPERATIONSTASKS_ZIP' => [
        'name' => 'zip',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'zip',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 9,
      ],
      'LBL_WFOPERATIONSTASKS_SERVICE_PROVIDER' => [
        'name' => 'serviceprovider',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'serviceprovider',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~M',
        'quickcreate' => 0,
        'sequence' => 1,
        'setRelatedModules' => ['WFCostcenters'],
      ],
      'LBL_WFOPERATIONSTASKS_TRACKING_NUM' => [
        'name' => 'tracking_num',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'tracking_num',
        'columntype' => 'VARCHAR(150)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'quickcreate' => 0,
        'sequence' => 5,
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
            'column' => 'createdtime',
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
      ],
    ],
  ];

multicreate($create);

$module = Vtiger_Module::getInstance('WFOperationsTasks');

$module->setGuestBlocks('WFLineItems',['LBL_WFLINEITEMS_BLOCK']);

foreach(['All','Open Local Operation Tasks','Closed Local Operation Tasks'] as $filterName) {
  $filter = Vtiger_Filter::getInstance($filterName);
  if($filter) {
    continue;
  }
  $filter = new Vtiger_Filter();
  $fields = [
    'number',
    'order',
    'account',
    'warehouse',
    'createdtime',
    'requested_by',
    'taken_by',
    'priority'
  ];

  if($filterName == 'All') {
    array_splice($fields,2,0,'wfoperationstasks_status');
  } elseif ($filterName == 'Open Local Operation Tasks') {
    $filter->isdefault = true;
  }
  $filter->name = $filterName;
  $module->addFilter($filter);

  if($filterName == 'Open Local Operation Tasks') {
    $filter->addRule(Vtiger_Field::getInstance('wfoperationstasks_status',$module),'EQUALS','Created,Open,Processing');
  } elseif ($filterName == 'Closed Local Operation Tasks') {
    $filter->addRule(Vtiger_Field::getInstance('wfoperationstasks_status',$module),'EQUALS','Closed');
  }

  foreach($fields as $seq=>$fieldname) {
    $field = Vtiger_Field::getInstance($fieldname,$module);
    if(!$field) {
      continue;
    }
    $filter->addField($field, $seq);
  }
}
