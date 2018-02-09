<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AgentManager_RelationAjax_Action extends Vtiger_RelationAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('addRelation');
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

    /*
     * Function to add relation for specified source record id and related record id list
     * @param <array> $request
     *		keys					Content
     *		src_module				source module name
     *		src_record				source record id
     *		related_module			related module name
     *		related_record_list		json encoded of list of related record ids
     */
    public function addRelation($request)
    {
        $sourceModule = $request->getModule();

        $sourceRecordId = $request->get('src_record');

        $relatedModule = $request->get('related_module');

        $relatedRecordIdList = $request->get('related_record_list');

        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        
        $db = PearDatabase::getInstance();

        foreach ($relatedRecordIdList as $relatedRecordId) {
            $relationModel->addRelation($sourceRecordId, $relatedRecordId);
            
            //grab agent name
            $sql = "SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
            $result = $db->pquery($sql, array($sourceRecordId));
            $row = $result->fetchRow();
            $agentName = $row[0];
            
            //grab agent group id
            $sql = "SELECT groupid FROM `vtiger_groups` WHERE groupname=?";
            $result = $db->pquery($sql, array($agentName));
            $row = $result->fetchRow();
            $groupId = $row[0];
            
            //check to see if user already exists in users2group
            $sql = "SELECT groupid, userid FROM `vtiger_users2group` WHERE groupid=? AND userid=?";
            $result = $db->pquery($sql, array($groupId, $relatedRecordId));
            $row = $result->fetchRow();
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_users2group` VALUES (?,?)";
                $result = $db->pquery($sql, array($groupId, $relatedRecordId));
            }
        }
    }

    /**
     * Function to delete the relation for specified source record id and related record id list
     * @param <array> $request
     *		keys					Content
     *		src_module				source module name
     *		src_record				source record id
     *		related_module			related module name
     *		related_record_list		json encoded of list of related record ids
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
