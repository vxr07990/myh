<?php

class TariffServices_Detail_View extends Vtiger_Detail_View
{
    public function preProcess(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
    }

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
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        file_put_contents('logs/TariffServicesLoadTest.log', print_r($recordModel, true)."\n", FILE_APPEND);
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('DEFAULTCOUNTIES', $recordModel->getDefaultCountiesExistingRecord());
        $viewer->assign('DEFAULTBULKIES', $recordModel->getDefaultBulkies());
        $viewer->assign('DEFAULTPACKING', $recordModel->getDefaultPacking());
        $viewer->assign('BASEPLUS', $recordModel->getEntries('baseplus'));
        $viewer->assign('BREAKPOINT', $recordModel->getEntries('breakpoint'));
        $viewer->assign('WEIGHTMILEAGE', $recordModel->getEntries('weightmileage'));
        $viewer->assign('SERVICECHARGEMATRIX', $recordModel->get('service_base_charge_matrix'));
        $viewer->assign('SERVICECHARGE', $recordModel->getEntries('servicebasecharge'));
        $viewer->assign('BULKYITEMS', $recordModel->getEntries('bulky'));
        $viewer->assign('CHARGESPERHUNDRED', $recordModel->getEntries('chargeperhundred'));
        $viewer->assign('COUNTYCHARGES', $recordModel->getEntries('countycharge'));
        $viewer->assign('HOURLYSET', $recordModel->getEntries('hourlyset'));
        $viewer->assign('PACKINGITEMS', $recordModel->getEntries('packingitems'));
        $viewer->assign('VALUATIONITEMS', $recordModel->getEntries('valuations'));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }
}
