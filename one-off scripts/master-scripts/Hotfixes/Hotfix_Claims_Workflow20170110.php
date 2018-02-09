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

include_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
include_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
include_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
include_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';

if (!function_exists('existWorkflow')) {

    function existWorkflow($wf) {
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE module_name=? AND summary=? AND test=?', [$wf->moduleName, $wf->description, $wf->test]);
        if ($result && $adb->num_rows($result) > 0) {
            $exist = true;
        } else {
            $exist = false;
        }
        return $exist;
    }

}
if (!function_exists('existTask')) {

    function existTask($wftSummary, $wfId) {
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery('SELECT task_id FROM com_vtiger_workflowtasks WHERE summary=? AND workflow_id=?', [$wftSummary, $wfId]);
        if ($result && $adb->num_rows($result) > 0) {
            $exist = true;
        } else {
            $exist = false;
        }
        return $exist;
    }

}
if (!function_exists('exitsEntityMethod')) {

    function exitsEntityMethod($moduleName, $methodName, $functionPath, $functionName) {
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery('SELECT workflowtasks_entitymethod_id FROM com_vtiger_workflowtasks_entitymethod WHERE module_name=? AND method_name=? AND function_path=? AND function_name=?', [$moduleName, $methodName, $functionPath, $functionName]);
        if ($result && $adb->num_rows($result) > 0) {
            $exist = true;
        } else {
            $exist = false;
        }
        return $exist;
    }

}

$adb = PearDatabase::getInstance();
$wfManager = new VTWorkflowManager($adb);
$taskManager = new VTTaskManager($adb);
$entityMethodManager = new VTEntityMethodManager($adb);

//Create workflow ----- Claims Module
$wf1 = $wfManager->newWorkflow('Claims');

$wf1->description = 'Claims Update Days to Settle';
$wf1->test = '[]';
$wf1->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
$wf1->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf1->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf1->schtime = '00:30:00';
$wf1->schdayofmonth = null;
$wf1->schdayofweek = null;
$wf1->schmonth = null;
$wf1->schannualdates = null;
$wf1->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  0:30AM'));

if (!existWorkflow($wf1)) {
    $wfManager->save($wf1);
}

if ($wf1->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf1->id;
    $task1->active = true;
    $task1->summary = 'Update Calendar and Business Days to Settle';
    $task1->methodName = 'calculateDaysToSettle';

    if (!existTask($task1->summary, $wf1->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Claims', 'calculateDaysToSettle', 'modules/Claims/actions/CreateWorkflow.php', 'calculateDaysToSettle')) {
        $entityMethodManager->addEntityMethod('Claims', 'calculateDaysToSettle', 'modules/Claims/actions/CreateWorkflow.php', 'calculateDaysToSettle');
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";