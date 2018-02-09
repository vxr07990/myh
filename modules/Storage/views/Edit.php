<?php

Class Storage_Edit_View extends Vtiger_Edit_View {

    public function checkPermission(Vtiger_Request $request) {
	$moduleName = $request->getModule();
	$record = $request->get('record');

	$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

	if (!$recordPermission) {
	    throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
	}

	if(!empty($record) && $record != ''){
	   $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Storage');
	    $user = Users_Record_Model::getCurrentUserModel();

	    if ($recordModel->get('storage_status') == 'Cancelled' && !is_admin($user)) {
		//Leave the admin access to the record. Someone must have the right to update a cancelled storage
		throw new AppException(vtranslate('LBL_STORAGE_CANCELLED', 'Storage'));
	    }
	}
	
    }
    
     public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        if($extraPermissions[0] != true){
            if($record != null){
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
            }
        }
        */
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        $this->setViewerForGuestBlocks($moduleName, $record, $viewer);
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime     = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
	$structuredValues = $recordStructureInstance->getStructure();
	if(!is_admin(Users_Record_Model::getCurrentUserModel())){
	    unset($structuredValues['LBL_STORAGE_INFORMATION']['storage_status']);
	    unset($structuredValues['LBL_STORAGE_INFORMATION']['storage_datetime_cancelled']);
	    unset($structuredValues['LBL_STORAGE_INFORMATION']['storage_cancelled_user_id']);
	}
	
	
        /* VGS Global Business Line Blocks */
        if (!empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } elseif (empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
            //pull contact from the related thing.
            //OT 13379
            $sourceRecord = $request->get('sourceRecord');
            $sourceModule = $request->get('sourceModule');
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }

}
