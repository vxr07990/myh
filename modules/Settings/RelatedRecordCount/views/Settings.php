<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
ini_set('display_errors','0');
class Settings_RelatedRecordCount_Settings_View extends Settings_Vtiger_Index_View {
    public function process(Vtiger_Request $request) {
        $this->renderSettingsUI($request);
    } 

    public function renderSettingsUI(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        $ajax = $request->get('ajax');
        $viewer = $this->getViewer($request);

        $settingModel = new Settings_RelatedRecordCount_Settings_Model();
        $entities = $settingModel->getData();

        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('ENTITIES', $entities);
        $viewer->assign('COUNT_ENTITY', count($entities));

        if($ajax){
            $viewer->view('SettingsAjax.tpl', $qualifiedModuleName);
        }else{
            $viewer->view('Settings.tpl', $qualifiedModuleName);
        }
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            'modules.Vtiger.resources.Vtiger',
            'modules.Settings.Vtiger.resources.Vtiger',
            'modules.Settings.Vtiger.resources.Edit',
            "modules.Settings.$moduleName.resources.Settings",
            "libraries/jquery/colorpicker/js/colorpicker",
            "libraries/jquery/colorpicker/js/eye",
            "libraries/jquery/colorpicker/js/utils"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);

        $cssFileNames = array(
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
            '~/layouts/vlayout/modules/Settings/RelatedRecordCount/resources/RelatedRecordCount.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}