<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Trips_RelationAjax_Action extends Vtiger_RelationAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('addRelation');
        $this->exposeMethod('moveRelation');
        $this->exposeMethod('deleteRelation');
        $this->exposeMethod('getRelatedListPageCount');
    }

    public function checkPermission(Vtiger_Request $request)
    {
    }

    public function preProcess(Vtiger_Request $request)
    {
        return true;
    }

    public function postProcess(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function moveRelation($request)
    {
        $sourceModule = $request->getModule();
        $sourceRecordId = $request->get('src_record');
        $oldSourceRecordId = $request->get('old_src_record');

        $relatedModule = $request->get('related_module');
        $relatedRecordIdList = $request->get('related_record_list');

        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        foreach ($relatedRecordIdList as $relatedRecordId) {
            $relationModel->deleteRelation($oldSourceRecordId, $relatedRecordId);
            $relationModel->addRelation($sourceRecordId, $relatedRecordId);

            //Update task with the new assigned Trip ID
            include_once 'include/Webservices/Revise.php';
            $user = Users_Record_Model::getCurrentUserModel();
	    $tripRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Trips');
	    
            $orderUpdate['id'] = vtws_getWebserviceEntityId('Orders', $relatedRecordId);
            $orderUpdate['orders_assignedtrip'] = 1;
            $orderUpdate['orders_trip'] = vtws_getWebserviceEntityId('Trips', $sourceRecordId);
	    $orderUpdate['driver_trip'] = vtws_getWebserviceEntityId('Employees', $tripRecordModel->get('driver_id'));
	    $orderUpdate['agent_trip'] = vtws_getWebserviceEntityId('Agents', $tripRecordModel->get('agent_unit'));

	    try {
		vtws_revise($orderUpdate, $user);
	    } catch (Exception $exc) {
		echo $exc->getTraceAsString();
		MoveCrm\LogUtils::LogToFile('LOG_CRM_FAILS', "VTWS ERROR = ".$exc->getMessage(), true);
	    }
        }


        //Updating trip total counts
        $tripRecordModel = Vtiger_Record_Model::getInstanceById($oldSourceRecordId, 'Trips');
        $tripRecordModel->recalculateTripsFields($request);

        $tripRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Trips');
        $tripRecordModel->recalculateTripsFields($request);


        $msg = new Vtiger_Response();
        $msg->setResult('Order moved.');
        $msg->emit();
    }

    /*
     * Function to add relation for specified source record id and related record id list
     * @param <array> $request
     * 		keys					Content
     * 		src_module				source module name
     * 		src_record				source record id
     * 		related_module			related module name
     * 		related_record_list		json encoded of list of related record ids
     */

    public function addRelation($request)
    {
        $sourceModule = $request->getModule();
        $sourceRecordId = $request->get('src_record');
        $ispackage = $request->get('ispackage');
        $relatedModule = $request->get('related_module');
        $relatedRecordIdList = $request->get('related_record_list');
		$driver = $request->get('driver_id');
		$agent = $request->get('agent_unit');

        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        foreach ($relatedRecordIdList as $relatedRecordId) {
            $relationModel->addRelation($sourceRecordId, $relatedRecordId);

            if ($relatedModule == 'Orders' && $request->get('calledby') == 'ldd') {
                include_once 'include/Webservices/Revise.php';
                $user = Users_Record_Model::getCurrentUserModel();
		
		$tripRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Trips');
		
                $orderUpdate['id'] = vtws_getWebserviceEntityId('Orders', $relatedRecordId);
                $orderUpdate['orders_assignedtrip'] = ($ispackage == "true") ? 0 : 1;
                $orderUpdate['orders_trip'] = vtws_getWebserviceEntityId('Trips', $sourceRecordId);
		$orderUpdate['driver_trip'] = vtws_getWebserviceEntityId('Employees', $tripRecordModel->get('driver_id'));
		$orderUpdate['agent_trip'] = vtws_getWebserviceEntityId('Agents', $tripRecordModel->get('agent_unit'));
		$orderArray['orders_assignedtrip'] = 1;
                vtws_revise($orderUpdate, $user);

                //Need to update the trip orders count, weight and more

                $tripRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Trips');
                $tripRecordModel->recalculateTripsFields($request);
            }
        }
        $msg = new Vtiger_Response();
        $msg->setResult('ok');
        $msg->emit();
    }

    /**
     * Function to delete the relation for specified source record id and related record id list
     * @param <array> $request
     * 		keys					Content
     * 		src_module				source module name
     * 		src_record				source record id
     * 		related_module			related module name
     * 		related_record_list		json encoded of list of related record ids
     */
    public function deleteRelation($request)
    {
        $sourceModule = $request->getModule();
        $sourceRecordId = $request->get('src_record');

        $relatedModule = $request->get('related_module');
        $relatedRecordIdList = $request->get('related_record_list');

        //Setting related module as current module to delete the relation
        vglobal('currentModule', $relatedModule);

        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        foreach ($relatedRecordIdList as $relatedRecordId) {
            $response = $relationModel->deleteRelation($sourceRecordId, $relatedRecordId);

            if ($relatedModule == 'Orders') {
                include_once 'include/Webservices/Revise.php';
                $user = Users_Record_Model::getCurrentUserModel();
                $orderUpdate['id'] = vtws_getWebserviceEntityId('Orders', $relatedRecordId);
                $orderUpdate['orders_assignedtrip'] = 0;
                $orderUpdate['orders_trip'] = '';
		$orderUpdate['driver_trip'] = '';
		$orderUpdate['agent_trip'] = '';
                vtws_revise($orderUpdate, $user);

                //Need to update the trip orders count, weight and more

                $tripRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Trips');
                $tripRecordModel->recalculateTripsFields($request);
            }
        }
        echo $response;
    }

    /**
     * Function to get the page count for reltedlist
     * @return total number of pages
     */
    public function getRelatedListPageCount(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId = $request->get('record');
        $label = $request->get('tab_label');
        $pagingModel = new Vtiger_Paging_Model();
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
        $totalCount = $relationListView->getRelatedEntriesCount();
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $totalCount / (int) $pageLimit);

        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $result = array();
        $result['numberOfRecords'] = $totalCount;
        $result['page'] = $pageCount;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
