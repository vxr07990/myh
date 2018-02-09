<?php

include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';

class Orders_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
		$db = PearDatabase::getInstance();
		if ($request->get('record')) {
			if ($request->get('ordersstatus')) {
				$result    = $db->pquery("SELECT ordersstatus FROM vtiger_orders WHERE ordersid=?", [$request->get('record')]);
				$row       = $result->fetchRow();
				$oldStatus = $row[0];
				if ($oldStatus !== $request->get('ordersstatus')) {
					$orderModel = Vtiger_Module_Model::getInstance('Orders');
					$orderModel->createMilestone($request->get('record'), $oldStatus, $request->get('ordersstatus'));
				}
			}
			$oldContact = false;
			$sql        = "SELECT orders_contacts FROM `vtiger_orders` WHERE ordersid=?";
			$result     = $db->pquery($sql, [$request->get('record')]);
			$row        = $result->fetchRow();
			$oldContact = $row[0];
			$modo       = 'Edit';
			
			$orderModel = Vtiger_Record_Model::getInstanceById($request->get('record'),'Orders');
			if($orderModel->get('orders_trip')){
				$tripRecordModel = Vtiger_Record_Model::getInstanceById($orderModel->get('orders_trip'), "Trips");
				$tripRecordModel->recalculateTripsFields();
			}
		} else {
			$modo = 'Create';
			//Set the dispatch status by default to Unplanned - Since the field is read-only in editview 
			//we cant send this field with form post.
			if(!isset($_REQUEST['orders_otherstatus']) || $request->get('orders_otherstatus') == ''){
			    $request->set('orders_otherstatus','Unplanned');
			}
			
		}
		parent::process($request);
		$sql          = "SELECT ordersid FROM `vtiger_orders` ORDER BY ordersid DESC LIMIT 1";
		$result       = $db->pquery($sql, []);
		$row          = $result->fetchRow();
		$orderid      = $this->getObjectTypeId($db, 'Orders').$row[0];
		$splitOrderId = explode('x', $orderid);
		$splitOrderId = $splitOrderId[1];
		if ($request->get('record')) {
			$splitOrderId = $request->get('record');
		}
		//Let this be handled by saveentity itself.
//		if ($modo == 'Create') {
//			$result     = $db->pquery("SELECT cur_id,prefix FROM vtiger_modentity_num WHERE semodule=?", ['Orders']);
//			$currentSeq = floatval($db->query_result($result, 0, 'cur_id'));
//			$prefix     = $db->query_result($result, 0, 'prefix');
//			$newSeq     = explode($prefix, $request->get('orders_no'));
//			$seq        = floatval($newSeq[1]);
//			if ($seq > $currentSeq) {
//				$new_sequence_number = $seq;
//			} else {
//				$new_sequence_number = $currentSeq;
//			}
//			$sql = $db->pquery("UPDATE vtiger_modentity_num SET cur_id=? WHERE semodule=?", [$new_sequence_number, 'Orders']);
//		}
        CRMEntity::UpdateRelation($splitOrderId, 'Orders', $request->get('orders_contacts'), 'Contacts');
        /*
		if ($oldContact != false) {
			$sql    = "SELECT * FROM `vtiger_crmentityrel` WHERE crmid=? AND relcrmid=?";
			$result = $db->pquery($sql, [$splitOrderId, $oldContact]);
			$row    = $result->fetchRow();
			if ($row != NULL && $request->get('orders_contacts') != $oldContact) {
				$sql    = "DELETE FROM `vtiger_crmentityrel` WHERE relcrmid=? AND crmid=?";
				$result = $db->pquery($sql, [$request->get('orders_contacts'), $splitOrderId]);
				$sql    = "UPDATE `vtiger_crmentityrel` SET relcrmid=? WHERE crmid=? AND relcrmid=?";
				$result = $db->pquery($sql, [$request->get('orders_contacts'), $splitOrderId, $oldContact]);
			} else {
				$sql    = "INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES (?,?,?,?)";
				$result = $db->pquery($sql, [$splitOrderId, 'Orders', $request->get('orders_contacts'), 'Contacts']);
			}
		} else {
			$sql    = "INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES (?,?,?,?)";
			$result = $db->pquery($sql, [$splitOrderId, 'Orders', $request->get('orders_contacts'), 'Contacts']);
		}
        */
		//$this->saveParticipants($request, $splitOrderId); OLD
		//participants save
        // do this in saveentity
