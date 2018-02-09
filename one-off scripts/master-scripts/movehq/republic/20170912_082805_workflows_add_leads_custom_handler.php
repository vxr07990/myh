<?php
/**
 * FAKE NEWS
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/23/2017
 * Time: 3:32 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';

$module = Vtiger_Module::getInstance('Accounts');

if(!$module)
{
    return;
}

$taskType = [
    'name' => 'VTEntityMethodTask',
    'label' => 'Invoke Custom Function',
    'classname' => 'VTEntityMethodTask',
    'classpath' => 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc',
    'templatepath' => 'com_vtiger_workflow/taskforms/VTEntityMethodTask.tpl',
    'sourcemodule' => '',
    'modules'  => ['include' => [],'exclude' => []]
];

$entityTaskObject =  VTTaskType::getInstanceFromTaskType($taskType['name']);

if ($entityTaskObject) {
    print "There is a task object\n"
          . print_r($entityTaskObject, true)
          . PHP_EOL;
}

if($entityTaskObject->get('classname') != $taskType['classname']) {
    $taskObject = new VTTaskType;
    $taskObject->registerTaskType($taskType);
}

$entityModuleName = 'Leads';
$entityLabel = 'Republic Post FTP Leads';
$entityFilePath = 'modules/Leads/actions/ApiPostHandler.php';
$entityMethodName = 'republicFTPLeads';

$emm = new VTEntityMethodManager($adb);
if (!$emm->entityMethodExists($entityModuleName, $entityMethodName)) {
    $emm->addEntityMethod($entityModuleName, $entityLabel, $entityFilePath, $entityMethodName);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";