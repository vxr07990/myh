<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ClaimsSummary_Detail_View extends Vtiger_Detail_View
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
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        //Comments

        $parentRecordId = $request->get('record');
        $commentRecordId = $request->get('commentid');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');

        $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);

        if (!empty($commentRecordId)) {
            $currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
        }

        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
        $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
        $viewer->assign('CURRENT_COMMENT', $currentCommentModel);

        //Claim Types associate to this Claim
	
	

        $claimTypeList = $recordModel->getAssociatedClaims($recordId);
        $viewer->assign('CLAIM_TYPE_LIST', $claimTypeList);

        $claimTypeSummaryGrids = $recordModel->getClaimTypeSummaryGrids($claimTypeList);
        $viewer->assign('CLAIM_TYPE_SUMMARY_GRIDS', $claimTypeSummaryGrids);

        //Claim Payment info

        $claimTypePaymentList = $this->getAssociatedClaimsPayments($recordId, $claimTypeList);
        $viewer->assign('CLAIM_TYPE_PAYMENT_DETAILS_LIST', $claimTypePaymentList);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }


    public function getAssociatedClaimsPayments($claimsSummaryID, $claims) {
	$db = PearDatabase::getInstance();
	$claimTypePaymentList = [];

	foreach ($claims as $claim) {
	    $claim_type = $claim['claim_type'];
	    $claim_id = $claim['claim_id'];
	    $cash = 0;
	    $repair = 0;
	    $inspection = 0;
	    $goodwill = 0;
	    $erroromission = 0;
	    $amount_denied = 0;
	    $result = $db->pquery("SELECT sett.* FROM vtiger_claimitems it INNER JOIN vtiger_claimitems_settlementamount sett ON it.claimitemsid = sett.claimitemsid INNER JOIN vtiger_crmentity cr ON sett.claimitemsid = cr.crmid WHERE cr.deleted = 0 AND it.linked_claim = ?", array($claim_id));
	    if ($result && $db->num_rows($result) > 0) {
		while ($arr = $db->fetch_array($result)) {
		    $amount_denied += intval($arr['amount_denied']);
		    switch ($arr['payment_type']) {
			case "Cash":
			    $cash += floatval($arr['amount']);
			    break;
			case "Repair":
			    $repair += floatval($arr['amount']);
			    break;
			case "Inspection":
			    $inspection += floatval($arr['amount']);
			    break;
			case "Goodwill":
			    $goodwill += floatval($arr['amount']);
			    break;
			case "Error/Omission":
			    $erroromission += floatval($arr['amount']);
			    break;
		    }
		}
	    }

	    $total = $cash + $repair + $inspection + $goodwill + $erroromission;
	    $claimTypePaymentList[] = array("claim_id" => $claim_id, "claim_type" => $claim_type, "cash" => $cash, "repair" => $repair, "inspection" => $inspection, "goodwill" => $goodwill, "erroromission" => $erroromission, "amount_denied" => $amount_denied, "total" => $total);
	}
	return $claimTypePaymentList;
    }



}
