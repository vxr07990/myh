<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LeadSourceManager_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');
        $isRelationOperation = $request->get('relationOperation');
        $isDuplicate     = $request->get('isDuplicate');

        //TODO: needto add sourceModule to the Edit link.
        if ($sourceModule != 'AgentManger') {
            //FAIL with an error!
        }

        if (!empty($record) && $isDuplicate == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        //So empty(record) says it's a new creation, if it's got a source then put the parent agent
        //into the clean record model so it preselects those field.
        if (empty($record) && $sourceModule && $sourceRecord) {
            $agentRecordModel = $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);

            //AgentManger doesn't have agentid ... amusingly.
            if ($sourceModule == 'AgentManager') {
                //use the agentmanager's crmid as the agentid.  (this will always be the same as $sourceRecord)
                $srcAgent = $sourceRecordModel->getId();
            } else {
                $srcAgent = $sourceRecordModel->get('agentid');
                $agentRecordModel = Vtiger_Record_Model::getInstanceById($srcAgent);
            }

            //as these are set. these three are going to be DetailView display types with hidden inputs
            $fieldModel = $fieldList['agentid'];
            $recordModel->set('agentid', $fieldModel->getDBInsertValue($srcAgent));

            $fieldModel = $fieldList['agency_code'];
            $recordModel->set('agency_code', $fieldModel->getDBInsertValue($agentRecordModel->get('agency_code')));

            $fieldModel = $fieldList['brand'];
            $recordModel->set('brand', $fieldModel->getDBInsertValue($agentRecordModel->getBrand()));

            $fieldModel = $fieldList['vanline_related'];
            $recordModel->set('vanline_related', $fieldModel->getDBInsertValue($agentRecordModel->get('vanline_id')));
        }

        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        /* VGS Global Business Line Blocks */
        if (!empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } elseif (empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        //if it is relation edit
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $sourceModule);
            $viewer->assign('SOURCE_RECORD', $sourceRecord);
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }
}
