<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class Claims_Module_Model extends Vtiger_Module_Model {

	public function isSummaryViewSupported() {
		return false;
	}

    public function getSummaryTable($claimsID) {
	$db = PearDatabase::getInstance();
	$summaryTable = [];
	$damagesArr = $lossArr = [];

	$request = new Vtiger_Request($_REQUEST);
	$request->set('record', $claimsID);
	$claimItems = Claims_Module_Model::getClaimsItemsArr($request);
	$damage = Array("Cosmetic", "Structural", "Water Damage", "Inconvenience", "Mechanical/Electrical");

	foreach ($claimItems as $claimItem) {
	    $lossCode = $claimItem['claimitemsdetails_losscode'];
	    if ($lossCode !== "") {
		$result = $db->pquery("SELECT * FROM vtiger_claims_sprgrid WHERE rel_crmid = ?  ORDER BY agent_name", array($claimItem['claimitemsid']));
		$numRows = $db->num_rows($result);
		if ($numRows > 0) {//respon_amount
		    $newAmount = $itemQty = 0;
		    while ($arr = $db->fetch_array($result)) {
			$claimClass = (in_array($lossCode, $damage)) ? "Damages" : "Loss";
			$agentType = $arr['agent_type'];
			$key = $agentType . '_' . $arr['agents_id'] . '_' . $arr['vendors_id'] . '_' . $claimClass;

			$auxResult = $db->pquery("SELECT * FROM vtiger_claims_summarygrid WHERE claims_id = ? AND agent_type = ? AND agent_id=? AND serviceprovider_id=? AND claim_class = ?", array($claimsID, $agentType, $arr['agents_id'], $arr['vendors_id'], $claimClass));
			if (!$auxResult) {
			    $agentChargeBack = $serviceProviderChargeBack = $effectiveDate = $distribution = $distributionDate = $dbId = "";
			} else {
			    $row = $auxResult->FetchRow();
			    $agentChargeBack = $row['agent_chargeback'];
			    $dbId = $row['id'];
			    $serviceProviderChargeBack = $row['service_providerchargeBack'];
			    $distribution = $row['distribution'];
			    $distributionDate = $row['distribution_date'];
			    if ($row['effective_date'] != '' && $row['effective_date'] != '0000-00-00') {
				$effectiveDate = DateTimeField::convertToUserFormat($row['effective_date']);
			    } else {
				$effectiveDate = '';
			    }
			    if ($row['distribution_date'] != '' && $row['distribution_date'] != '0000-00-00') {
				$distributionDate = DateTimeField::convertToUserFormat($row['distribution_date']);
			    } else {
				$distributionDate = '';
			    }
			}

			if (!array_key_exists($key, $summaryTable)) {
			    $summaryTable[$key] = array(
				"dbId" => $dbId,
				"claimTypeID" => $claimsID,
				"AgentType" => $agentType,
				"AgentID" => $arr['agents_id'],
				"Agent" => $arr['agent_name'],
				"ServiceProviderID" => $arr['vendors_id'],
				"ServiceProvider" => $arr['vendors_name'],
				"Amount" => $arr['respon_amount'],
				"Qty" => "1",
				"agentChargeBack" => $agentChargeBack,
				"serviceProviderChargeBack" => $serviceProviderChargeBack,
				"effectiveDate" => $effectiveDate,
				"distribution" => $distribution,
				"distributionDate" => $distributionDate,
				"claimClass" => $claimClass,
			    );
			} else {
			    $newAmount = floatval($summaryTable[$key]['Amount']) + floatval($arr['respon_amount']);
			    $itemQty = intval($summaryTable[$key]['Qty']) + 1;
			    $summaryTable[$key]['Amount'] = $newAmount;
			    $summaryTable[$key]['Qty'] = $itemQty;
			}
		    }
		}
	    } else {
		continue;
	    }
	}

	$summaryTable = Claims_Module_Model::addClaimsResponsability($summaryTable, $claimsID);

	return $summaryTable;
    }

    //add the amounts from the current claim item service provider responsability block
    public function addClaimsResponsability($summaryTable, $claimsID) {
	$ownSPRItems = Claims_Module_Model::getGridItems("spr", $claimsID);
	$claimClass = ''; //We dont have this field in the claim types :/ need to check with kim what to do about it
		
	foreach ($ownSPRItems as $sprItem) {
	    $claimTypeRecordModel = Vtiger_Record_Model::getInstanceById($sprItem['rel_crmid'], 'Claims');
	    
	    $claimClass = $claimTypeRecordModel->get('claim_type');
	    if($claimClass == 'Cargo'){
		$claimClass = 'Damages';
	    }
	    
		    
	    $key = $sprItem['agent_type'] . '_' . $sprItem['agents_id'] . '_' . $sprItem['vendors_id'] . '_' . $claimClass;
	    if (!array_key_exists($key, $summaryTable)) {
		$summaryTable[$key] = array(
		    "dbId" => $dbId,
		    "AgentType" => $sprItem['agent_type'],
		    "AgentID" => $sprItem['agents_id'],
		    "Agent" => $sprItem['agent_name'],
		    "ServiceProviderID" => $sprItem['vendors_id'],
		    "ServiceProvider" => $sprItem['vendors_name'],
		    "Amount" => floatval($sprItem['respon_amount']),
		    "Qty" => "1",
		    "agentChargeBack" => 0,
		    "serviceProviderChargeBack" => 0,
		    "effectiveDate" => '',
		    "distribution" => '',
		    "distributionDate" => '',
		    "claimClass" => $claimClass,
		);
	    } else {
		$newAmount = floatval($summaryTable[$key]['Amount']) + floatval($sprItem['respon_amount']);
		$itemQty = intval($summaryTable[$key]['Qty']) + 1;
		$summaryTable[$key]['Amount'] = $newAmount;
		$summaryTable[$key]['Qty'] = $itemQty;
	    }
	}

	return $summaryTable;
    }

    public function getPaymentList($claimsID) {
		$db = PearDatabase::getInstance();
		$paymentList = [];

		$result = $db->pquery("SELECT * FROM vtiger_claims_payments WHERE claimsId = ?", array($claimsID));
		if($db->num_rows($result) > 0){
			while ($arr = $db->fetch_array($result)) {
				$paymentList[] = array("paymentId" => $arr["paymentid"], "fees" => $arr["paymentfees"], "date" => $arr["feesdate"], "amount" => $arr["feesamount"]);
			}
		}
		return $paymentList;
	}

	public function getStatusChangeList($recordId, $actualStatus = "", $actualReason = "") {
		$statusChangeList = [];
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT * FROM vtiger_claims_status_change WHERE claimsID = ?", array($recordId));
		if ($db->num_rows($result) > 0) {
			while ($arr = $db->fetch_array($result)) {
				$statusChangeList[] = array("status" => $arr[status], "reason" => $arr[reason], "effective_date" => $arr[effective_date]);
			}
		}
		return $statusChangeList;
	}

	public function getClaimsItemsArrHeader() {
		$claimsItemsArrayHeader = ['LBL_CLAIMITEMS_ITEMDESCRIPTION', 'LBL_CLAIMITEMS_ITEMSTATUS', 'LBL_CLAIMITEMS_LOSSCODE', 'LBL_CLAIMITEMS_ORIGINALCOST',
			'LBL_CLAIMITEMS_REPLACEMENTCOST', 'LBL_CLAIMITEMS_CLAIMANTREQUEST', 'LBL_CLAIMITEMS_AMOUNT', 'Item Omitted'];

		return $claimsItemsArrayHeader;
	}

	function isSummaryField($field) {
		$db = PearDatabase::getInstance();
		return $db->pquery("SELECT summaryfield FROM vtiger_field WHERE columnname = ?", array($field))->fetchRow()[0];
	}

	public function getClaimsItemsArr(Vtiger_Request $request) {
		$claimsItemsArray = [];
		$db = PearDatabase::getInstance();
		$recordId = $request->get('record');

		$stmt = "SELECT vtiger_claimitems.claimitemsid,vtiger_claimitems.item_description, vtiger_claimitems.item_status, vtiger_claimitems.claimitemsdetails_losscode,
						vtiger_claimitems.claimitemsdetails_originalcost, vtiger_claimitems.claimitemsdetails_replacementcost,
						vtiger_claimitems.claimitemsdetails_claimantrequest, vtiger_claimitems.claimitemsdetails_amount,
						vtiger_claimitems_settlementamount.item_omitted
					FROM vtiger_claimitems
                    INNER JOIN vtiger_crmentity ON vtiger_claimitems.claimitemsid = vtiger_crmentity.crmid
                    INNER JOIN vtiger_claims ON vtiger_claimitems.linked_claim = vtiger_claims.claimsid
					LEFT JOIN vtiger_claimitems_settlementamount ON vtiger_claimitems.claimitemsid=vtiger_claimitems_settlementamount.claimitemsid
                    WHERE deleted = 0 AND vtiger_claims.claimsid = ?";
		$result = $db->pquery($stmt, array($recordId));
		if ($db->num_rows($result) > 0) {
			while ($arr = $db->fetchByAssoc($result)) {
				foreach ($arr as $key => $value) {
//                    if(Claims_Module_Model::isSummaryField($key)){ //$this was pointing to Claims_Detail object or something like that :(
					$aux[$key] = $value;
//                    }
				}
//		$aux[claimitemsdetails_exceptions] = implode(', ', explode(' |##| ', $aux[claimitemsdetails_exceptions]));
				$aux["claimitemsid"] = $arr[claimitemsid];
				$claimsItemsArray[] = $aux;
			}
		}
		return $claimsItemsArray;
	}

	public function getGridItems($gridType, $recordId) {
		if ($gridType && $gridType != '' && $recordId) {
			$table = '';
			$orderBy = '';
			switch ($gridType) {
				case 'status':
					$table = 'vtiger_claims_statusgrid';
					$orderBy = ' ORDER BY effective_date ASC';
					break;
				case 'payments' :
					$table = 'vtiger_claims_paymentsgrid';
					break;
				case 'spr' :
					$table = 'vtiger_claims_sprgrid';
					break;
				case 'daily_expenses' :
					$table = 'vtiger_claims_daily_expense';
					break;
				default:
					break;
			}

			$items = [];
			$db = PearDatabase::getInstance();
			$sql = 'SELECT * FROM ' . $table . ' WHERE rel_crmid=' . $recordId . ' ' . $orderBy;
			$result = $db->query($sql);
			if ($result && $db->num_rows($result) > 0) {
				while ($row = $db->fetchByAssoc($result)) {
					if ($gridType == 'spr' && $row['vendors_id'] > 0) {
						$row['vendors_name'] = getEntityName('Vendors', array($row['vendors_id']));
						$row['vendors_name'] = $row['vendors_name'][$row['vendors_id']];
						//OT17265 - claims - show icode
						$row['icode'] = Vtiger_Record_Model::getInstanceById($row['vendors_id'])->getDisplayValue('icode');
					}
					$items[] = $row;
				}
			}
			return $items;
		} else {
			return [];
		}
	}

	public function getSideBarLinks($linkParams) {
		$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
		$links = parent::getSideBarLinks($linkParams);
		$parentQuickLinks = parent::getSideBarLinks($linkParams);


		$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues(
						array(
							'linktype' => 'SIDEBARLINK',
							'linklabel' => 'LBL_CLAIMS_MANAGER',
							'linkurl' => 'index.php?module=ClaimsSummary&view=List',
							'linkicon' => '',
						)
		);


		return $parentQuickLinks;
	}
    function getOrderParticipantPicklistValues($claimSummaryid) {

		$claimsSummaryModule = Vtiger_Record_Model::getInstanceById($claimSummaryid, 'ClaimsSummary');

		$orderId = $claimsSummaryModule->get('claimssummary_orderid');
		$agents = ParticipatingAgents_Module_Model::getParticipants($orderId);
		$picklistValues = [];
		if (is_array($agents)) {
			foreach ($agents as $agent) {
			$picklistValues[$agent['agent_type']] = $agent['agent_type'];
			}
		}

		return $picklistValues;
    }
}