<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PushNotifications_Detail_View extends Vtiger_Detail_View
{
    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $summaryInfo    = [];
        // Take first block information as summary information
        $stucturedValues = $recordStrucure->getStructure();
        foreach ($stucturedValues as $blockLabel => $fieldList) {
            $summaryInfo[$blockLabel] = $fieldList;
            break;
        }
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $navigationInfo       = ListViewSession::getListViewNavigation($recordId);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('NAVIGATION', $navigationInfo);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $found        = false;
        if ($navigationInfo) {
            foreach ($navigationInfo as $page => $pageInfo) {
                foreach ($pageInfo as $index => $record) {
                    //If record found then next record in the interation
                    //will be next record
                    if ($found) {
                        $nextRecordId = $record;
                        break;
                    }
                    if ($record == $recordId) {
                        $found = true;
                    }
                    //If record not found then we are assigning previousRecordId
                    //assuming next record will get matched
                    if (!$found) {
                        $prevRecordId = $record;
                    }
                }
                //if record is found and next record is not calculated we need to perform iteration
                if ($found && !empty($nextRecordId)) {
                    break;
                }
            }
        }
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if (!empty($prevRecordId)) {
            $viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
        }
        if (!empty($nextRecordId)) {
            $viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
        }
        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));
        $linkParams = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);
        $viewer->assign('MODULE_NAME', $moduleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKLIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('UNSAVED_RECORD', $request->get('unsaved'));
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }
}
