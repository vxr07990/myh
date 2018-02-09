<?php
/**
 * Created by PhpStorm.
 * User: Rik Davis
 * Date: 1/5/18
 * Time: 5:14 PM
 */

if (function_exists("call_ms_function_ver")) {
  $version = 4;
  if (call_ms_function_ver(__FILE__, $version)) {
    //already ran
    print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
    return;
  }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

// OT5513 - AgentManager addition of new state number field for reporting

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleName = 'AgentManager';
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
  echo "Module $moduleName not found.";
  return;
}

$Vtiger_Utils_Log = true;

$create = [
  'AgentManager' => [
    'LBL_AGENTMANAGER_INFORMATION' => [
      'LBL_STATE_NUMBER' => [
        'name' => 'state_number',
        'table' => 'vtiger_agentmanager',
        'column' => 'state_number',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'displaytype' => 1,
      ]
    ],
  ]
];

multicreate($create);