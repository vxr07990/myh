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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('WFOperationsTasks');

if(!$module) {
  return false;
}

$create = ['WFOperationsTasks' => [
    'LBL_WFOPERATIONSTASKS_TASKDETAILS_BLOCK' => [
      'LBL_WFOPERATIONSTASKS_COSTCENTER' => [
        'name' => 'costcenter',
        'table' => 'vtiger_wfoperationstasks',
        'column' => 'costcenter',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'presence' => 0,
        'quickcreate' => 0,
        'sequence' => 5,
        'setRelatedModules' => ['WFCostCenters'],
      ],
    ],
  ]
];

multicreate($create);
