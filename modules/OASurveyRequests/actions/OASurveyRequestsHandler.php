<?php

include_once('include/Webservices/Create.php');

class OASurveyRequests_OASurveyRequestsHandler_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        $this->exposeMethod('ajaxHandler');
    }
    
    public function checkPermission(Vtiger_Request $request)
    {
        return true;
    }
    
    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    public function ajaxHandler(Vtiger_Request $request)
    {
        include_once 'include/Webservices/Revise.php';

        if (!isset($_REQUEST["email"])) {
            $agentStatusArray = array('0' => 'Pending','1' => 'Accepted','2' => 'Removed','3' => 'Declined');
            $db = PearDatabase::getInstance();
            //file_put_contents('logs/devLog.log', "\n OA HANDLER REQ : ".print_r($_REQUEST, true), FILE_APPEND);
            // Can't user webservices to update because the record is owned by another agent.
            file_put_contents('logs/devLog.log', "\n SQL: UPDATE vtiger_participatingagents SET status=? WHERE rel_crmid=? AND agent_type=?", FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n  SQL PARAMS: ".print_r(array($agentStatusArray[$request->get("status")], $request->get("crmid"), $request->get("agent_type")), true), FILE_APPEND);
            $db->pquery("UPDATE vtiger_oasurveyrequests SET oasurveyrequests_status=? WHERE oasurveyrequestsid=?", array($agentStatusArray[$request->get("status")], $request->get("id")));
            $db->pquery("UPDATE vtiger_participatingagents SET status=? WHERE rel_crmid=? AND agent_type=?", array($agentStatusArray[$request->get("status")], $request->get("crmid"), $request->get("agent_type")));
            
            if ($agentStatusArray[$request->get("status")] == 'Declined') {
                //create calendar task for requesting user indicating rejection
                $user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                $row = $db->pquery("SELECT `vtiger_oasurveyrequests`.requestor_user_id, `vtiger_crmentity`.label, `vtiger_crmentity`.setype, `vtiger_agents`.agentname 
									FROM `vtiger_oasurveyrequests` LEFT JOIN `vtiger_agents` ON `vtiger_oasurveyrequests`.requested_agency = `vtiger_agents`.agentsid 
									LEFT JOIN `vtiger_crmentity` ON `vtiger_oasurveyrequests`.related_record = `vtiger_crmentity`.crmid WHERE oasurveyrequestsid=?", array($request->get("id")))->fetchRow();
                $requestorId = $row['requestor_user_id'];
                $agentName = $row['agentname'];
                $relatedRecordName = $row['label'];
                $relatedRecordModule = $row['setype'];
                //todo convert dates/times to user's timezone
                $taskData = [
                    'subject' => "Participation Rejected By $agentName",
                    'taskstatus' => 'Pending Input',
                    'date_start' => date('Y-m-d'),
                    'time_start' => date('h:i A'),
                    'due_date' => date('Y-m-d'),
                    'description' => "$agentName has declined your participation request for the $relatedRecordModule record $relatedRecordName.",
                    'assigned_user_id' => '19x' . $requestorId,
                ];
                //file_put_contents('logs/devLog.log', "\n TASK DATA: " . print_r($taskData, true), FILE_APPEND);
                $newTask = vtws_create('Calendar', $taskData, $current_user);
                //file_put_contents('logs/devLog.log', "\n new task ID: " . $newTask['id'], FILE_APPEND);
            }
        }
    }
}
