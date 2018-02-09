<?php

class OrdersTask_ShowModals_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showColumnsModal');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
            return;
        }
    }

	function showColumnsModal(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
		
		$relatedModuleName = $request->get("relModule");
		$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
		$relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
		$eventBlocksFields = $relatedRecordStructureInstance->getStructure();
		
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', "OrdersTask");
		$viewer->assign('CURRENT_USER_FULL_NAME', $currentUser->get('first_name').' '.$currentUser->get('last_name'));
        $viewer->assign('CURRENT_USER_ID', $currentUser->getId());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', array_filter($eventBlocksFields));
		$viewer->assign('relModule', $relatedModuleName);
		

		
		$toEdit = $request->get("toEdit");
		if($toEdit){
			$result = $db->pquery("SELECT columns,filter_name,default_filter FROM vtiger_localdispatch_selectedcolumns WHERE id = ?", array($toEdit));
			if($result && $db->num_rows($result)){
				$result = $result->FetchRow();
				$viewer->assign('toEdit', $toEdit);
				$viewer->assign('columns', $result['columns']);
				$viewer->assign('filterName', $result['filter_name']);
				$viewer->assign('defaultFilter', $result['default_filter']);
			}
		}else{
			$viewer->assign('toEdit', "0");
			$viewer->assign('columns', "");
			$viewer->assign('filterName', "");
			$viewer->assign('defaultFilter', "0");
		}
		
        echo $viewer->view('SelectColumnsModal.tpl', "OrdersTask", true);
	}
}