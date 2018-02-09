<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');
        $recordModel = $this->record;
        $viewer      = $this->getViewer($request);
        if (!$recordModel) {
            if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $viewer->assign('BRAND_FIELD_MODEL', $recordModel->get('brand'));
                }
                //file_put_contents('logs/devLog.log', 'If Statement', FILE_APPEND);
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $vanline = '';
                    if (!$db) {
                        $db = \PearDatabase::getInstance();
                    }
                    $currentUser = Users_Record_Model::getCurrentUserModel();
                    /*$sql       = 'SELECT UNIQUE `vtiger_vanlinemanager`.`vanline_id`
                                        FROM `vtiger_groups`
                                        JOIN `vtiger_agentmanager`
                                            ON `vtiger_agentmanager`.`agency_name` LIKE `vtiger_groups`.`groupname`
                                        JOIN `vtiger_vanlinemanager`
                                            ON `vtiger_vanlinemanager`.`vanlinemanagerid` = `vtiger_agentmanager`.`vanline_id`
                                        JOIN `vtiger_users2group`
                                            USING (`groupid`)
                                        WHERE `userid` = ?
                                        LIMIT 1';*/
                    //$vanlineId = $db->pquery($sql, Users_Record_Model::getCurrentUserModel()->get('id'));
                    $vanlines = $currentUser->getAccessibleVanlinesForUser();
                    $vanlineId = key($vanlines);
                    $sql = "SELECT vanline_id FROM `vtiger_vanlinemanager` WHERE vanlinemanagerid=?";
                    $result = $db->pquery($sql, [$vanlineId]);
                    $row = $result->fetchRow();
                    $vanlineId = $row['vanline_id'];
                    if ($vanlineId == 1) {
                        $vanline = 'AVL';
                    } else {
                        $vanline = 'NAVL';
                    }
                    $viewer->assign('BRAND_FIELD_MODEL', $vanline);
                }
            }
        }
        $salutationFieldModel = Vtiger_Field_Model::getInstance('salutationtype', $recordModel->getModule());
        // Fix for http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7851
        $salutationType = $request->get('salutationtype');
        if (!empty($salutationType)) {
            $salutationFieldModel->set('fieldvalue', $request->get('salutationtype'));
        } else {
            $salutationFieldModel->set('fieldvalue', $recordModel->get('salutationtype'));
        }
        $viewer->assign('SALUTATION_FIELD_MODEL', $salutationFieldModel);
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');

        if ($request->get('isDuplicate')) {
            $viewer->assign('IS_DUPLICATE', 'yes');
        }

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
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        foreach ($recordStructureInstance->getStructure() as $blockName => $blockFields) {
            $surveyTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                if (($fieldNameTest === 'survey_time' || $fieldNameTest === 'survey_end_time') && $fieldModelTest->get('fieldvalue') !== '') {
                    $time = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
                    if ($fieldNameTest === 'survey_time') {
                        $surveyTime = $fieldModelTest->get('fieldvalue');
                    }
                    $fieldModelTest->set('fieldvalue', $time);
                }
                if ($fieldNameTest === 'survey_date' && $fieldModelTest->get('fieldvalue') !== '') {
                    if ($surveyTime === '') {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$blockFields['survey_time']->get('fieldvalue'))->format('Y-m-d');
                    } else {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$surveyTime)->format('Y-m-d');
                    }
                    $fieldModelTest->set('fieldvalue', $date);
                }
            }
        }
        //End Time Zone Conversion
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
        //TODO: get this up in vtiger_edit_view to make it automagical
        $this->setViewerForGuestBlocks($moduleName, $record, $viewer);
        $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['EmployeeRoles','Employees']);

        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $viewer->assign('COMPS', $recordModel->getPricingCompetitors());
        }
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($record));
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));

        $parentRecordId = $request->get('record');
        if ($parentRecordId) {
            $commentRecordId = $request->get('commentid');
            $moduleName = $request->getModule();
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');

            $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);

            if (!empty($commentRecordId)) {
                $currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
            }

            $viewer = $this->getViewer($request);
            $viewer->assign('CURRENTUSER', $currentUserModel);
            $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
            $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
            $viewer->assign('CURRENT_COMMENT', $currentCommentModel);
        }

        if (getenv('INSTANCE_NAME') != 'graebel') {
            //logic to include MoveRoles
            $MoveRolesModel = Vtiger_Module_Model::getInstance('MoveRoles');
            if ($MoveRolesModel && $MoveRolesModel->isActive()) {
                $viewer->assign('MOVEROLES_MODULE_MODEL', $MoveRolesModel);
                $viewer->assign('MOVEROLES_LIST', $MoveRolesModel->getMoveRoles($record));
            }
        }

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$record);
        }

        if (getenv('INSTANCE_NAME') == 'sirva') {
            setDefaultCoordinator($recordModel, $viewer);
        }

        $viewer->view('EditView.tpl', $moduleName);
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
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        if(getenv('INSTANCE_NAME') != 'graebel') {
            $MoveRolesModel = Vtiger_Module_Model::getInstance('MoveRoles');
            if ($MoveRolesModel && $MoveRolesModel->isActive()) {
                $jsFileNames[] = "modules.MoveRoles.resources.EditBlock";
            }
        }
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames = [
                "modules.VehicleLookup.resources.Edit",
            ];
        }
        $jsFileNames[] = "modules.Vtiger.resources.MoveType";
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = "modules.Opportunities.resources.MilitaryFields";
        }
        $addressListModule=Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $jsFileNames[] = "modules.AddressList.resources.EditBlock";
        }
        $jsFileNames[] = 'modules.Vtiger.resources.DaysToMove';
        $jsFileNames[] = "modules.Vtiger.resources.SalesPerson";
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

}
