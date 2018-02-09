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

$taskType = 'VTCreateEntityTask';

//Create workflow for employee_dlexpy ----- Employees Module
$wf1 = $wfManager->newWorkflow('Employees');

//On Notice section
$wf1->description = 'Lisence Expiration Date - On Notice';
$wf1->test = '[{"fieldname":"employee_dlexpy","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf1->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf1->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf1->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf1->schtime = '02:00:00';
$wf1->schdayofmonth = null;
$wf1->schdayofweek = null;
$wf1->schmonth = null;
$wf1->schannualdates = null;
$wf1->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  2:00AM'));

if (!existWorkflow($wf1)) {
    $wfManager->save($wf1);
}

if ($wf1->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //On Notice
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf1->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Lisence Expiration Date';
    $task1->methodName = 'createOnNoticeForLisenceExpirationDate';

    if (!existTask($task1->summary, $wf1->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Employees', 'createOnNoticeForLisenceExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOnNoticeForLisenceExpirationDate')) {
        $entityMethodManager->addEntityMethod('Employees', 'createOnNoticeForLisenceExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOnNoticeForLisenceExpirationDate');
    }
}

//Out of Service section
$wf101 = $wfManager->newWorkflow('Employees');

$wf101->description = 'Lisence Expiration Date - Out Of Service';
$wf101->test = '[{"fieldname":"employee_dlexpy","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf101->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf101->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf101->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf101->schtime = '02:15:00';
$wf101->schdayofmonth = null;
$wf101->schdayofweek = null;
$wf101->schmonth = null;
$wf101->schannualdates = null;
$wf101->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  2:15AM'));

if (!existWorkflow($wf101)) {
    $wfManager->save($wf101);
}

if ($wf101->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //Out Of Service
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf101->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Lisence Expiration Date';
    $task1->methodName = 'createOutOfServiceForLisenceExpirationDate';

    if (!existTask($task1->summary, $wf101->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Employees', 'createOutOfServiceForLisenceExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOutOfServiceForLisenceExpirationDate')) {
        $entityMethodManager->addEntityMethod('Employees', 'createOutOfServiceForLisenceExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOutOfServiceForLisenceExpirationDate');
    }
}
//--------------------End employee_dlexpy-------------------------------------------------------------
//
//
//Create workflow for employees_nbackground ------ Employees Module
$wf2 = $wfManager->newWorkflow('Employees');

$wf2->description = 'Background Expiration Date - On Notice';
$wf2->test = '[{"fieldname":"employees_nbackground","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf2->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf2->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf2->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf2->schtime = '02:30:00';
$wf2->schdayofmonth = null;
$wf2->schdayofweek = null;
$wf2->schmonth = null;
$wf2->schannualdates = null;
$wf2->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  2:30AM'));

if (!existWorkflow($wf2)) {
    $wfManager->save($wf2);
}

if ($wf2->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //On Notice
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf2->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Background Expiration Date';
    $task1->methodName = 'createOnNoticeForBackgroundExpirationDate';

    if (!existTask($task1->summary, $wf2->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Employees', 'createOnNoticeForBackgroundExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOnNoticeForBackgroundExpirationDate')) {
        $entityMethodManager->addEntityMethod('Employees', 'createOnNoticeForBackgroundExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOnNoticeForBackgroundExpirationDate');
    }
}


$wf201 = $wfManager->newWorkflow('Employees');

$wf201->description = 'Background Expiration Date - Out of Service';
$wf201->test = '[{"fieldname":"employees_nbackground","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf201->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf201->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf201->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf201->schtime = '02:45:00';
$wf201->schdayofmonth = null;
$wf201->schdayofweek = null;
$wf201->schmonth = null;
$wf201->schannualdates = null;
$wf201->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  2:45AM'));

if (!existWorkflow($wf201)) {
    $wfManager->save($wf201);
}

if ($wf201->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //Out Of Service
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf201->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Background Expiration Date';
    $task1->methodName = 'createOutOfServiceForBackgroundExpirationDate';

    if (!existTask($task1->summary, $wf201->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Employees', 'createOutOfServiceForBackgroundExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOutOfServiceForBackgroundExpirationDate')) {
        $entityMethodManager->addEntityMethod('Employees', 'createOutOfServiceForBackgroundExpirationDate', 'modules/Employees/actions/CreateWorkflow.php', 'createOutOfServiceForBackgroundExpirationDate');
    }
}
//--------------------End employees_nbackground-------------------------------------------------------------
//
//
//Create workflow for vehicle_plateexp ---- Vehicles Module
$wf3 = $wfManager->newWorkflow('Vehicles');

$wf3->description = 'License Plate Expiration - On notice';
$wf3->test = '[{"fieldname":"vehicle_plateexp","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf3->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf3->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf3->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf3->schtime = '03:00:00';
$wf3->schdayofmonth = null;
$wf3->schdayofweek = null;
$wf3->schmonth = null;
$wf3->schannualdates = null;
$wf3->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  3:00AM'));

if (!existWorkflow($wf3)) {
    $wfManager->save($wf3);
}

if ($wf3->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //On Notice
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf3->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for License Plate Expiration';
    $task1->methodName = 'createOnNoticeForLicensePlateExpiration';

    if (!existTask($task1->summary, $wf3->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Vehicles', 'createOnNoticeForLicensePlateExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOnNoticeForLicensePlateExpiration')) {
        $entityMethodManager->addEntityMethod('Vehicles', 'createOnNoticeForLicensePlateExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOnNoticeForLicensePlateExpiration');
    }
}
//---------------
$wf301 = $wfManager->newWorkflow('Vehicles');

$wf301->description = 'License Plate Expiration - Out Of Service';
$wf301->test = '[{"fieldname":"vehicle_plateexp","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf301->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf301->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf301->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf301->schtime = '03:15:00';
$wf301->schdayofmonth = null;
$wf301->schdayofweek = null;
$wf301->schmonth = null;
$wf301->schannualdates = null;
$wf301->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  3:15AM'));

if (!existWorkflow($wf301)) {
    $wfManager->save($wf301);
}

if ($wf301->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //Out Of Service
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf301->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for License Plate Expiration';
    $task1->methodName = 'createOutOfServiceForLicensePlateExpiration';

    if (!existTask($task1->summary, $wf301->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Vehicles', 'createOutOfServiceForLicensePlateExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOutOfServiceForLicensePlateExpiration')) {
        $entityMethodManager->addEntityMethod('Vehicles', 'createOutOfServiceForLicensePlateExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOutOfServiceForLicensePlateExpiration');
    }
}
//--------------------End vehicle_plateexp-------------------------------------------------------------
//
//
//Create workflow for vehicles_insuranceexpdate ---- Vehicles Module
$wf4 = $wfManager->newWorkflow('Vehicles');

$wf4->description = 'Insurance Expiration - On Notice';
$wf4->test = '[{"fieldname":"vehicles_insuranceexpdate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf4->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf4->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf4->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf4->schtime = '03:30:00';
$wf4->schdayofmonth = null;
$wf4->schdayofweek = null;
$wf4->schmonth = null;
$wf4->schannualdates = null;
$wf4->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  3:30AM'));

if (!existWorkflow($wf4)) {
    $wfManager->save($wf4);
}

if ($wf4->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //On Notice
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf4->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Insurance Expiration';
    $task1->methodName = 'createOnNoticeForInsuranceExpiration';

    if (!existTask($task1->summary, $wf4->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Vehicles', 'createOnNoticeForInsuranceExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOnNoticeForInsuranceExpiration')) {
        $entityMethodManager->addEntityMethod('Vehicles', 'createOnNoticeForInsuranceExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOnNoticeForInsuranceExpiration');
    }
}
//--------------------
$wf401 = $wfManager->newWorkflow('Vehicles');

$wf401->description = 'Insurance Expiration - Out Of Service';
$wf401->test = '[{"fieldname":"vehicles_insuranceexpdate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf401->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf401->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf401->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf401->schtime = '03:45:00';
$wf401->schdayofmonth = null;
$wf401->schdayofweek = null;
$wf401->schmonth = null;
$wf401->schannualdates = null;
$wf401->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  3:45AM'));

if (!existWorkflow($wf401)) {
    $wfManager->save($wf401);
}

if ($wf401->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //Out Of Service
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf401->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Insurance Expiration';
    $task1->methodName = 'createOutOfServiceForInsuranceExpiration';

    if (!existTask($task1->summary, $wf401->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('Vehicles', 'createOutOfServiceForInsuranceExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOutOfServiceForInsuranceExpiration')) {
        $entityMethodManager->addEntityMethod('Vehicles', 'createOutOfServiceForInsuranceExpiration', 'modules/Vehicles/actions/CreateWorkflow.php', 'createOutOfServiceForInsuranceExpiration');
    }
}
//--------------------End vehicles_insuranceexpdate-------------------------------------------------------------
//
//Create workflow for inspection_duedate ---- Vehicle Inspections Module
$wf7 = $wfManager->newWorkflow('VehicleInspections');

$wf7->description = 'Inspection Due Date - On Notice';
$wf7->test = '[{"fieldname":"inspection_duedate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf7->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf7->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf7->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf7->schtime = '04:00:00';
$wf7->schdayofmonth = null;
$wf7->schdayofweek = null;
$wf7->schmonth = null;
$wf7->schannualdates = null;
$wf7->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  4:00AM'));

if (!existWorkflow($wf7)) {
    $wfManager->save($wf7);
}

if ($wf7->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf7->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Inspection Due Date';
    $task1->methodName = 'createOnNoticeForInspectionDueDate';

    if (!existTask($task1->summary, $wf7->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('VehicleInspections', 'createOnNoticeForInspectionDueDate', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOnNoticeForInspectionDueDate')) {
        $entityMethodManager->addEntityMethod('VehicleInspections', 'createOnNoticeForInspectionDueDate', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOnNoticeForInspectionDueDate');
    }
}
//--------------------
$wf701 = $wfManager->newWorkflow('VehicleInspections');

$wf701->description = 'Inspection Due Date - Out Of Service';
$wf701->test = '[{"fieldname":"inspection_duedate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf701->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf701->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf701->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf701->schtime = '04:15:00';
$wf701->schdayofmonth = null;
$wf701->schdayofweek = null;
$wf701->schmonth = null;
$wf701->schannualdates = null;
$wf701->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  4:15AM'));

if (!existWorkflow($wf701)) {
    $wfManager->save($wf701);
}

if ($wf701->id != null) {
    //Create WorkflowTasks
    //Invoke Custom Function
    //Out of Service
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf701->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Inspection Due Date';
    $task1->methodName = 'createOutOfServiceForInspectionDueDate';

    if (!existTask($task1->summary, $wf701->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('VehicleInspections', 'createOutOfServiceForInspectionDueDate', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOutOfServiceForInspectionDueDate')) {
        $entityMethodManager->addEntityMethod('VehicleInspections', 'createOutOfServiceForInspectionDueDate', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOutOfServiceForInspectionDueDate');
    }
}
//--------------------End vehicles_insuranceexpdate-------------------------------------------------------------
//
//
//Create workflow for inspection_photosdate ---- Vehicle Inspections Module
$wf8 = $wfManager->newWorkflow('VehicleInspections');

$wf8->description = 'Photos Due Date - On Notice';
$wf8->test = '[{"fieldname":"inspection_photosdate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf8->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf8->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf8->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf8->schtime = '04:30:00';
$wf8->schdayofmonth = null;
$wf8->schdayofweek = null;
$wf8->schmonth = null;
$wf8->schannualdates = null;
$wf8->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  4:30AM'));

if (!existWorkflow($wf8)) {
    $wfManager->save($wf8);
}

if ($wf8->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf8->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Photos Due';
    $task1->methodName = 'createOnNoticeForInspectionPhotosDue';

    if (!existTask($task1->summary, $wf8->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('VehicleInspections', 'createOnNoticeForInspectionPhotosDue', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOnNoticeForInspectionPhotosDue')) {
        $entityMethodManager->addEntityMethod('VehicleInspections', 'createOnNoticeForInspectionPhotosDue', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOnNoticeForInspectionPhotosDue');
    }
}
//--------------------
$wf801 = $wfManager->newWorkflow('VehicleInspections');

$wf801->description = 'Photos Due Date - Out Of Service';
$wf801->test = '[{"fieldname":"inspection_photosdate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf801->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf801->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf801->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf801->schtime = '04:45:00';
$wf801->schdayofmonth = null;
$wf801->schdayofweek = null;
$wf801->schmonth = null;
$wf801->schannualdates = null;
$wf801->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  4:45AM'));

if (!existWorkflow($wf801)) {
    $wfManager->save($wf801);
}

if ($wf801->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf801->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Photos Due';
    $task1->methodName = 'createOutOfServiceForInspectionPhotosDue';

    if (!existTask($task1->summary, $wf801->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('VehicleInspections', 'createOutOfServiceForInspectionPhotosDue', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOutOfServiceForInspectionPhotosDue')) {
        $entityMethodManager->addEntityMethod('VehicleInspections', 'createOutOfServiceForInspectionPhotosDue', 'modules/VehicleInspections/actions/CreateWorkflow.php', 'createOutOfServiceForInspectionPhotosDue');
    }
}
//--------------------End inspection_photosdate-------------------------------------------------------------
//
//
//Create workflow for annualreviewdue ---- Driver Qualification Module
$wf9 = $wfManager->newWorkflow('DriverQualification');

$wf9->description = 'Annual Review Due - On Notice';
$wf9->test = '[{"fieldname":"annualreviewdue","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf9->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf9->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf9->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf9->schtime = '05:00:00';
$wf9->schdayofmonth = null;
$wf9->schdayofweek = null;
$wf9->schmonth = null;
$wf9->schannualdates = null;
$wf9->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  5:00AM'));

if (!existWorkflow($wf9)) {
    $wfManager->save($wf9);
}

if ($wf9->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf9->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Annual Review Due';
    $task1->methodName = 'createOnNoticeForAnnualReviewDue';

    if (!existTask($task1->summary, $wf9->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOnNoticeForAnnualReviewDue', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForAnnualReviewDue')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOnNoticeForAnnualReviewDue', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForAnnualReviewDue');
    }
}
//--------------------
$wf901 = $wfManager->newWorkflow('DriverQualification');

$wf901->description = 'Annual Review Due - Out Of Service';
$wf901->test = '[{"fieldname":"annualreviewdue","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf901->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf901->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf901->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf901->schtime = '05:15:00';
$wf901->schdayofmonth = null;
$wf901->schdayofweek = null;
$wf901->schmonth = null;
$wf901->schannualdates = null;
$wf901->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  5:15AM'));

if (!existWorkflow($wf901)) {
    $wfManager->save($wf901);
}

if ($wf901->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf901->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Annual Review Due';
    $task1->methodName = 'createOutOfServiceForAnnualReviewDue';

    if (!existTask($task1->summary, $wf901->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOutOfServiceForAnnualReviewDue', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForAnnualReviewDue')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOutOfServiceForAnnualReviewDue', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForAnnualReviewDue');
    }
}
//--------------------End annualreviewdue-------------------------------------------------------------
//
//
//Create workflow for physicalexpirationdate ---- Driver Qualification Module
$wf10 = $wfManager->newWorkflow('DriverQualification');

$wf10->description = 'Physical Expiration Date - On Notice';
$wf10->test = '[{"fieldname":"physicalexpirationdate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf10->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf10->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf10->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf10->schtime = '05:30:00';
$wf10->schdayofmonth = null;
$wf10->schdayofweek = null;
$wf10->schmonth = null;
$wf10->schannualdates = null;
$wf10->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  5:30AM'));

if (!existWorkflow($wf10)) {
    $wfManager->save($wf10);
}

if ($wf10->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf10->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Physical Expiration Date';
    $task1->methodName = 'createOnNoticeForPhysicalExpirationDate';

    if (!existTask($task1->summary, $wf10->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOnNoticeForPhysicalExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForPhysicalExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOnNoticeForPhysicalExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForPhysicalExpirationDate');
    }
}
//--------------------
$wf1001 = $wfManager->newWorkflow('DriverQualification');

$wf1001->description = 'Physical Expiration Date - Out Of Service';
$wf1001->test = '[{"fieldname":"physicalexpirationdate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf1001->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf1001->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf1001->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf1001->schtime = '05:45:00';
$wf1001->schdayofmonth = null;
$wf1001->schdayofweek = null;
$wf1001->schmonth = null;
$wf1001->schannualdates = null;
$wf1001->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  5:45AM'));

if (!existWorkflow($wf1001)) {
    $wfManager->save($wf1001);
}

if ($wf1001->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf1001->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Physical Expiration Date';
    $task1->methodName = 'createOutOfServiceForPhysicalExpirationDate';

    if (!existTask($task1->summary, $wf1001->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOutOfServiceForPhysicalExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForPhysicalExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOutOfServiceForPhysicalExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForPhysicalExpirationDate');
    }
}
//--------------------End physicalexpirationdate-------------------------------------------------------------
//
//
//Create workflow for mvrexpirationdate ---- Driver Qualification Module
$wf11 = $wfManager->newWorkflow('DriverQualification');

$wf11->description = 'MVR Expiration Date - On Notice';
$wf11->test = '[{"fieldname":"mvrexpirationdate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf11->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf11->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf11->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf11->schtime = '06:00:00';
$wf11->schdayofmonth = null;
$wf11->schdayofweek = null;
$wf11->schmonth = null;
$wf11->schannualdates = null;
$wf11->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  6:00AM'));

if (!existWorkflow($wf11)) {
    $wfManager->save($wf11);
}

if ($wf11->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf11->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for MVR Expiration Date';
    $task1->methodName = 'createOnNoticeForMVRExpirationDate';

    if (!existTask($task1->summary, $wf11->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOnNoticeForMVRExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForMVRExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOnNoticeForMVRExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForMVRExpirationDate');
    }
}
//--------------------
$wf1101 = $wfManager->newWorkflow('DriverQualification');

$wf1101->description = 'MVR Expiration Date - Out Of Service';
$wf1101->test = '[{"fieldname":"mvrexpirationdate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf1101->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf1101->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf1101->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf1101->schtime = '06:15:00';
$wf1101->schdayofmonth = null;
$wf1101->schdayofweek = null;
$wf1101->schmonth = null;
$wf1101->schannualdates = null;
$wf1101->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  6:15AM'));

if (!existWorkflow($wf1101)) {
    $wfManager->save($wf1101);
}

if ($wf1101->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf1101->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for MVR Expiration Date';
    $task1->methodName = 'createOutOfServiceForMVRExpirationDate';

    if (!existTask($task1->summary, $wf1101->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOutOfServiceForMVRExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForMVRExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOutOfServiceForMVRExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForMVRExpirationDate');
    }
}
//--------------------End mvrexpirationdate-------------------------------------------------------------
//
//
//Create workflow for orientationexpitariondate ---- Driver Qualification Module
$wf12 = $wfManager->newWorkflow('DriverQualification');

$wf12->description = 'Orientation Expiration Date - On Notice';
$wf12->test = '[{"fieldname":"orientationexpitariondate","operation":"days later","value":"30","valuetype":"","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf12->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf12->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf12->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf12->schtime = '06:30:00';
$wf12->schdayofmonth = null;
$wf12->schdayofweek = null;
$wf12->schmonth = null;
$wf12->schannualdates = null;
$wf12->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  6:30AM'));

if (!existWorkflow($wf12)) {
    $wfManager->save($wf12);
}

if ($wf12->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf12->id;
    $task1->active = true;
    $task1->summary = 'Create On Notice for Orientation Expiration Date';
    $task1->methodName = 'createOnNoticeForOrientationExpirationDate';

    if (!existTask($task1->summary, $wf12->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOnNoticeForOrientationExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForOrientationExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOnNoticeForOrientationExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOnNoticeForOrientationExpirationDate');
    }
}
//--------------------
$wf1201 = $wfManager->newWorkflow('DriverQualification');

$wf1201->description = 'Orientation Expiration Date - Out Of Service';
$wf1201->test = '[{"fieldname":"orientationexpitariondate","operation":"is today","value":null,"valuetype":"rawtext","joincondition":"","groupjoin":"and","groupid":"0"}]';
$wf1201->executionCondition = VTWorkflowManager::$ON_SCHEDULE; // VTWorkflowManager::$ON_SCHEDULE;
$wf1201->agents = '15 |##| 450 |##| 456 |##| 457 |##| 458 |##| 460 |##| 462 |##| 463 |##| 464 |##| 465 |##| 466 |##| 475 |##| 476 |##| 477 |##| 478 |##| 479 |##| 480 |##| 482 |##| 483 |##| 484 |##| 485 |##| 507 |##| 508 |##| 509 |##| 510 |##| 511 |##| 513 |##| 514 |##| 515 |##| 516 |##| 517 |##| 520 |##| 1001 |##| 18213 |##| 18294 |##| 18295 |##| 22217 |##| 22218 |##| 38190';
$wf1201->schtypeid = Workflow::$SCHEDULED_DAILY;
$wf1201->schtime = '06:45:00';
$wf1201->schdayofmonth = null;
$wf1201->schdayofweek = null;
$wf1201->schmonth = null;
$wf1201->schannualdates = null;
$wf1201->nexttrigger_time = date('Y-m-d H:i:s', strtotime('tomorrow  6:45AM'));

if (!existWorkflow($wf1201)) {
    $wfManager->save($wf1201);
}

if ($wf1201->id != null) {
    //Create WorkflowTask
    //Invoke Custom Function
    $task1 = new VTEntityMethodTask();
    $task1->workflowId = $wf1201->id;
    $task1->active = true;
    $task1->summary = 'Create Out Of Service for Orientation Expiration Date';
    $task1->methodName = 'createOutOfServiceForOrientationExpirationDate';

    if (!existTask($task1->summary, $wf1201->id)) {
        $taskManager->saveTask($task1);
    }

    if (!exitsEntityMethod('DriverQualification', 'createOutOfServiceForOrientationExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForOrientationExpirationDate')) {
        $entityMethodManager->addEntityMethod('DriverQualification', 'createOutOfServiceForOrientationExpirationDate', 'modules/DriverQualification/actions/CreateWorkflow.php', 'createOutOfServiceForOrientationExpirationDate');
    }
}
//--------------------End orientationexpitariondate-------------------------------------------------------------


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";