<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class MenuCreator_Module_Model extends Vtiger_Module_Model {
	
    public function getMenuEditorModules($recordId){
        $db = PearDatabase::getInstance();
		$selectedModules = [];
		
		$query = $db->pquery("SELECT * FROM vtiger_menueditor WHERE menucreator_id = ?", array($recordId));
		
		if($db->num_rows($query) > 0){
			$ids = explode(",",$db->query_result($query, 0, "selected_modules"));
			foreach($ids as $id){
				$auxQuery = $db->pquery("SELECT * FROM vtiger_tab WHERE tabid = ?", array($id));
				$row = $db->query_result_rowdata($auxQuery);
				$selectedModules[$id] = Vtiger_Module_Model::getInstanceFromArray($row);
			}
		}
        
		return $selectedModules;
    }
	
	public function getAllModules(){
		$moduleModelList = $returnModuleList = [];
		
		$allModelsList = Vtiger_Module_Model::getAll(array('0','2'));
		$allModelsList = MenuGroups_Module_Model::returnMenuModels($allModelsList, true);
		
		foreach($allModelsList as $module){
			$translatedModule = vtranslate($module, $module);
			$returnModuleList[$translatedModule] = Vtiger_Module_Model::getInstance($module);

		}
		
		return $returnModuleList;
	}
	
	public function saveMenuEditor($request,$relId) {
		$db = PearDatabase::getInstance();
		
		$selectedModules_orders = array();
		$selectedModules_orders = $request['menuListSelectElement_selected_order'];
		if ($selectedModules_orders) {
			$selectedModules = implode(",",json_decode($selectedModules_orders, true));
		}else{
			$selectedModules = implode(",",$request['menuListSelectElement']);
		}
		
		$query = $db->pquery("SELECT * FROM vtiger_menueditor WHERE menucreator_id = ?", array($relId));
		if($db->num_rows($query) > 0){
			$sql = "UPDATE vtiger_menueditor SET selected_modules = ? WHERE menucreator_id = ?";
		}else{
			$sql = "INSERT INTO vtiger_menueditor(selected_modules, menucreator_id) VALUES (?,?)";
		}
		$db->pquery($sql, array($selectedModules, $relId));
	}
	
	function returnMenuModels(){
		
	}
}