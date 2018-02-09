<?php

class MenuCreator_Detail_View extends Vtiger_Detail_View {
    public function showModuleBasicView(Vtiger_Request $request) {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $MenuGroupsModel=Vtiger_Module_Model::getInstance('MenuGroups');
        if($MenuGroupsModel && $MenuGroupsModel->isActive()) {
            $viewer->assign('MENUGROUPS_MODULE_MODEL', $MenuGroupsModel);
            $MenuGroupsModel->setViewerForMenuGroups($viewer, $recordId);
        }
		
		$MenuCreatorModel = Vtiger_Module_Model::getInstance($moduleName);
		$selectedModelModules = $MenuCreatorModel->getMenuEditorModules($recordId);
        
		$selectedModules = [];
		foreach($selectedModelModules as $key => $moduleModel){
			array_push($selectedModules, $moduleModel->getName());
		}
		
        $viewer->assign('SELECTED_MODULES', implode(", ",$selectedModules));
		
        return parent::showModuleBasicView($request);
    }

    public function showModuleDetailView(Vtiger_Request $request){
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $MenuGroupsModel=Vtiger_Module_Model::getInstance('MenuGroups');
        if($MenuGroupsModel && $MenuGroupsModel->isActive()) {
            $viewer->assign('MENUGROUPS_MODULE_MODEL', $MenuGroupsModel);
            $MenuGroupsModel->setViewerForMenuGroups($viewer, $recordId);

        }

		$MenuCreatorModel = Vtiger_Module_Model::getInstance($moduleName);
		$selectedModelModules = $MenuCreatorModel->getMenuEditorModules($recordId);
        
		$selectedModules = [];
		foreach($selectedModelModules as $key => $moduleModel){
			array_push($selectedModules, $moduleModel->getName());
		}
		
        $viewer->assign('SELECTED_MODULES', implode(", ",$selectedModules));
		
        return parent::showModuleDetailView($request);
    }

}