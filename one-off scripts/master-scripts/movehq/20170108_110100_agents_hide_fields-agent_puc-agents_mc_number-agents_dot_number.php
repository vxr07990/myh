<?php
/**
 * Created by PhpStorm.
 * User: Rik Davis
 * Date: 1/8/18
 * Time: 11:14 AM
 */
if (function_exists("call_ms_function_ver")) {
  $version = 2;
  if (call_ms_function_ver(__FILE__, $version)) {
    //already ran
    print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
    return;
  }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

// OT5514 - Agent Roster hide fields for reporting

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

updateFieldPresence('agent_puc', 'Agents', 1);
updateFieldPresence('agents_mc_number', 'Agents', 1);
updateFieldPresence('agents_dot_number', 'Agents', 1);