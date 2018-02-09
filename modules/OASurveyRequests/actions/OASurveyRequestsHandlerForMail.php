<?php

chdir(dirname(__FILE__) . '/../../..');

require_once 'includes/Loader.php';
require_once 'include/utils/utils.php';
require_once 'include/Webservices/Revise.php';
vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport('includes.runtime.Controller');

class OASurveyRequests_OASurveyRequestsHandlerForMail_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        $arr_agent_status = array('0' => 'Pending','1' => 'Accepted','2' => 'Removed','3' => 'Declined');
        $db = PearDatabase::getInstance();
        $email = $request->get('email');
        
        if (isset($email) && $request->get("email") == "true") {
            $nro_status = $request->get("status");
            $status = $arr_agent_status[$request->get("status")];
            $db->pquery("UPDATE vtiger_oasurveyrequests SET oasurveyrequests_status=? WHERE oasurveyrequestsid=?", array($status, $request->get("oaid")));
            $db->pquery("UPDATE vtiger_participatingagents SET status=? WHERE rel_crmid=? AND agents_id=? AND agent_type=?", array($nro_status, $request->get("crmid"), $request->get("agent_id"), $request->get("agent_type")));
        }
    }
}

$handler = new OASurveyRequests_OASurveyRequestsHandlerForMail_Action();
$handler->process(new Vtiger_Request($_REQUEST));
