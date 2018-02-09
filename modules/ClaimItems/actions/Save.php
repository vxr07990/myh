<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ClaimItems_Save_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
	
	$parentRecordModel = Vtiger_Record_Model::getInstanceById($request->get('linked_claim'), 'Claims');
	$request->set('agentid', $parentRecordModel->get('agentid'));

	$recordModel = $this->saveRecord($request);

	//we are doubling this up because some things MIGHT have their own saveRecord.
	$recordId = $recordModel->getId();


	$claimItemsId = $recordId;
	$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
	if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
	    $this->saveSPR($_REQUEST, $recordId);
	}
	$this->saveSettlementAmount($request, $recordId);
	$this->saveOriginalConditions($request, $recordId);
	$this->saveDailyExpenses($request, $recordId);

	$parentClaim = $recordModel->get('linked_claim');

	$parentClaimRecordModel = Vtiger_Record_Model::getInstanceById($parentClaim, 'Claims');
	$loadUrl = $parentClaimRecordModel->getDetailViewUrl();
	header("Location: $loadUrl");
    }

    public function saveSPR($fieldList, $recordId)
    {
	$db = PearDatabase::getInstance();
	for ($index = 0; $index <= $fieldList['numSPR']; $index++) {
	    if (!$fieldList['participantId_' . $index]) {
		continue;
	    }
	    $deleted = $fieldList['participantDelete_' . $index];
	    $participantId = $fieldList['participantId_' . $index];
	    $agents_type = $fieldList['agent_type_' . $index];
	    $agents_id = $fieldList['agents_id_' . $index];
	    $agent_name = $fieldList['agents_id_' . $index . '_display'];
	    $vendors_id = $fieldList['vendors_id_' . $index];
	    $vendor_name = $fieldList['vendors_id_' . $index . '_display'];
	    $respon_percentage = $fieldList['respon_percentage_' . $index];
	    $respon_amount = $fieldList['respon_amount_' . $index];
	    if ($deleted == 'deleted') {
		$sql = "DELETE FROM `vtiger_claims_sprgrid` WHERE sprid=? AND rel_crmid=?";
		$db->pquery($sql, [$participantId, $recordId]);
		continue;
	    }
	    if ($participantId == 'none') {
		//insert
		$sql = "INSERT INTO `vtiger_claims_sprgrid` (rel_crmid, agents_id, agent_name, vendors_id, vendors_name, respon_percentage, respon_amount, agent_type)
				    VALUES (?,?,?,?,?,?,?,?)";
		$db->pquery($sql, [$recordId, $agents_id, $agent_name, $vendors_id, $vendor_name, $respon_percentage, $respon_amount, $agents_type]);
	    } else {
		//update
		$sql = "UPDATE `vtiger_claims_sprgrid` SET agents_id=?, agent_name=?, vendors_id=?, vendors_name=?, respon_percentage=?, respon_amount=?, agent_type=?
				     WHERE sprid=? AND rel_crmid=?";
		$db->pquery($sql, [$agents_id, $agent_name, $vendors_id, $vendor_name, $respon_percentage, $respon_amount, $agents_type, $participantId, $recordId]);
	    }
	}
    }

    public function saveSettlementAmount($request, $claimItemsID)
    {
	$db = PearDatabase::getInstance();
	$qty = $request->get("numSettlementAmount");

	$db->pquery("DELETE FROM vtiger_claimitems_settlementamount WHERE claimitemsid = ?", array($claimItemsID));

	for ($i = 1; $i <= $qty; $i++) {
	    if (isset($_REQUEST["paymentType" . $i])) {
		$paymentType = $request->get("paymentType" . $i);
		$amount = $request->get("amount" . $i);
		$amountDenied = $request->get("amountDenied" . $i);
		$itemOmitted = ($request->get("itemOmitted" . $i) == "") ? "no" : "yes";
		$db->pquery("INSERT INTO vtiger_claimitems_settlementamount(claimitemsid, payment_type, amount, amount_denied, item_omitted) VALUES (?,?,?,?,?)", array($claimItemsID, $paymentType, $amount, $amountDenied, $itemOmitted));
	    }
	}
    }

    public function saveOriginalConditions($request, $claimItemsID)
    {
	$db = PearDatabase::getInstance();
	$qty = $request->get("numOriginalConditions");

	$db->pquery("DELETE FROM vtiger_claimitems_originalconditions WHERE claimitemsid = ?", array($claimItemsID));

	for ($i = 1; $i <= $qty; $i++) {
	    if (isset($_REQUEST["inventoryNumber" . $i])) {
		$inventoryNumber = $request->get("inventoryNumber" . $i);
		$tagColor = $request->get("tagColor" . $i);
		$originalCondition = $request->get("originalCondition" . $i);
		$exceptions = $request->get("exceptions" . $i);
		$dateTaken = $request->get("dateTaken" . $i);
		$db->pquery("INSERT INTO vtiger_claimitems_originalconditions(claimitemsid, inventory_number, tag_color, original_conditions, exceptions, date_taken) VALUES (?,?,?,?,?,?)", array($claimItemsID, $inventoryNumber, $tagColor, $originalCondition, $exceptions, $dateTaken));
	    }
	}
    }

    
    public function saveDailyExpenses(Vtiger_Request $request, $recordId)
    {
		$db = PearDatabase::getInstance();
		for ($index = 0; $index <= $request->get('numDailyExpenses'); $index++) {
			if (!$request->get('dailyExpenseId_' . $index)) {
				continue;
			}
			$deleted = $request->get('dailyExpenseDelete_' . $index);
			$dailyExpenseId = $request->get('dailyExpenseId_' . $index);
			$expenseDate = DateTimeField::convertToDBFormat($request->get('expenseDate' . $index));
			$nAdults = $request->get('nAdults' . $index);
			$nChildren = $request->get('nChildren' . $index);
			$dailyRate = $request->get('dailyRate' . $index);
			$nMeals = $request->get('nMeals' . $index);
			$tCostMeals = $request->get('tCostMeals' . $index);
			$dailyTotal = $request->get('dailyTotal' . $index);

			if ($deleted == 'deleted') {
				$sql = "DELETE FROM `vtiger_claims_daily_expense` WHERE dailyexpenseid=? AND rel_crmid=?";
				$db->pquery($sql, [$dailyExpenseId, $recordId]);
				continue;
			}
			if ($dailyExpenseId == 'none') {
				//insert
				$sql = "INSERT INTO `vtiger_claims_daily_expense` (rel_crmid, expense_date, no_adults, no_children, daily_rate, no_meals, total_cost_meals, daily_total)
							VALUES (?,?,?,?,?,?,?,?)";
				$db->pquery($sql, [$recordId, $expenseDate, $nAdults, $nChildren, $dailyRate, $nMeals, $tCostMeals, $dailyTotal]);
			} else {
				//update
				$sql = "UPDATE `vtiger_claims_daily_expense` SET expense_date=?, no_adults=?, no_children=?, daily_rate=?, no_meals=?, total_cost_meals=?, daily_total=?
							 WHERE dailyexpenseid=? AND rel_crmid=?";
				$db->pquery($sql, [$expenseDate, $nAdults, $nChildren, $dailyRate, $nMeals, $tCostMeals, $dailyTotal, $dailyExpenseId, $recordId]);
			}
		}
    }
}
