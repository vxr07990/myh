<?php

class ItemCodes_Detail_View extends Vtiger_Detail_View
{
    public function showModuleBasicView(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $ItemCodesMappingModel=Vtiger_Module_Model::getInstance('ItemCodesMapping');
        if ($ItemCodesMappingModel && $ItemCodesMappingModel->isActive()) {
            $viewer->assign('ITEMCODESMAPPING_MODULE_MODEL', $ItemCodesMappingModel);
            $ItemCodesMappingModel->setViewerForItemCodesMapping($viewer, $recordId);
        }

        return parent::showModuleBasicView($request);
    }

    public function showModuleDetailView(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $ItemCodesMappingModel=Vtiger_Module_Model::getInstance('ItemCodesMapping');
        if ($ItemCodesMappingModel && $ItemCodesMappingModel->isActive()) {
            $viewer->assign('ITEMCODESMAPPING_MODULE_MODEL', $ItemCodesMappingModel);
            $ItemCodesMappingModel->setViewerForItemCodesMapping($viewer, $recordId);
        }

        return parent::showModuleDetailView($request);
    }
}
