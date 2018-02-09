<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Project_Detail_View extends Vtiger_Detail_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showRelatedRecords');
        $this->exposeMethod('getResources');
        $this->exposeMethod('deleteRelatedResource');
        $this->exposeMethod('editRelatedResource');
    }

    public function showModuleSummaryView($request)
    {
        $recordId       = $request->get('record');
        $moduleName     = $request->getModule();
        $recordModel    = Vtiger_Record_Model::getInstanceById($recordId);
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $viewer         = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_INFORMATION', $recordModel->getSummaryInfo());
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);

        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel     = $this->record->getRecord();
        $recordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStructure->getStructure();
        $moduleModel      = $recordModel->getModule();
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        foreach ($structuredValues as $blockName => $blockFields) {
            $surveyTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                if (($fieldNameTest === 'survey_time' || $fieldNameTest === 'survey_end_time') && $fieldModelTest->get('fieldvalue') !== '') {
                    $time = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
                    if ($fieldNameTest === 'survey_time') {
                        $surveyTime = $fieldModelTest->get('fieldvalue');
                    }
                    $fieldModelTest->set('fieldvalue', $time);
                }
                if ($fieldNameTest === 'survey_date' && $fieldModelTest->get('fieldvalue') !== '') {
                    if ($surveyTime === '') {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$blockFields['survey_time']->get('fieldvalue'))->format('Y-m-d');
                    } else {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$surveyTime)->format('Y-m-d');
                    }
                    $fieldModelTest->set('fieldvalue', $date);
                }
            }
        }
        //End Time Zone Conversion
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    /**
     * Function returns related records based on related moduleName
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showRelatedRecords(Vtiger_Request $request)
    {
        $parentId          = $request->get('record');
        $pageNumber        = $request->get('page');
        $limit             = $request->get('limit');
        $relatedModuleName = $request->get('relatedModule');
        $orderBy           = $request->get('orderby');
        $sortOrder         = $request->get('sortorder');
        $whereCondition    = $request->get('whereCondition');
        $moduleName        = $request->getModule();
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "icon-chevron-down";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "icon-chevron-up";
        }
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
        if (!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder', $sortOrder);
        }
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        if ($whereCondition) {
            $relationListView->set('whereCondition', $whereCondition);
        }
        $models = $relationListView->getEntries($pagingModel);
        $header = $relationListView->getHeaders();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_RECORDS', $models);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleName);
        $viewer->assign('RELATED_MODULE_MODEL', Vtiger_Module_Model::getInstance($relatedModuleName));
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
    }

    /**
     * Function to get Resources related to project tasks
     *
     * @param Vtiger_Request $request
     *
     * @return <html of resources list>
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function getResources(Vtiger_Request $request)
    {
        $moduleModel    = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $resourcesTable = $moduleModel->getResources($request);

        return $resourcesTable;
    }

    /**
     * Function to delete a resource related to a project tasks
     *
     * @param Vtiger_Request $request
     *
     * @return <html of resources list>
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function deleteRelatedResource(Vtiger_Request $request)
    {
        $moduleModel = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $moduleModel->deleteResourceFromTask($request);
        $resourcesTable = $moduleModel->getResources($request);

        return $resourcesTable;
    }

    /**
     * Function to edit a resource allocation
     *
     * @param Vtiger_Request $request
     *
     * @return <html of resources list>
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function editRelatedResource(Vtiger_Request $request)
    {
        $moduleModel = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $moduleModel->deleteResourceFromTask($request);
    }
}
