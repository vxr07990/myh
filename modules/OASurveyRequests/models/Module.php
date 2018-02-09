<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/vtlib/Vtiger/Module.php');
include_once('include/Webservices/Revise.php');
include_once('include/Webservices/Create.php');
include_once 'vtlib/Vtiger/Mailer.php';

class OASurveyRequests_Module_Model extends Vtiger_Module_Model
{
    public $arr_agent_types = array('0' => 'Booking Agent', '1' => 'Destination Agent', '2' => 'Destination Storage Agent', '3' => 'Hauling Agent', '4' => 'Invoicing Agent', '5' => 'Origin Agent', '6' => 'Origin Storage Agent', '7' => 'Estimating Agent');
    public $arr_agent_status = array('0' => 'Pending', '1' => 'Accepted', '2' => 'Removed', '3' => 'Declined');
    public $arr_agent_permissions = array('0' => 'Full', '1' => 'Read-Only', '2' => 'No-Rates', '3' => 'No-Access');

    public function isQuickCreateSupported()
    {
        return false;
    }

    public function checkExistence($oppID, $agentID, $agentType)
    {
        $db = PearDatabase::getInstance();

        //$agentType = $this->arr_agent_types[$agentType];
        $result = $db->pquery("SELECT * FROM vtiger_oasurveyrequests oa INNER JOIN vtiger_crmentity cr ON oa.oasurveyrequestsid = cr.crmid WHERE cr.deleted = 0 AND oa.requested_agency=? AND oa.oasurveyrequests_agent_type=? AND oa.related_record=?", array($agentID, $agentType, $oppID));

        if ($db->num_rows($result) > 0) {
            return $db->query_result($result, 0, 'oasurveyrequestsid');
        } else {
            return false;
        }
    }

