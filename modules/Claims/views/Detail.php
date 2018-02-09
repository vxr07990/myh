<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Claims_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);

            return;
        }
        echo $this->showModuleDetailView($request);
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
        global $hiddenBlocksArray, $adb;
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
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        $claimsItemsArray = Claims_Module_Model::getClaimsItemsArr($request);
        $viewer->assign('CLAIMS_ITEMS_COUNT', count($claimsItemsArray));
        $viewer->assign('CLAIMS_ITEMS_ARRAY', $claimsItemsArray);
        $viewer->assign('CLAIMS_ITEMS_ARRAY_HEADER', Claims_Module_Model::getClaimsItemsArrHeader($request));
        
        $summaryTable = Claims_Module_Model::getSummaryTable($recordId);
        $viewer->assign('SUMMARY_TABLE', $summaryTable);
        $viewer->assign('FLAG', "Detail");
        
        $paymentList = Claims_Module_Model::getPaymentList($recordId);
        $viewer->assign('PAYMENT_LIST', $paymentList);
        
        $statusChangeList = Claims_Module_Model::getStatusChangeList($recordId, $recordModel->get("claims_status_statusgrid"), $recordModel->get("claims_reason_statusgrid"));
        $viewer->assign('STATUS_LIST', $statusChangeList);
    //logic to include the participating agents block
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $viewer->assign('SERVICE_PROVIDER_RESPO', true);
            //call to get db data
        $list = Claims_Module_Model::getGridItems('spr', $recordId);
            $viewer->assign('SERVICE_PROVIDER_LIST', $list);
        }
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
