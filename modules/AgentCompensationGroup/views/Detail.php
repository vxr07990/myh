<?php

class AgentCompensationGroup_Detail_View extends Vtiger_Detail_View {
    public function showModuleBasicView(Vtiger_Request $request) {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $AgentCompensationItemsModel=Vtiger_Module_Model::getInstance('AgentCompensationItems');
        if($AgentCompensationItemsModel && $AgentCompensationItemsModel->isActive()) {
            $AgentCompensationItemsModel->setViewerForAgentCompensationItems($viewer, $recordId);
        }

        $EscrowsModel=Vtiger_Module_Model::getInstance('Escrows');
        if($EscrowsModel && $EscrowsModel->isActive()) {
            $EscrowsModel->setViewerForEscrows($viewer, $recordId);
        }

        parent::showModuleBasicView($request);
    }

    public function showModuleDetailView(Vtiger_Request $request){
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $AgentCompensationItemsModel=Vtiger_Module_Model::getInstance('AgentCompensationItems');
        if($AgentCompensationItemsModel && $AgentCompensationItemsModel->isActive()) {
            $AgentCompensationItemsModel->setViewerForAgentCompensationItems($viewer, $recordId);
        }

        $EscrowsModel=Vtiger_Module_Model::getInstance('Escrows');
        if($EscrowsModel && $EscrowsModel->isActive()) {
            $EscrowsModel->setViewerForEscrows($viewer, $recordId);
        }

        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);

        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);

    }

}
