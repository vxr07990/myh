<?php

class AgentManager_Detail_View extends Vtiger_Detail_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('manageCoordinators');
    }

    public function process(Vtiger_Request $request) {
        $recordId                = $request->get('record');
        $moduleName              = $request->getModule();
        $recordModel             = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $viewer                  = $this->getViewer($request);
        $viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());
        return parent::process($request);
    }

    function manageCoordinators(Vtiger_Request $request) {
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
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_ID', $recordId);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('CoordinatorListDetail.tpl', $moduleName, true);
    }

    function showModuleDetailView(Vtiger_Request $request) {
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
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
            $viewer->assign('PRESHIP_CHECKLIST', $vehicleLookupModel::getChecklist($recordId));
        }
        //logic to include CapacityCalendarCounter
        $CapacityCalendarCounterModel = Vtiger_Module_Model::getInstance('CapacityCalendarCounter');
        if ($CapacityCalendarCounterModel && $CapacityCalendarCounterModel->isActive()) {
            $viewer->assign('CAPACITYCALENDARCOUNTER_LIST', $CapacityCalendarCounterModel->getCapacityCalendarCounter($recordId));
            $viewer->assign('CAPACITYCALENDARCOUNTER_MODULE_MODEL', $CapacityCalendarCounterModel);
            $viewer->assign('CAPACITYCALENDARCOUNTER_BLOCK_FIELDS', $CapacityCalendarCounterModel->getFields('LBL_CAPACITYCALENDARCOUNTER_INFORMATION'));
        }

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    function showModuleBasicView($request) {
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
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
            $viewer->assign('PRESHIP_CHECKLIST', $vehicleLookupModel::getChecklist($recordId));
        }
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        //logic to include CapacityCalendarCounter
        $CapacityCalendarCounterModel = Vtiger_Module_Model::getInstance('CapacityCalendarCounter');
        if ($CapacityCalendarCounterModel && $CapacityCalendarCounterModel->isActive()) {
            $viewer->assign('CAPACITYCALENDARCOUNTER_LIST', $CapacityCalendarCounterModel->getCapacityCalendarCounter($recordId));
            $viewer->assign('CAPACITYCALENDARCOUNTER_MODULE_MODEL', $CapacityCalendarCounterModel);
        }
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames = [
                "modules.VehicleLookup.resources.Detail",
            ];
        } else {
            $jsFileNames = [];
        }

        $TimeCalculatorModel=Vtiger_Module_Model::getInstance('TimeCalculator');
        if ($TimeCalculatorModel && $TimeCalculatorModel->isActive()) {
            $jsFileNames[] = "modules.Vtiger.resources.Edit";
            $jsFileNames[] = "modules.TimeCalculator.resources.RelatedEdit";
            $LongCarriesModel = Vtiger_Module_Model::getInstance('LongCarries');
            if ($LongCarriesModel && $LongCarriesModel->isActive()) {
                $jsFileNames[] = "modules.LongCarries.resources.EditBlock";
            }

            $FlightsModel = Vtiger_Module_Model::getInstance('Flights');
            if ($FlightsModel && $FlightsModel->isActive()) {
                $jsFileNames[] = "modules.Flights.resources.EditBlock";
            }

            $ElevatorsModel = Vtiger_Module_Model::getInstance('Elevators');
            if ($ElevatorsModel && $ElevatorsModel->isActive()) {
                $jsFileNames[] = "modules.Elevators.resources.EditBlock";
            }
        }

        $revenueGroupingModel    = Vtiger_Module_Model::getInstance('RevenueGroupingItem');
        if ($revenueGroupingModel && $revenueGroupingModel->isActive()) {
            $jsFileNames[]="modules.RevenueGroupingItem.resources.EditBlock";
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
