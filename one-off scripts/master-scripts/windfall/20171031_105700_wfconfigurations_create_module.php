<?php
if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');


//Note - at this point, a link to WFAccounts already exists
$create = ['WFConfiguration' => [
  'LBL_WFCONFIGURATION_SETUP' => [
    'LBL_WFCONFIGURATION_UDF1' => [
      'name' => 'udf1_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf1_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF2' => [
      'name' => 'udf2_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf2_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF3' => [
      'name' => 'udf3_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf3_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF4' => [
      'name' => 'udf4_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf4_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF5' => [
      'name' => 'udf5_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf5_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF6' => [
      'name' => 'udf6_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf6_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF7' => [
      'name' => 'udf7_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf7_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF8' => [
      'name' => 'udf8_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf8_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF9' => [
      'name' => 'udf9_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf9_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF10' => [
      'name' => 'udf10_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf10_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF11' => [
      'name' => 'udf11_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf11_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF12' => [
      'name' => 'udf12_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf12_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF13' => [
      'name' => 'udf13_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf13_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF14' => [
      'name' => 'udf14_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf14_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF15' => [
      'name' => 'udf15_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf15_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF16' => [
      'name' => 'udf16_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf16_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF17' => [
      'name' => 'udf17_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf17_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF18' => [
      'name' => 'udf18_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf18_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF19' => [
      'name' => 'udf19_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf19_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
    'LBL_WFCONFIGURATION_UDF20' => [
      'name' => 'udf20_label',
      'table' => 'vtiger_wfconfiguration',
      'column' => 'udf20_label',
      'columntype' => 'varchar(100)',
      'presence' => 0,
      'uitype' => 1,
      'typeofdata' => 'V~O',
    ],
  ],
  'LBL_WFCONFIGURATION_DETAILS' => [
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
],
];

multicreate($create);

$accountTab = getTabId('WFAccounts');
$configTab = getTabId('WFConfiguration');

$module = Vtiger_Module::getInstance('WFConfiguration');
$field = Vtiger_Field::getInstance('wfaccount',$module);
if($field) {
  $module->setEntityIdentifier($field);
}

$accountModule = Vtiger_Module::getInstance('WFAccounts');
if (!$accountModule) {
    print "No Accounts module FAILED".PHP_EOL;
    return;
}
$accountModule->unsetRelatedList($module, 'Configuration', 'get_dependents_list');
$accountModule->setRelatedList($module, 'Configuration', ['SELECT'], 'get_dependents_list', 1);

$db = PearDatabase::getInstance();

$results = $db->pquery('SELECT * FROM `vtiger_wfaccounts` LEFT JOIN `vtiger_crmentity` ON `vtiger_wfaccounts`.`wfaccountsid` = `vtiger_crmentity`.`crmid` WHERE `deleted` = ?',[0]);

while($row = $results->fetchRow()) {
  $account = Vtiger_Record_Model::getInstanceById($row['wfaccountsid'],'WFAccounts');
  if($account) {
    $config = $db->pquery('SELECT * FROM `vtiger_wfconfiguration` WHERE `wfaccount` = ?',[$row['wfaccountsid']]);
    if($db->num_rows($config) == 0) {
      $configuration = Vtiger_Record_Model::getCleanInstance('WFConfiguration');
      $configuration->set('wfaccount',$row['wfaccountsid']);
      $configuration->set('assigned_user_id',$account->get('assigned_user_id'));
      $configuration->set('agentid',$account->get('agentid'));
      $configuration->set('smcreatorid',$account->get('created_by'));
      $configuration->set('createdtime',date('Y-m-d H:i:s'));
      $configuration->set('modifiedtime',date('Y-m-d H:i:s'));
      $configuration->save();
    }
  }
}

Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET `sequence` = 5 WHERE `blocklabel` = "LBL_WFINVENTORY_USER_DEFINED"');
