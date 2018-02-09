<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contracts_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
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
        //$vanlineModel = Vtiger_Module_Model::getInstance('VanlineManager');
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
        $db = PearDatabase::getInstance();
        /*-----------------------------Grab annual rate increases---------------------------------*/
        $annualRateIncrease = [];
        $result             = $db->pquery('SELECT * FROM `vtiger_annual_rate` WHERE contractid = ?', [$record]);
        $row                = $result->fetchRow();
        while ($row != null) {
            if($request->get('isDuplicate'))
            {
                $row['annualrateid'] = '0';
            }
            $annualRateIncrease[] = $row;
            //file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
            $row = $result->fetchRow();
        }
        $viewer->assign('ANNUAL_RATES', $annualRateIncrease);
        /*-----------------------------End annual rate increases----------------------------------*/
        /*-----------------------------Grab fuel table---------------------------------*/
        //@TODO: why is this conditionalized in edit and detail view but not save_entitiy?
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $fuelTable = [];
            $result    = $db->pquery('SELECT * FROM `vtiger_contractfuel` WHERE contractid = ?', [$record]);
            $row       = $result->fetchRow();
            while ($row != null) {
                $fuelTable[] = $row;
                //file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
                $row = $result->fetchRow();
            }
            $viewer->assign('FUEL_TABLE', $fuelTable);
            $subAgreementLabel = strrpos($request->get('tab_label'), 'Sub-');
            if ($subAgreementLabel !== false || $recordModel->get('parent_contract')) {
                $viewer->assign('SUB', true);
                if ($fieldList['move_type']) {
                    $typeOfData = $fieldList['move_type']->get('typeofdata')? :'V~O';
                    $typeOfData = preg_replace('/~O/i', '~M', $typeOfData);
                    $fieldList['move_type']->set('typeofdata', $typeOfData);
                }
            } else {
                $viewer->assign('SUB', false);
                if ($fieldList['move_type']) {
                    $fieldList['move_type']->set('presence', 1);
                }
            }
            //auto populate the source Record.
            //Somewhere it does it for the nat_account_no, but I can not find where.
            //OHHHH it's on the get query string... OK i'm adding it in here in case that URL changes.
            if ($request->get('sourceRecord')) {
                if ($request->get('sourceModule') == 'Accounts') {
                    $accountRecordId = $request->get('sourceRecord');
                    try {
                        //ensure it's an Accounts record before setting it.
                        if ($request->get('sourceRecord')) {
                            $actRecord = Vtiger_Record_Model::getInstanceById($accountRecordId, 'Accounts');
                            if (!$request->get('account_id') && !$recordModel->get('account_id')) {
                                $recordModel->set('account_id', $actRecord->getId());
                            }
                            if (!$request->get('nat_account_no') && !$recordModel->get('nat_account_no')) {
                                $recordModel->set('nat_account_no', $actRecord->get('apn'));
                            }
                        }
                    } catch (Exception $e) {
                        //actually do nothing it's OK
                    }
                } elseif ($request->get('sourceModule') == 'Contracts') {
                    try {
                        //@TODO: probably like do this better with a database table... but right now it seems one off.
                        //pull the parent record and get it's related account or apn.
                        $parentRecord = Vtiger_Record_Model::getInstanceById($request->get('sourceRecord'), 'Contracts');
                        if (!$request->get('account_id') && !$recordModel->get('account_id')) {
                            $recordModel->set('account_id', $parentRecord->get('account_id'));
                        }
                        if (!$request->get('nat_account_no') && !$recordModel->get('nat_account_no')) {
                            $recordModel->set('nat_account_no', $parentRecord->get('nat_account_no'));
                        }
                        //I was going to stop there... but then I thought you know they are going to ask.
                        if (!$request->get('contact_id') && !$recordModel->get('contact_id')) {
                            $recordModel->set('contact_id', $parentRecord->get('contact_id'));
                        }
                        if (!$request->get('begin_date') && !$recordModel->get('begin_date')) {
                            $recordModel->set('begin_date', $parentRecord->get('begin_date'));
                        }
                        if (!$request->get('phone') && !$recordModel->get('phone')) {
                            $recordModel->set('phone', $parentRecord->get('phone'));
                        }
                    } catch (Exception $e) {
                        //again do nothing because not auto populating a value isn't the end of the world.
                    }
                }
            }
        }
        /*-----------------------------End fuel table----------------------------------*/

        /*-----------------------BEGIN tabled Flat Auto rate----------------------------*/
        if (getenv('INSTANCE_NAME') != 'sirva') {
            //Flat Rate Auto table
            $sql    = "SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE `contractid` =?";
            $result    = $db->pquery($sql, [$record]);
            $row       = $result->fetchRow();
            while ($row != null) {
                if($request->get('isDuplicate'))
                {
                    $row['line_item_id'] = 'none';
                }
                $flatRateAutoTable[] = $row;
                $row = $result->fetchRow();
            }
            $viewer->assign('FLAT_RATE_AUTO_TABLE', $flatRateAutoTable);
            $r = $recordModel->getFuelLookupTable($request->get('isDuplicate'));
            $viewer->assign('FUEL_TABLE', $r);
        }
        /*-----------------------End tabled Flat Auto rate----------------------------*/


        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        //Doubled up because some things expected MODULE_NAME instead of MODULE
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MISC_CHARGES', $recordModel->getMiscCharges($request));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('ASSIGNED_RECORDS', $recordModel->getAssignedRecords());
        $vanlineRecords = VanlineManager_Module_Model::getAllRecords();
        $viewer->assign('VANLINES', $vanlineRecords);
        $viewer->assign('AGENTS', AgentManager_Module_Model::getAllRecords());
        $vanlineNames = [];
        foreach ($vanlineRecords as $vanlineRecord) {
            $vanlineNames[$vanlineRecord->get('id')] = $vanlineRecord->get('vanline_name');
        }
        $viewer->assign('VANLINE_NAMES', $vanlineNames);
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $vanlineOwner = false;
        $db           = PearDatabase::getInstance();
        $sql          = "SELECT vtiger_groups.grouptype FROM `vtiger_crmentity` INNER JOIN `vtiger_groups` ON vtiger_crmentity.smownerid = vtiger_groups.groupid WHERE vtiger_crmentity.crmid = ?";
        $result       = $db->pquery($sql, [$record]);
        $row          = $result->fetchRow();
        if ($row[0] == 1) {
            $vanlineOwner = true;
        }
        $viewer->assign('VANLINE_OWNER', $vanlineOwner);
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            "modules.Contracts.resources.AnnualRateIncrease",
        ];
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = "modules.Contracts.resources.BaseSirva";
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
