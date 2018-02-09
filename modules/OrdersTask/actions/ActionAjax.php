<?php

include_once 'include/Webservices/Revise.php';

class OrdersTask_ActionAjax_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getParticipatingAgentByOwner');
        $this->exposeMethod('getAgentName');
        $this->exposeMethod('isDateBlockedForTask');
        $this->exposeMethod('checkResourcesAvailabilityForDate');
        $this->exposeMethod('removeUnavailableTaskResources');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function getParticipatingAgentByOwner(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $agents = array();
        // Agent Manage Id
        $recordId = $request->get('record');
        $agentManageID = $request->get('agentId');
        $participating_agent = $request->get('participating_agent');

        $select = "SELECT `agentsid`, `agentname`, `agent_number` FROM `vtiger_agents` WHERE `agentmanager_id`=? AND `agentsid`<>?";
        $result = $adb->pquery($select, array($agentManageID,$participating_agent));
        if ($adb->num_rows($result)>0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $agents[] = array(
                    'agentsid'=>$row['agentsid'],
                    'agentname'=>$row['agentname']." (".$row['agent_number'].")",
                );
            }
        }
        $participating_agentModel=Vtiger_Record_Model::getInstanceById($participating_agent);
        $agents[]= array(
            'agentsid'=>$participating_agent,
            'agentname'=>$participating_agentModel->getDisplayName(),
            'selected'=>true,
        );

        $response = new Vtiger_Response();
        $response->setResult($agents);
        $response->emit();
    }
    
    public function isDateBlockedForTask(Vtiger_Request $request){
        $isBlocked = false;
        $date = DateTimeField::convertToDBFormat($request->get('date'));
        $participatingAgent = $request->get('participatingAgent');
        $businessLine = $request->get('businessLine');
        
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = array_keys($currentUser->getAccessibleAgentsForUser());
        
        $holiday = array();
        $sql = "SELECT vtiger_holiday.* FROM vtiger_holiday "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_holiday.holidayid "
                . "WHERE holiday_type = 'Blocked' AND holiday_date = '$date' AND "
                . "(holiday_business_line = 'All' OR holiday_business_line = '$businessLine' ) "
                . "AND deleted = 0 AND vtiger_crmentity.agentid IN ( ". generateQuestionMarks($accesibleAgents)." ) ";
        $result = $db->pquery($sql,$accesibleAgents);
        if($db->num_rows($result) > 0){
            $isBlocked = true;
//            while($row = $db->fetchByAssoc($result)){
//                $holiday[] = $row;
//            }
        }
        $data = array(
            'isBlocked'=>$isBlocked,
        );
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    
    public function checkResourcesAvailabilityForDate(Vtiger_Request $request){
        $allAvailable = true;
        $notAvailableResourcesArray = [];
        $taskId = $request->get('task_id');
        $userFormatDate = $request->get('assigned_date');
        $date = DateTimeField::convertToDBFormat($userFormatDate);
        
        $orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $crew = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_employee')));
        $vehicles = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_vehicles')));
//        $vendors = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_vendor')));
        
        $orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
        if(!empty($crew)){
            $availableEmployees = $orderTaskModel->getEmployeesByDateAndRole($date, $date);
            $availableEmployees = $availableEmployees[$date];
            foreach ($crew as $id){
                if(!isset($availableEmployees[$id])){
                    $allAvailable = false;
                    array_push($notAvailableResourcesArray['Crew'][$id], $id);
                }
            }
        }
        
        if(!empty($vehicles)){
            $availableVehicles = $orderTaskModel->getVehiclesByDateAndType($date, $date);
            $availableVehicles = $availableVehicles[$date];
            foreach ($vehicles as $id) {
                if(!isset($availableVehicles[$id])){
                    $allAvailable = false;
                    array_push($notAvailableResourcesArray['Vehicle'][$id], $id);
                }
            }
        }
        
        //TODO: NewLoadLocalDispatch getVendors needs to be moved to OrdersTask/Module.php and be rebuilt beacause agents/vendors module merge
        if(!empty($vendors)){
            $dateArray['dateFrom'] = $date;
            $availableVendors = $orderTaskModel->getAvailableVendors($vendors, '', $dateArray);
            foreach ($vendors as $id) {
                if(!isset($availableVendors[$id])){
                    $allAvailable = false;
                    array_push($notAvailableResourcesArray['Vendor'][$id], $id);
                }
            }
        }
        
        $msgString = '';
        //make string for popup
        if(!$allAvailable){
            reset($notAvailableResourcesArray);
            $auxModuleName = key($notAvailableResourcesArray);
            $msgString .= $auxModuleName." Message - ";
            foreach ($notAvailableResourcesArray as $moduleName => $data) {
                if($moduleName != $auxModuleName){
                    $msgString = substr($msgString, 0,-2);
                    $msgString .= " is not available on ".$userFormatDate." and will be removed from ".$auxModuleName.".";
                    $msgString .= "<br>".$moduleName." Message - ";
                }
                foreach ($data as $key => $value) {
                    $label = Vtiger_Record_Model::getInstanceById($key)->getName();
                    $msgString .= $label.", ";
                }
                $auxModuleName = $moduleName;
            }
            $msgString = substr($msgString, 0,-2);
            $msgString .= " is not available on ".$userFormatDate." and will be removed from ".$auxModuleName.".";
            $msgString .= "<br>Do you want to continue?";
        }
        
        $data = array(
            'allAvailable'=>$allAvailable,
            'msg'=>$msgString,
            'ids'=>$notAvailableResourcesArray
        );
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    
    public function removeUnavailableTaskResources(Vtiger_Request $request){
        $taskId = $request->get('task_id');
        $resourcesToRemove = $request->get('resources');
        
        $orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $crew = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_employee')));
        $vehicles = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_vehicles')));
        $vendors = array_filter(explode(' |##| ', $orderTaskRecord->get('assigned_vendor')));
        
        foreach ($crew as $key => $value) {
            if(isset($resourcesToRemove['Crew'][$value])){
                unset($crew[$key]);
            }
        }
        foreach ($vehicles as $key => $value) {
            if(isset($resourcesToRemove['Vehicle'][$value])){
                unset($vehicles[$key]);
            }
        }
        //TODO: uncomment this lines when checkResourcesAvailabilityForDate TODO is resolved
//        foreach ($vendors as $key => $value) {
//            if(isset($resourcesToRemove['Vendor'][$value])){
//                unset($vendors[$key]);
//            }
//        }
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $orderTask = [
            'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskId),
            'assigned_employee' => implode(' |##| ', array_filter($crew)),
            'assigned_vehicles' => implode(' |##| ', array_filter($vehicles)),
            'assigned_vendors' => implode(' |##| ', array_filter($vendors)),
            'actual_of_crew' => count($crew),
            'actual_of_vehicles' => count($vehicles)
        ];

        try {
            vtws_revise($orderTask, $currentUser);
            $result = ['result' => 'OK'];
         } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
         }
         
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
}