    public function checkOwners($owner, $agentId)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT `vtiger_agents`.agentmanager_id FROM `vtiger_agents` WHERE agentsid = ? LIMIT 1";
        $row = $db->pquery($sql, array($agentId))->fetchRow();
        if ($row) {
            $agentManagerId = $row['agentmanager_id'];
        } else {
            //agent record does not have an associated agent manager entry
            return false;
        }
        //if the requesting user has access to the participating agent or if the agent is the owner
        if (in_array($agentManagerId, getPermittedAccessible()) || $agentManagerId == $owner) {
            return true;
        } else {
            return false;
        }
    }

    public function saveOASurveyRequest($agentPermission, $agentStatus, $agentType, $agentId, $module, $owner, $record, $mode, $oasurveyrequestsID = '', $participantId = false)
    {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
        $arr_agent_status_conversion = array('full' => 'Full', 'read_only' => 'Read-Only', 'no_rates' => 'No-Rates', 'no_access' => 'No-Access');

        if ($this->checkOwners($owner, $agentId) || (getenv('INSTANCE_NAME') == 'sirva') && $agentType == 'Destination Agent') {
            $db->pquery("UPDATE vtiger_participatingagents SET status=? WHERE rel_crmid=? AND agents_id=? AND agent_type=?", array("Accepted", $record, $agentId, $agentType));
            return false;
        }
        //get the requestor agency name & record label to be used in the message
        $requestorAgencyName = $db->pquery("SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid = ? LIMIT 1", [$owner])->fetchRow()['agency_name'];
        //have to do this because label is updated right AFTER the save for no particular reason
        $recordLabel = decode_html(getEntityName($module, $record)[$record]);
        $element["view_level"] = $agentPermission;
        $element["oasurveyrequests_status"] = $agentStatus;
        $element["oasurveyrequests_agent_type"] = $agentType;
        $element["requested_agency"] = vtws_getWebserviceEntityId('Agents', $agentId);
        $element["related_record"] = vtws_getWebserviceEntityId($module, $record);
        $element["agentid"] = $owner;
        $element["requestor_agency_id"] = vtws_getWebserviceEntityId('AgentManager', $owner);
        $element["assigned_user_id"] = $element["requestor_user_id"] = vtws_getWebserviceEntityId('Users', $user->id);
        $element['message'] = "$requestorAgencyName has requested your agency as a participating " . $element["oasurveyrequests_agent_type"] . " on $module record $recordLabel with view level $arr_agent_status_conversion[$agentPermission]";
        //file_put_contents('logs/devLog.log', "\n Element : ".print_r($element, true), FILE_APPEND);
        if ($mode == "create" || (empty($oasurveyrequestsID) && $element["oasurveyrequests_status"] == 'Pending')) {
            $newRequest = vtws_create("OASurveyRequests", $element, $user);
            $requestId = explode('x', $newRequest['id'])[1];
            //file_put_contents('logs/devLog.log', "\n create req record id: $requestId", FILE_APPEND);
            //@TODO: fix this because these two functions both pull the agent record, that's wasteful, unless caching in fact works.  Does it?
            if (!$this->isAgentCRMUser($agentId)) {
                $this->sendNotification($element);
            }
        } else {
            if ($oasurveyrequestsID) {
                //update
                $element["oasurveyrequestsid"] = vtws_getWebserviceEntityId('OASurveyRequests', $oasurveyrequestsID);
                $element["id"]                 = vtws_getWebserviceEntityId('OASurveyRequests', $oasurveyrequestsID);
                //file_put_contents('logs/devLog.log', "\n REQ revise Element : ".print_r($element, true), FILE_APPEND);
                $newRequest = vtws_revise($element, $user);
                $requestId  = explode('x', $newRequest['id'])[1];
                //file_put_contents('logs/devLog.log', "\n hits this?", FILE_APPEND);
            }
        }
        //file_put_contents('logs/devLog.log', "\n return req record id: $requestId", FILE_APPEND);
        return $requestId;
    }

    public function updateParticipants(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();

        $status = array_search($request->get("oasurveyrequests_status"), $this->arr_agent_status);
        $permission = array_search($request->get("view_level"), $this->arr_agent_permissions);
        $agent_type = array_search($request->get("oasurveyrequests_agent_type"), $this->arr_agent_types);

        return $db->pquery("UPDATE vtiger_participatingagents SET status=?, permission=? WHERE rel_crmid=? AND agents_id=? AND agent_type=?", array($status, $permission, $request->get("related_record"), $request->get("requested_agency"), $agent_type));
    }

    public function getPendingRequestForUser()
    {
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userAccesibleAgents = '('.implode(',', explode(' |##| ', $currentUser->get('agent_ids'))).')';

        //file_put_contents('logs/devLog.log', "\n user acc - " . $userAccesibleAgents, FILE_APPEND);

        $result = $db->pquery("SELECT * FROM vtiger_oasurveyrequests
                                INNER JOIN vtiger_crmentity ON vtiger_oasurveyrequests.oasurveyrequestsid = vtiger_crmentity.crmid
                                INNER JOIN vtiger_agents ON vtiger_oasurveyrequests.requested_agency = vtiger_agents.agentsid
                                WHERE agentmanager_id IN $userAccesibleAgents AND oasurveyrequests_status = ? AND deleted=0 AND related_record <> '0'", array("Pending"));



        $recordModels = array();
        if ($result && $db->num_rows($result) > 0 && $currentUser->get('cf_oa_da_coordinator') == 1) {
            while ($row = $db->fetch_array($result)) {
                $model = Vtiger_Record_Model::getInstanceById($row["related_record"], $this->getSeType($row["related_record"]));
                $userModel = Vtiger_Record_Model::getInstanceById($row["requestor_user_id"], "Users");
                //file_put_contents('logs/devLog.log', "\n OA Row : ".print_r($row, true), FILE_APPEND);
                $status = array_search($row["oasurveyrequests_status"], $this->arr_agent_status);
                $permission = array_search($row["view_level"], $this->arr_agent_permissions);
                $agent_type = $row["oasurveyrequests_agent_type"];

                $crmid = $row["related_record"];

                $recordModels[] = array("id" => $row["oasurveyrequestsid"], "agent_id" => $row["requested_agency"], "status" => $status, "permission" => $permission, "agent_type" => $agent_type, "crmid" => $crmid, "modulo" => $this->getSeType($row["related_record"]), "related_record" => $model->getName(), "user_requestor" => $userModel->getName(), "message" => $row["message"]);
            }
        }

        return $recordModels;
    }

    public function sendNotification($oaRequestInfo)
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $site_URL = vglobal('site_URL');

        $mailer = new Vtiger_Mailer();
        $mailer->IsHTML(true);

        $fromEmail = $currentUserModel->get('email1'); //campo creado desde vtiger, email del agent
        $replyTo = $currentUserModel->get('email1');
        $userName = $currentUserModel->getName();
        $mailer->ConfigSenderInfo($fromEmail, $userName, $replyTo);

        $mailer->initialize();

        $mailer->Body = 'The following message was included in the OA Survey Request:<br>' . $oaRequestInfo["message"] . '<br><br><a href="' . $site_URL . "modules/OASurveyRequests/actions/OASurveyRequestsHandlerForMail.php?status=1&email=true&agent_id=" . $oaRequestInfo["requested_agency"] . "&agent_type=" . $oaRequestInfo["oasurveyrequests_agent_type"] . "&crmid=" . $oaRequestInfo["related_record"] . "&oaid=" . $oaRequestInfo["oasurveyrequestsid"] . '" target="_blank" style="font-size: 12.8px;">Accept</a> - <a href="' . $site_URL . "modules/OASurveyRequests/actions/OASurveyRequestsHandlerForMail.php?status=3&email=true&agent_id=" . $oaRequestInfo["requested_agency"] . "&agent_type=" . $oaRequestInfo["oasurveyrequests_agent_type"] . "&crmid=" . $oaRequestInfo["related_record"] . "&oaid=" . $oaRequestInfo["oasurveyrequestsid"] . '" target="_blank" style="font-size: 12.8px;">Decline</a><br><br>Regards, Sirva';

        $mailer->Subject = "OA Survey Request Message";

        $agentEmail = $this->getAgentEmail($oaRequestInfo["requested_agency"]);

        if ($agentEmail) {
            $mailer->AddAddress($agentEmail);
            $status = $mailer->Send(true);
            if (!$status) {
                $status = $mailer->getError();
            }
        }
    }

    public function updateCallStatus($recordIds)
    {
        $db = PearDatabase::getInstance();
        $query = "UPDATE " . self::moduletableName . " SET callstatus='no-response'
                  WHERE pbxmanagerid IN (" . generateQuestionMarks($recordIds) . ")
                  AND callstatus='ringing'";
        $db->pquery($query, $recordIds);
    }

    public function getSeType($id)
    {
        $db = PearDatabase::getInstance();

        $result = $db->pquery("SELECT * FROM vtiger_crmentity WHERE crmid=? AND deleted=0", array($id));
        if ($result != null && isset($result)) {
            if ($db->num_rows($result) > 0) {
                $seType = $db->query_result($result, 0, "setype");
            } else {
                $seType = null;
            }
        }
        return $seType;
    }

    public function getAgentEmail($agentId)
    {
        try {
            $agentRecordModel = Vtiger_Record_Model::getInstanceById($agentId, 'Agents');
            $email            = $agentRecordModel->get('agent_email');
            if ($email != '') {
                return $email;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    //@TODO: the above is dependent on this return true, why not pass the record model back instead of true and pass it to getAgentEmail
    //@TODO: the above is dependent on this return true, why not pass the record model back instead of true and pass it to getAgentEmail
    public function isAgentCRMUser($agentId)
    {
        try {
            $agentInstance = Vtiger_Record_Model::getInstanceById($agentId, 'Agents');
            if ($agentInstance->get('agentmanager_id') == '') {
                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            //So ya like if there's no record don't bring the whole house down.
            return false;
        }
    }
}
