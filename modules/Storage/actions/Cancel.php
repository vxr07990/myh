<?php

include_once 'include/Webservices/Revise.php';

class Storage_Cancel_Action extends Vtiger_BasicAjax_Action {

    function process(Vtiger_Request $request) {
	$recordId = $request->get('storageid');
	$now = gmdate('Y-m-d H:i:s');
	$user = Users_Record_Model::getCurrentUserModel();
	$userId = $user->id;
	$db = PearDatabase::getInstance();
	if (!empty($recordId)) {
	    $storageRecordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Storage');
	    if ($storageRecordModel->get('storage_status') != 'Cancelled') {

		$data['id'] = vtws_getWebserviceEntityId('Storage', $recordId);
		$data['storage_status'] = 'Cancelled';
		$data['storage_cancelled_user_id'] = vtws_getWebserviceEntityId('Users', $userId);
		$data['storage_datetime_cancelled'] = $now;

		try {
		    vtws_revise($data, $user);
		    $detailViewUrl = Vtiger_Module_Model::getInstance('Storage')->getDetailViewUrl($recordId);
		    $response = new Vtiger_Response();
		    $response->setResult($detailViewUrl);
		    $response->emit();
		} catch (Exception $exc) {

		    $response = new Vtiger_Response();
		    $response->setError(1001, $exc->getMessage());
		    $response->emit();
		}
	    } else {
		$response = new Vtiger_Response();
		$response->setError(1001, 'Already Cancelled');
		return $response;
	    }
	}
    }
}
