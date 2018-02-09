<?php

class ItemCodes_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $ItemCodesMappingModel=Vtiger_Module_Model::getInstance('ItemCodesMapping');
        if ($ItemCodesMappingModel && $ItemCodesMappingModel->isActive()) {
            $viewer->assign('ITEMCODESMAPPING_MODULE_MODEL', $ItemCodesMappingModel);
            $viewer->assign('IS_DUPLICATE', $request->get('isDuplicate'));
            $ItemCodesMappingModel->setViewerForItemCodesMapping($viewer, $recordId);
        }

        parent::process($request);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $ItemCodesMappingModel=Vtiger_Module_Model::getInstance('ItemCodesMapping');
        $jsFileNames = [];
        if ($ItemCodesMappingModel && $ItemCodesMappingModel->isActive()) {
            $jsFileNames[] = "modules.ItemCodesMapping.resources.EditBlock";
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
