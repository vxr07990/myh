<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ClaimItems_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $viewer     = $this->getViewer($request);
        $db         = PearDatabase::getInstance();
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
        $paymentList = [];
        if (!empty($record)) {
            $settlementAmountList = $this->getSettlementAmount($record);
        }
        $viewer->assign('SETTLEMENT_AMOUNT_LIST', $settlementAmountList);
        $originalConditionsList = [];
        if (!empty($record)) {
            $originalConditionsList = $this->getOriginalConditions($record);
        }
        $viewer->assign('ORIGINAL_CONDITION_LIST', $originalConditionsList);
        //participants block
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $viewer->assign('SERVICE_PROVIDER_RESPO', true);
            //call to get db data
            $list = Claims_Module_Model::getGridItems('spr', $record);
            $viewer->assign('SERVICE_PROVIDER_LIST', $list);
        }
        $dailyExpensesList = [];
        if (!empty($record)) {
            $dailyExpensesList = Claims_Module_Model::getGridItems('daily_expenses', $record);
        }
        $viewer->assign('DAILY_EXPENSES_LIST', $dailyExpensesList);
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
        $structuredValues = $recordStructureInstance->getStructure();
        $structuredValues = $this->hideFieldsFromClaimType($structuredValues, $recordModel->get("linked_claim"));
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $qty = 0;
        foreach ($structuredValues as $arr) {
            if (count($arr) > 0) {
                $qty++;
            }
        }
        $viewer->assign('QTY', $qty);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('ORIGINAL_CONDITION_SHOW', ClaimItems_Module_Model::displayLikeInventoryItems($recordModel->get("linked_claim")));
	
	if(!empty($record)){
        $claimType = ClaimItems_Module_Model::getClaimType($recordModel->get("linked_claim"));
	    $claimTypeModel = Vtiger_Record_Model::getInstanceById($recordModel->get("linked_claim"), 'Claims');
	    $claimSummaryId = $claimTypeModel->get('claimssummary_id');
	}  else {
	    $claimType = ClaimItems_Module_Model::getClaimType($request->get("linked_claim"));
	    $claimTypeModel = Vtiger_Record_Model::getInstanceById($request->get("linked_claim"), 'Claims');
	    $claimSummaryId = $claimTypeModel->get('claimssummary_id');
	}
	
        if ($claimType == 'Service Recovery') {
            $viewer->assign('DAILY_EXPENSES_SHOW', true);
        } else {
            $viewer->assign('DAILY_EXPENSES_SHOW', false);
        }
        
	$viewer->assign('CLAIM_TYPE', $claimType);
	$viewer->assign('CLAIM_SUMMARY_ID', $claimSummaryId);
	
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function hideFieldsFromClaimType($structuredValues, $claimID)
    {
        $arr = ClaimItems_Module_Model::getClaimItemsHiddenFields($claimID);

        foreach ($arr as $value) {
            foreach ($structuredValues as $key => $val) {
                if ($val[$value]) {
                    unset($structuredValues[$key][$value]);
                }
            }
        }
        return $structuredValues;
    }
    
    public function getOriginalConditions($claimItemsID)
    {
        $db = PearDatabase::getInstance();
        $originalConditionsList = [];
                
        $result = $db->pquery("SELECT * FROM vtiger_claimitems_originalconditions WHERE claimitemsid = ?", array($claimItemsID));
        while ($arr = $db->fetch_array($result)) {
            $originalConditionsList[] = array("original_condition_id" => $arr[id], "inventory_number" => $arr[inventory_number], "tag_color" => $arr[tag_color], "original_conditions" => $arr[original_conditions], "exceptions" => $arr[exceptions], "date_taken" => $arr[date_taken]);
        }
        return $originalConditionsList;
    }
    
    public function getSettlementAmount($claimItemsID)
    {
        $db = PearDatabase::getInstance();
        $settlementAmountList = [];
        
        $result = $db->pquery("SELECT * FROM vtiger_claimitems_settlementamount WHERE claimitemsid = ?", array($claimItemsID));
        while ($arr = $db->fetch_array($result)) {
            $settlementAmountList[] = array("settlementAmountId" => $arr[id], "paymentType" => $arr[payment_type], "amount" => $arr[amount], "amountDenied" => $arr[amount_denied], "itemOmitted" => $arr[item_omitted]);
        }
        return $settlementAmountList;
    }
}
