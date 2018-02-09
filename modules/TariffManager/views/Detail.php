<?php

class TariffManager_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        //file_put_contents('logs/TariffManagerDetail.log', date('Y-m-d H:i:s - ')."Entering showModuleDetailView function\n", FILE_APPEND);
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

        $valuations = $recordModel::getValuationSetting($recordId);
        if (!empty($valuations)) {
            $viewer->assign('VALUATIONS', $recordModel::getValuationSetting($recordId));
        }

        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('ASSIGNED_RECORDS', $recordModel->getAssignedRecords());
        $vanlineRecords = VanlineManager_Module_Model::getAllRecords();
        $viewer->assign('VANLINES', $vanlineRecords);
        $viewer->assign('AGENTS', AgentManager_Module_Model::getAllRecords());
        $vanlineNames = [];
        foreach ($vanlineRecords as $vanlineRecord) {
            $vanlineNames[$vanlineRecord->get('id')] = $vanlineRecord->get('vanline_name');
        }
        $viewer->assign('VANLINE_NAMES', $vanlineNames);
        //file_put_contents('logs/TariffManagerDetail.log', date('Y-m-d H:i:s - ')."Preparing to view template file\n", FILE_APPEND);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
