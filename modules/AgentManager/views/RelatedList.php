<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AgentManager_RelatedList_View extends Vtiger_RelatedList_View
{

    function process(Vtiger_Request $request)
    {
        $moduleName        = $request->getModule();
        $relatedModule = $request->get('relatedModule');
        $parentId          = $request->get('record');
        $relatedMethod = $request->get('relatedMethod');
        if (empty($relatedMethod) || $relatedMethod != 'Edit'){
            $relatedMethod = 'Detail';
        }

        if($relatedModule == 'TimeCalculator') {
            $viewer             = $this->getViewer($request);

            global $adb;
            // Get TimeCalculator record based on current Agent Manager
            $agentId=$request->get('record');
            $query="SELECT  `crmid`
                FROM `vtiger_crmentity`
                WHERE deleted=0 AND setype=? and `agentid` = ? ORDER BY crmid DESC LIMIT 1";
            $rs=$adb->pquery($query,array('TimeCalculator',$agentId));

            if($adb->num_rows($rs)) {
                $recordId = $adb->query_result($rs,0,'crmid');
            }


            $LongCarriesModel=Vtiger_Module_Model::getInstance('LongCarries');
            if($LongCarriesModel && $LongCarriesModel->isActive()) {
                $viewer->assign('LONGCARRIES_MODULE_MODEL', $LongCarriesModel);
                $LongCarriesModel->setViewerForLongCarries($viewer, $recordId);
            }

            $FlightsModel=Vtiger_Module_Model::getInstance('Flights');
            if($FlightsModel && $FlightsModel->isActive()) {
                $viewer->assign('FLIGHTS_MODULE_MODEL', $FlightsModel);
                $FlightsModel->setViewerForFlights($viewer, $recordId);
            }

            $ElevatorsModel=Vtiger_Module_Model::getInstance('Elevators');
            if($ElevatorsModel && $ElevatorsModel->isActive()) {
                $viewer->assign('ELEVATORS_MODULE_MODEL', $ElevatorsModel);
                $ElevatorsModel->setViewerForElevators($viewer, $recordId);
            }

            if($recordId) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $relatedModule);
            }else{
                $recordModel = Vtiger_Record_Model::getCleanInstance($relatedModule);
            }

            $moduleModel     = $recordModel->getModule();
            $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
            $viewer->assign('RECORD_ID', $recordId);
            $viewer->assign('AGENT_ID', $parentId);
            $viewer->assign('RELATEDMODULE_NAME', $relatedModule);
            $viewer->assign('MODE', 'edit');
            $viewer->assign('MODULE', 'TimeCalculator');
            $viewer->assign('METHOD', $relatedMethod);
            $viewer->assign('RECORD', $recordModel);
            $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
            $viewer->assign('TIMECALCULATOR_RECORD_STRUCTURE', $recordStructureInstance->getStructure());
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
            return $viewer->view('RelatedList.tpl', $moduleName, 'true');
        }elseif ($relatedModule == 'RevenueGrouping') {
            return $this->showRevenueGroupingEditView($request, $request->get('editblock'));

        } else {
            return parent::process($request);
        }
    }

    function showRevenueGroupingEditView(Vtiger_Request $request, $edit=false)
    {
        global $adb;
        $viewer = $this->getViewer($request);
        $moduleName = $request->get('relatedModule');
        $agentId = $request->get('record');
        $rs = $adb->pquery("SELECT * FROM vtiger_agentmanager 
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid= vtiger_agentmanager.agentmanagerid
                WHERE agentmanagerid = ? AND deleted=0",[$agentId]);
        if($adb->num_rows($rs) > 0){
            $revenueGroupingId = $adb->query_result($rs,0,'revenuegroupingid');
            $query = $adb->pquery("SELECT * FROM  `vtiger_crmentity` WHERE crmid =? AND deleted=?",array($revenueGroupingId,0));
            if ($adb->num_rows($query)> 0){
                $revenueGroupingId = $adb->query_result($rs,0,'revenuegroupingid');
            }
            else{
                $revenueGroupingId = '';
            }
        }




        if (!empty($revenueGroupingId)) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($revenueGroupingId, $moduleName);
            $viewer->assign('RECORD_ID', $revenueGroupingId);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);

        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            $specialField = false;

            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $arrRecordStructure = $recordStructureInstance->getStructure();
        $arrRecordStructure['LBL_REVENUEGROUPINGDETAIL']['agentid']->set('fieldvalue',$agentId);
        //logic to include RevenueGroupingItem
        $revenueGroupingItemModel = Vtiger_Module_Model::getInstance('RevenueGroupingItem');
        if ($revenueGroupingItemModel && $revenueGroupingItemModel->isActive()) {
            $viewer->assign('REVENUEGROUPINGITEM_MODULE_MODEL', $revenueGroupingItemModel);
            $viewer->assign('REVENUEGROUPINGITEM_BLOCK_FIELDS', $revenueGroupingItemModel->getFields('LBL_REVENUEGROUPINGITEMSDETAIL'));
            $viewer->assign('REVENUEGROUPINGITEM_LIST', $revenueGroupingItemModel->getRevenueGroupingItem($revenueGroupingId));
            $viewer->assign('REVENUEGROUPINGITEM_LIST_DEFAULT', RevenueGrouping_Edit_View::$revenueGroupingItemDefault);
        }

        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $arrRecordStructure);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('HIDDEN_BLOCKS', ['LBL_REVENUEGROUPINGDETAIL']);
        $isRelationOperation = 1;
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        $viewer->assign('SOURCE_MODULE', $request->getModule());
        $viewer->assign('SOURCE_RECORD', $agentId);
        if($edit) {
            $viewer->view('EditViewBlocks.tpl', $moduleName);
        }else{
            $viewer->view('DetailViewBlockView.tpl', $moduleName);
        }

    }
}
