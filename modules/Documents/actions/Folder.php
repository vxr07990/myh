<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Folder_Action extends Vtiger_Action_Controller
{
	public function __construct()
			    {
		parent::__construct();
		$this->exposeMethod('save');
		$this->exposeMethod('delete');
	}
	
	public function checkPermission(Vtiger_Request $request)
			    {
		$moduleName = $request->getModule();
		
		if (!Users_Privileges_Model::isPermitted($moduleName, 'DetailView')) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}
	
	public function process(Vtiger_Request $request)
			    {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
		}
	}
	
	public function save($request)
		{
		$moduleName = $request->getModule();
		$folderName = $request->get('foldername');
		$agentid = $request->get('agentmanager_id');
		
		$result = array();
		
		if (!empty($folderName)) {
			$moduelInstance = Vtiger_Module_Model::getInstance($moduleName);
			$fieldModel = Vtiger_Field_Model::getInstance('foldername',$moduelInstance);
			if ($this->checkDuplicate($folderName, $agentid, $fieldModel->id)) {
				throw new AppException(vtranslate('LBL_FOLDER_EXISTS', $moduleName));
				exit;
			}
			
			try{
				
				$id = PicklistCustomizer_Module_Model::addPickListValues($fieldModel, $folderName, $agentid);
				$result = array('success'=>true, 'message'=>vtranslate('LBL_FOLDER_SAVED', $moduleName));
			}
			catch(Exception $exc){
				$result = array('success'=>false, 'message'=>vtranslate('LBL_ERROR_SAVING_FOLDER', $moduleName));
			}
			
			$response = new Vtiger_Response();
			$response->setResult($result);
			$response->emit();
		}
	}
	
	
	public function delete($request)
			    {
		$moduleName = $request->getModule();
		$folderId = $request->get('folderid');
		$result = array();
		
		if (!empty($folderId)) {
			$folderModel = Documents_Folder_Model::getInstanceById($folderId);
			if (!($folderModel->hasDocuments())) {
				$folderModel->delete();
				$result = array('success'=>true, 'message'=>vtranslate('LBL_FOLDER_DELETED', $moduleName));
			}
			else {
				$result = array('success'=>false, 'message'=>vtranslate('LBL_FOLDER_HAS_DOCUMENTS', $moduleName));
			}
		}
		
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	public function validateRequest(Vtiger_Request $request)
			    {
		$request->validateWriteAccess();
	}
	
	
	
	
        /**
        * Function returns duplicate record status of the module
        * @return true if duplicate records exists else false
        */
        
		public function checkDuplicate($folderName, $agentId, $fieldId)
		{
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT 1 FROM vtiger_picklistexceptions WHERE value = ? AND agentid=? AND fieldid=?", array($folderName, $agentId, $fieldId));
		$num_rows = $db->num_rows($result);
		
		if ($num_rows > 0) {
			return true;
		}
		
		return false;
	}
	
}
