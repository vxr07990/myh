<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ClaimItems_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;

    public function __construct()
    {
	parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
	$mode = $request->getMode();
	if (!empty($mode)) {
	    echo $this->invokeExposedMethod($mode, $request);

	    return;
	}
	echo $this->showModuleDetailView($request);
    }

    /**
     * Function shows the entire detail for the record
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showModuleDetailView(Vtiger_Request $request)
    {
	global $hiddenBlocksArray, $adb;
	$recordId = $request->get('record');
	$moduleName = $request->getModule();
	if (!$this->record) {
	    $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
	}
	$recordModel = $this->record->getRecord();        
	$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
	if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
	    $hiddenBlocks = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
	    $recordModel->hiddenBlocks = $hiddenBlocks;
	}
	$structuredValues = $recordStrucure->getStructure();
        $structuredValues = $this->hideFieldsFromClaimType($structuredValues, $recordModel);
        
	$moduleModel = $recordModel->getModule();
	$viewer = $this->getViewer($request);
	$viewer->assign('RECORD', $recordModel);
	$viewer->assign('RECORD_STRUCTURE', $structuredValues);
	$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
	$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
	$viewer->assign('MODULE_NAME', $moduleName);
	$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

	$settlementAmountList = $this->getSettlementAmount($recordId);
	$viewer->assign('SETTLEMENT_AMOUNT_LIST', $settlementAmountList);

        $viewer->assign('DAILY_EXPENSES_SHOW', false);
        $displayOrigionalCond = ClaimItems_Module_Model::displayLikeInventoryItems($recordModel->get("linked_claim"));
    
        if (strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') === false && strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') === false) {
            $viewer->assign('ORIGINAL_CONDITION_SHOW', false);
            $viewer->assign('ORIGINAL_CONDITION_LIST', array());
            $viewer->assign('DAILY_EXPENSES_SHOW', true);
        } elseif ($displayOrigionalCond) {
            $viewer->assign('ORIGINAL_CONDITION_LIST', $this->getOriginalConditions($recordId));
            $viewer->assign('ORIGINAL_CONDITION_SHOW', true);
        } else {
            $viewer->assign('ORIGINAL_CONDITION_LIST', array());
            $viewer->assign('ORIGINAL_CONDITION_SHOW', false);
        }

	//logic to include the participating agents block
	$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
	if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
	    $viewer->assign('SERVICE_PROVIDER_RESPO', true);
	    //call to get db data
        $list = Claims_Module_Model::getGridItems('spr', $recordModel->getId());
	    $viewer->assign('SERVICE_PROVIDER_LIST', $list);
	}

    
        $claimType = ClaimItems_Module_Model::getClaimType($recordModel->get("linked_claim"));
    
    
        if ($claimType == 'Service Recovery' && strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') === false && strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') === false) {
            $dailyExpensesList = [];
            if (!empty($recordId)) {
                $dailyExpensesList = Claims_Module_Model::getGridItems('daily_expenses', $recordId);
            }
        }
    
        
	return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
    
    public function hideFieldsFromClaimType($structuredValues, $recordModel)
    {
        $arr = ClaimItems_Module_Model::getClaimItemsHiddenFields($recordModel->get("linked_claim"));
        if ($recordModel->get('claimitemsdetails_claimantrequest') !== "Cash") {
            array_push($arr, "claimitemsdetails_amount");
        }
    
    //Service recovery speficic fields
    $claimType = ClaimItems_Module_Model::getClaimType($recordModel->get("linked_claim"));
    
        if ($claimType == 'Service Recovery') {
            $arr2 = ClaimItems_Module_Model::getServiceRecoveryFields($recordModel);
            $arr = array_merge($arr, $arr2);
        }

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
	if ($result && $db->num_rows($result) > 0) {
	    while ($arr = $db->fetch_array($result)) {
		$settlementAmountList[] = array("settlementAmountId" => $arr[id], "paymentType" => $arr[payment_type], "amount" => $arr[amount], "amountDenied" => $arr[amount_denied], "itemOmitted" => $arr[item_omitted]);
	    }
	}

	return $settlementAmountList;
    }
}
