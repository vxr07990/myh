<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */ 
 
class Settings_RelatedRecordCount_ModuleChangeAjax_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        $settingModel = new Settings_RelatedRecordCount_EditViewAjax_Model();

        $active_module = $request->get('modulename');

        $listRelatedModules = $settingModel->getRelatedModules($active_module);
        $active_related_module = $listRelatedModules[0]['modulename'];

        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('LIST_RELATED_MODULES', $listRelatedModules);
        $viewer->assign('ACTIVE_RELATED_MODULE', $active_related_module);

        $viewer->view('ListRelatedModules.tpl', $qualifiedModuleName);

        die;
    }
}