//		$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
//		if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
//			$participatingAgentsModel::saveParticipants($_REQUEST, $splitOrderId);
//		}
		//extrastops save
		// now a guest module
//		$extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
//		if ($extraStopsModel && $extraStopsModel->isActive()) {
//			$extraStopsModel->saveStops($request, $splitOrderId);
//		}

		//vehicle lookup save
//		$vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
//		if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
//			file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ')."Preparing to call saveVehicles\n", FILE_APPEND);
//			$vehicleLookupModel::saveVehicles($request);
//		}
	}

    public function saveParticipants($request, $orderRecord)
    {
		//file_put_contents('logs/devLog.log', "\n RECORD: ".$oppRecord, FILE_APPEND);
		/*
		$totalParticipants = $request->get('numAgents');
		
		for($i = 1; $i<=$totalParticipants; $i++){
			
			$agentTypePrev = $request->get('agent_type'.$i.'_prev');
			$orderParticipantsPrev = $request->get('order_participants'.$i.'_prev');
			$participantPermissionPrev = $request->get('participantPermission'.$i.'_prev');
			
			$participantId = $request->get('participantId'.$i);
			$participantIdPrev = $request->get('participantId'.$i.'_prev');
			
			//file_put_contents('logs/devLog.log', "\n PID: ".$participantIdPrev, FILE_APPEND);
			
			$agentType = $request->get('agent_type'.$i);
			$orderParticipants = $request->get('order_participants'.$i);
			$participantPermission = $request->get('participantPermission'.$i);
			
			//file_put_contents('logs/devLog.log', "\n ROW NUM: ".$i, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n RECORD: ".$orderRecord, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n agentTypePrev: ".$agentTypePrev, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n orderParticipantsPrev: ".$orderParticipantsPrev, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n participantPermissionPrev: ".$participantPermissionPrev, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n agentType: ".$agentType, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n orderParticipants: ".$orderParticipants, FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n participantPermission: ".$participantPermission, FILE_APPEND);
			
			if($agentTypePrev == 'none' && $orderParticipantsPrev == 'none' && $participantPermissionPrev == 'none'){
				if(!empty($agentType) || !empty($orderParticipants) || !empty($participantPermission)){
					$db = PearDatabase::getInstance();
					$sql = 'INSERT INTO `vtiger_orders_participatingagents`(ordersid, agentid, agenttype, permissions, participantid) VALUES (?,?,?,?,?)';
					$db->pquery($sql, array($orderRecord, $orderParticipants, $agentType, $participantPermission, $participantId));
				}				
			} else{
				$db = PearDatabase::getInstance();
				$sql = 'UPDATE `vtiger_orders_participatingagents` SET ordersid = ?, agentid = ?, agenttype = ?, permissions = ?, participantid = ? WHERE ordersid = ? AND participantid = ?';
				$db->pquery($sql, array($orderRecord, $orderParticipants, $agentType, $participantPermission, $participantId, $orderRecord, $participantIdPrev));
			}
		}*/
	}

    protected function getObjectTypeId($db, $modName)
    {
		$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";

		$params[] = $modName;

		$result = $db->pquery($sql, $params);

		return $db->query_result($result, 0, 'id').'x';
	}

	

    protected function curlPOST($post_string, $webserviceURL)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webserviceURL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
		$curlResult = curl_exec($ch);
		curl_close($ch);

		return $curlResult;
	}



    protected function curlGET($get_string, $webserviceURL)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webserviceURL.$get_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
		$curlResult = curl_exec($ch);
		curl_close($ch);

		return $curlResult;
	}
}
