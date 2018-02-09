<?php

include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Revise.php';

class Claims_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		if ($request->get("record")) {
			$row = $db->pquery("SELECT claims_status_statusgrid,claims_reason_statusgrid FROM vtiger_claims WHERE claimsid = ?", array($request->get("record")))->fetchRow();

			$oldStatus = $row["claims_status_statusgrid"];
			$oldReason = $row["claims_reason_statusgrid"];
			$mode = "Edit";
		} else {
			$oldStatus = $oldReason = "";
			$mode = "Create";
		}
		parent::process($request);

		//service provider responsability table save
		$claimId = $request->get("record");

		if ($mode == "Edit") {
			$this->saveSummaryGridInfo($request, $claimId);
		}

		$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
		if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
			$this->saveSPR($_REQUEST, $claimId);
		}
		$this->savePayments($request, $claimId);

		$statusgrid = $request->get("claims_status_statusgrid");
		$reason = $request->get("claims_reason_statusgrid");

		$effectiveDate = $request->get("claims_effective_date_statusgrid");
		$date = DateTimeField::convertToDBFormat($effectiveDate);

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$claim = [
			'id' => vtws_getWebserviceEntityId('Claims', $claimId),
			'assigned_user_id' => vtws_getWebserviceEntityId('Users', $currentUser->id),
		];

		if ($statusgrid == "Active" && $reason == "Formal Claim Received") {
			$claim['claims_date_received'] = $date;
		} else if ($statusgrid == "Closed" && $reason == "Claim Investigation Concluded") {
			$claim['claims_date_closed'] = $date;
		}

		if ((($mode == "Create") && ($statusgrid != "" && $reason != "" && $date != "")) || (($mode == "Edit") && ($oldStatus != $statusgrid || $reason != $oldReason))) {
			$this->saveStatusChange($statusgrid, $reason, $date, $request->get("record"));
		}

		vtws_revise($claim, $currentUser);
	}

	function saveSummaryGridInfo($request, $claimsID) {
		$db = PearDatabase::getInstance();
		$summaryTableRows = $request->get("summaryTableRows");

		for ($i = 1; $i < $summaryTableRows; $i++) {
			if (isset($_REQUEST["agentChargeBack" . $i])) {
				$claimsID = $request->get('record');
				$agentType = $request->get("summary-agent-type-" . $i);
				$agentId = $request->get("summary-agent-id-" . $i);
				$serviceProviderId = $request->get("summary-serviceprovider-id-" . $i);
				$agentChargeBack = $request->get("agentChargeBack" . $i);
				$serviceProviderChargeBack = $request->get("serviceProviderChargeBack" . $i);
				$effectiveDate = DateTimeField::convertToDBFormat($request->get("effectiveDate" . $i));
				$distribution = ($request->get("distribution" . $i) == "") ? "no" : "yes";
				$distributionDate = DateTimeField::convertToDBFormat($request->get("distributionDate" . $i));
				$claimClass = $request->get("summary-claim-class-" . $i);
				
				$result = $db->pquery('SELECT id FROM vtiger_claims_summarygrid WHERE claims_id=? AND agent_type=? AND agent_id=? AND serviceprovider_id=? AND claim_class=?', [$claimsID, $agentType, $agentId, $serviceProviderId, $claimClass]);
				if($result && $db->num_rows($result)){
				    $dbId = $db->query_result($result, 0, 'id');
				    $db->pquery("UPDATE vtiger_claims_summarygrid SET agent_chargeback=?, service_providerchargeBack=?, effective_date=?, distribution=?, distribution_date=? WHERE id=?", array($agentChargeBack, $serviceProviderChargeBack, $effectiveDate, $distribution, $distributionDate, $dbId));

				} else {
				    $db->pquery("INSERT INTO vtiger_claims_summarygrid(claims_id, agent_type, agent_id, serviceprovider_id, claim_class, agent_chargeback, service_providerchargeBack, effective_date, distribution, distribution_date) VALUES (?,?,?,?,?,?,?,?,?,?)", array($claimsID, $agentType, $agentId, $serviceProviderId,  $claimClass, $agentChargeBack, $serviceProviderChargeBack, $effectiveDate, $distribution, $distributionDate));

				}
				
			}
		}
	}

	function saveStatusChange($status, $reason, $effectiveDate, $claimsID) {
		$db = PearDatabase::getInstance();
		$db->pquery("INSERT INTO vtiger_claims_status_change (claimsID, status, reason, effective_date) VALUES (?,?,?,?)", array($claimsID, $status, $reason, $effectiveDate));
	}

	function savePayments($request, $claimsID) {
		$db = PearDatabase::getInstance();
		$qty = $request->get("numPayments");

		$db->pquery("DELETE FROM vtiger_claims_payments WHERE claimsId = ?", array($claimsID));

		for ($i = 1; $i <= $qty; $i++) {
			if (isset($_REQUEST["paymentFees" . $i])) {
				$paymentFees = $request->get("paymentFees" . $i);
				$feesDate = $request->get("feesDate" . $i);
				$feesAmount = $request->get("feesAmount" . $i);
				$db->pquery("INSERT INTO vtiger_claims_payments(claimsId, paymentFees, feesDate, feesAmount) VALUES (?,?,?,?)", array($claimsID, $paymentFees, $feesDate, $feesAmount));
			}
		}
	}

	function saveSPR($fieldList, $recordId) {
		$db = PearDatabase::getInstance();
		for ($index = 0; $index <= $fieldList['numSPR']; $index++) {
			if (!$fieldList['participantId_' . $index]) {
				continue;
			}
			$deleted = $fieldList['participantDelete_' . $index];
			$participantId = $fieldList['participantId_' . $index];
			$agents_id = $fieldList['agents_id_' . $index];
			$agents_type = $fieldList['agent_type_' . $index];
			$agent_name = $fieldList['agents_id_' . $index . '_display'];
			$vendors_id = $fieldList['vendors_id_' . $index];
			$vendors_name = $fieldList['vendors_id_' . $index . '_display'];
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
				$db->pquery($sql, [$recordId, $agents_id, $agent_name, $vendors_id, $vendors_name, $respon_percentage, $respon_amount, $agents_type, $agents_type]);
			} else {
				//update
				$sql = "UPDATE `vtiger_claims_sprgrid` SET agents_id=?, agent_name=?, vendors_id=?, vendors_name=?, respon_percentage=?, respon_amount=?, agent_type=?
				     WHERE sprid=? AND rel_crmid=?";
				$db->pquery($sql, [$agents_id, $agent_name, $vendors_id, $vendors_name, $respon_percentage, $respon_amount, $agents_type, $participantId, $recordId]);
			}
		}
	}

}

?>