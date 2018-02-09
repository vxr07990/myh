<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Detail_View extends Accounts_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $viewer      = $this->getViewer($request);
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $viewer->assign('COMPS', $recordModel->getPricingCompetitors());
        }
        parent::process($request);
    }

    public function showModuleSummaryView($request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel    = $recordModel->getModule();
        $viewer         = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
        }
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    /**
     * Function shows the entire detail for the record
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        if ($recordModel->get('lead_type') != 'National Account') {
            $recordModel->hiddenBlocks[count($recordModel->hiddenBlocks)] = 'LBL_LEADS_NATIONALACCOUNT';
        }

        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();

        file_put_contents('logs/myLog.log', "\n  Test: ".print_r($moduleModel->getBlocks(), true), FILE_APPEND);

        $blocks = $moduleModel->getBlocks();
        if ($recordModel->get('lead_type') != 'National Account') {
            unset($blocks['LBL_LEADS_NATIONALACCOUNT']);
        }

        $viewer           = $this->getViewer($request);
        if($recordModel->get('billing_type') == 'NAT')
        {
            $viewer->assign('HIDE_EMPLOYEE_ASSISTING', 1);
        }
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $blocks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        if ($moduleName == 'Users') {
            $viewer->assign('IS_OI_ENABLED', $this->record->getRecord()->getOIEnabled());
        }
//        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
//        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
//            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
//        }

        $parentRecordId = $request->get('record');
        $commentRecordId = $request->get('commentid');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');

        $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);

        if (!empty($commentRecordId)) {
            $currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
        }
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);

        $viewer = $this->getViewer($request);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
        $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
        $viewer->assign('CURRENT_COMMENT', $currentCommentModel);

        if (getenv('INSTANCE_NAME') != 'graebel') {
        //logic to include MoveRoles
        $MoveRolesModel = Vtiger_Module_Model::getInstance('MoveRoles');
        if ($MoveRolesModel && $MoveRolesModel->isActive()) {
            $viewer->assign('MOVEROLES_MODULE_MODEL', $MoveRolesModel);
            $viewer->assign('MOVEROLES_LIST', $MoveRolesModel->getMoveRoles($recordId));
            }
        }

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$recordId);
        }
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames           = [
            "~/libraries/fullcalendar/fullcalendar.js",
            "~/libraries/jquery/colorpicker/js/colorpicker.js",
            "modules.Leads.resources.LeadCalendarView",
            "modules.VehicleLookup.resources.Detail",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/libraries/fullcalendar/fullcalendar.css',
            '~/libraries/fullcalendar/fullcalendar-bootstrap.css',
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}
