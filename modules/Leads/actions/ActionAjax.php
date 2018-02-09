<?php

class Leads_ActionAjax_Action extends Vtiger_ActionAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('countNewLeads');
        $this->exposeMethod('getBusinessLineByAccount');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function countNewLeads(Vtiger_Request $request)
    {
        $result = array();
        if (getenv('IGC_MOVEHQ') || getenv('INSTANCE_NAME') == 'national') {
            global $adb, $current_user;
            $recordLead = $request->get("recordlead");
            $query = $adb->pquery("SELECT * FROM `vtiger_leaddetails` a INNER JOIN `vtiger_crmentity` b ON b.crmid = a.leadid WHERE a.leadid =?",array($recordLead));
            $agentid = '';
            if($adb->num_rows($query)>0)
            {
                $agentid = $adb->query_result($query,0,'agentid');
            }
            $userModel = Users_Record_Model::getInstanceById($current_user->id, 'Users');
            $listAgentManager = $userModel->getAccessibleOwnersForUser('Leads');
            $newListAgentManager = $listAgentManager;
            unset($newListAgentManager['agents']);
            unset($newListAgentManager['vanlines']);
            $listAgent = array_keys($newListAgentManager);
            if (!in_array($agentid,$listAgent))
            {
                array_push($listAgent,$agentid);
            }

            $count=0;
            $rs=$adb->pquery("select COUNT(leadid) as new_leads
                    from vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
                    WHERE deleted=0 AND leadstatus ='New'
                    AND vtiger_leaddetails.converted = 0
                    AND `agentid`IN (".generateQuestionMarks(array_keys($listAgent)).")", array($listAgent));
            if ($adb->num_rows($rs)>0) {
                $count=$adb->query_result($rs, 0, 'new_leads');
            }
            $result['count'] = $count;
        } else {
            $result['count'] = 'NOT_IGC_MOVEHQ';
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    public function getBusinessLineByAccount(Vtiger_Request $request){
        global $adb;
        $ValAccount = $request->get('ValAccount');
        $AccRecordModel = Vtiger_Record_Model::getInstanceById($ValAccount);
        $businessLine  = $AccRecordModel->get('business_line');
        $results = array();
        if ($businessLine !=''){
            $businessLine = explode(' |##| ', $businessLine);
            foreach ($businessLine as $key){
                $results[] = $key;
            }
        }
        else{
            $accountModule = Vtiger_Module_Model::getInstance('Accounts');
            $fieldModel = $accountModule->getField('business_line');
            $results = $fieldModel->getPicklistValues();
        }
        $response = new Vtiger_Response();
        $response->setResult($results);
        $response->emit();

    }
}
