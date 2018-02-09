<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AutoSpotQuote_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $record = $request->get('record');

        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
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
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
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
            if ($moduleName == 'Calendar') {
                if ($sourceRecord) {
                    if ($sourceModule == 'Estimates') {
                        $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                        $sourceContactId = $sourceRecordModel->get('contact_id');
                        $existingRelatedContacts[] = array(
                            'name' => Vtiger_Util_Helper::getRecordName($sourceContactId),
                            'id' => $sourceContactId
                        );
                    }
                }
            }
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->assign('AUTO_QUOTE_INFO', $recordModel->get('auto_quote_info'));
        $viewer->assign('AUTO_QUOTE_SELECT', $recordModel->get('auto_quote_select'));
        $viewer->assign('AUTO_QUOTE_ID', $recordModel->get('auto_quote_id'));

        //Can't use nested objects in Smarty, because that was a good choice.. instead we will assinge 16 variables, instead of just one.
        $rateInfo = json_decode(urldecode($recordModel->get('auto_quote_info')));
        $viewer->assign('AUTO_QUOTE_10_load', $rateInfo->rates->ten_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_10_from', $rateInfo->rates->ten_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_10_to', $rateInfo->rates->ten_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_10_price', $rateInfo->rates->ten_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_7_load', $rateInfo->rates->seven_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_7_from', $rateInfo->rates->seven_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_7_to', $rateInfo->rates->seven_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_7_price', $rateInfo->rates->seven_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_4_load', $rateInfo->rates->four_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_4_from', $rateInfo->rates->four_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_4_to', $rateInfo->rates->four_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_4_price', $rateInfo->rates->four_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_2_load', $rateInfo->rates->two_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_2_from', $rateInfo->rates->two_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_2_to', $rateInfo->rates->two_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_2_price', $rateInfo->rates->two_day_pickup->price);

        $viewer->view('EditView.tpl', $moduleName);
    }
}
