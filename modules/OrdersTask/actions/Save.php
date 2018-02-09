<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		
		if($request->get('disp_assignedstart') == '' && $request->get('record') == ''){
		    $agentDispatchAssignedTime = $this->getDispatchDefaultTime($request);
		    if($agentDispatchAssignedTime){
			$request->set('disp_assignedstart', $agentDispatchAssignedTime); //(string) 08:00:00
			//unset($_REQUEST['disp_actualend']);
		    }
		}
		$hours = ($request->get('estimated_hours') == '') ? "0" : intval($request->get('estimated_hours'));
				
		$string_add = "+".$hours. " hour";
		
		$date = date("Y-m-d H:i:s",strtotime($request->get('disp_assignedstart')));
		$date = date('H:i:s',strtotime($string_add,strtotime($date)));
				
		$request->set('disp_actualend', $date);
                
		if($request->get('date_spread') == "0"){
			$serviceDateFrom = $request->get('service_date_from');
			$request->set('disp_assigneddate', $serviceDateFrom);
		}
		
		if($request->get('record') == ''){
			$request->set('dispatch_status', 'Requested');
		}



		
//                if($request->get('dispatch_status') == 'Accepted' && $request->get('service_date_from') != '' && !$request->get('date_spread') && !$request->get('multiservice_date')){
//                    $request->set('disp_assigneddate', $request->get('service_date_from'));
//                }
	    
	    
		$recordModel = $this->saveRecord($request);
		$recordModel->saveExtraTableBlocks($request);
		$pseudo = $request->get('pseudoSave') == '1';
		$reportSave = $request->get('reportSave') == '1';
		
		//we are doubling this up because some things MIGHT have their own saveRecord.
		$recordId = $recordModel->getId();
		$request->set('record', $recordId);

        if($request->get('relationOperation') && $request->get('sourceModule') == 'Opportunities' && $recordModel->getModule()->getName() == 'Orders')
        {
            $loadUrl = $recordModel->getDetailViewUrl();
        }
        else if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentRecordId = $request->get('sourceRecord');
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
			//TODO : Url should load the related list instead of detail view of record
			$detailViewLinkParams = array('MODULE'=>$parentModuleName,'RECORD'=>$parentRecordId);
			$detailViewModel = Vtiger_DetailView_Model::getInstance($parentModuleName, $parentRecordId);
			$detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
			$currentModuleName = $request->get('module');
			$relatedLinks = $detailViewLinks['DETAILVIEWRELATED'];
			$loadUrl = null;
			foreach($relatedLinks as $relatedLink){
				if($relatedLink->relatedModuleName == $currentModuleName){
					$loadUrl = $relatedLink->linkurl;
					$tabLabel = $relatedLink->linklabel;
					$loadUrl = "index.php?".$loadUrl."&tab_label=".$tabLabel;
					//file_put_contents('logs/devLog.log', "\n LINK: ".$loadUrl, FILE_APPEND);
				}
			}
			if(empty($loadUrl)){
				$loadUrl = $parentRecordModel->getDetailViewUrl();
			}
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
		if(!$pseudo && !$reportSave) {
			header("Location: $loadUrl");
		}
	}
	
	function getDispatchDefaultTime(Vtiger_Request $request){
	    $db = PearDatabase::getInstance();
	    
	    if($request->get('participating_agent') == ''){
		return false;
	    }
	    
	    $result = $db->pquery('SELECT order_task_start_time, timezone FROM vtiger_agentmanager 
					INNER JOIN vtiger_agents ON vtiger_agentmanager.agentmanagerid = vtiger_agents.agentmanager_id 
					INNER JOIN vtiger_fieldtimezonerel ON (vtiger_agentmanager.agentmanagerid = vtiger_fieldtimezonerel.crmid AND fieldid="order_task_start_time")
					WHERE vtiger_agents.agentsid=?', [$request->get('participating_agent')]);
	    
	    if($result && $db->num_rows($result)){
		//$user = Users_Record_Model::getCurrentUserModel();
		//$timeZone = $user->time_zone?$user->time_zone:$default_timezone;
		$time = DateTimeField::convertToUserTimeZone($db->query_result($result, 0, 'order_task_start_time'));
		
		return $time->format('H:i:s');
	    } else {
		return false;
	    }
	}
	
}
