<?php

class AgentCompensationGroup_Edit_View extends Vtiger_Edit_View {
    public function process(Vtiger_Request $request) {
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

        $viewer->assign('MODULE_MODEL',Vtiger_Module_Model::getInstance($moduleName));
        parent::process($request);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $EscrowsModel=Vtiger_Module_Model::getInstance('Escrows');
        $jsFileNames = [];
        if ($EscrowsModel && $EscrowsModel->isActive()) {
            $jsFileNames[] = "modules.Escrows.resources.EditBlock";
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}