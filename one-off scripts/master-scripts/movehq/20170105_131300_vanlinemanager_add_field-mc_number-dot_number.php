<?php
/**
 * Created by PhpStorm.
 * User: Rik Davis
 * Date: 1/5/18
 * Time: 1:21 PM
 */

if (function_exists("call_ms_function_ver")) {
  $version = 3;
  if (call_ms_function_ver(__FILE__, $version)) {
    //already ran
    print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
    return;
  }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

// OT5511 - VanlineManager addition of new fields for reporting

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleName = 'VanlineManager';
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
  echo "Module $moduleName not found.";
  return;
}

$Vtiger_Utils_Log = true;

$create = [
  'VanlineManager' => [
    'LBL_VANLINEMANAGER_INFORMATION' => [
      'LBL_MC_NUMBER' => [
        'name' => 'mc_number',
        'table' => 'vtiger_vanlinemanager',
        'column' => 'mc_number',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'columntype' => 'VARCHAR(100)',
        'displaytype' => 1,
      ],
      'LBL_DOT_NUMBER' => [
        'name' => 'dot_number',
        'table' => 'vtiger_vanlinemanager',
        'column' => 'dot_number',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'columntype' => 'VARCHAR(100)',
        'displaytype' => 1,
      ]
    ],
  ]
];

multicreate($create);