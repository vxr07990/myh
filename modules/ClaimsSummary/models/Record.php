<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClaimsSummary_Record_Model extends Vtiger_Record_Model {

    public function getAssociatedClaims() {
	$claimTypeList = [];

	$arr = $this->getRelatedClaims();
	foreach ($arr as $val) {
	    $claimEntity = Vtiger_Record_Model::getInstanceById($val, "Claims");
	    $data = $claimEntity->getData();
	    $claimTypeList[] = array("claim_id" => $val, "claim_type" => $data[claim_type], "received_date" => $data[claims_date_received], "closed_date" => $data[claims_date_closed], "calendar_days" => $data[claims_calendar_days_settle], "business_days" => $data[claims_business_days_settle]);
	}
	return $claimTypeList;
    }
    
    public function getRelatedClaims() {
	$db = PearDatabase::getInstance();
	$array = [];
	$result = $db->pquery("SELECT relcrmid FROM vtiger_crmentityrel WHERE module = 'ClaimsSummary' AND crmid = ? AND relmodule = 'Claims'", array($this->getId()));
	if ($db->num_rows($result) > 0) {
	    while ($arr = $db->fetch_array($result)) {
		$array[] = $arr[relcrmid];
	    }
	}
	return $array;
    }

    public function getClaimTypeSummaryGrids($claimTypes) {
	$claimTypeSummaryGrid = [];
	foreach ($claimTypes as $claimType) {
	    $summaryGrids[] = Claims_Module_Model::getSummaryTable($claimType[claim_id]);
	}

	$claimTypeSummaryGrid = array_shift($summaryGrids);
	//key => concatenation of agent id and service provider id
	foreach ($summaryGrids as $grid) {
	    foreach ($grid as $class => $infoClass) { //$class = Damages/Loss, $infoClass = array info about that class group by agent/serviceprovider
		if (count($infoClass) > 0) {
		    foreach ($infoClass as $key => $value) {
			if (array_key_exists($key, $claimTypeSummaryGrid[$class])) {
			    $claimTypeSummaryGrid[$class][$key][Amount] = floatval($claimTypeSummaryGrid[$class][$key][Amount]) + floatval($value[Amount]);
			    $claimTypeSummaryGrid[$class][$key][Qty] = intval($claimTypeSummaryGrid[$class][$key][Qty]) + 1;
			} else {
			    $claimTypeSummaryGrid[$class][$key] = $value;
			}
		    }
		}
	    }
	}
	return $claimTypeSummaryGrid;
    }

    

}
