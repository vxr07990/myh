<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClaimItems_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function to check whether the module is summary view supported
     * @return <Boolean> - true/false
     */
    public function isSummaryViewSupported()
    {
	return false;
    }

    public static function getClaimItemPicklistValues($claimType)
    {
		$db = PearDatabase::getInstance();
		$result = $db->query('SELECT claimitemsdetails_item FROM `vtiger_claimitemsdetails_item` ORDER BY claimitemsdetails_item ASC');
		$picklistValues = [];
		$filteredValues = [];
        while ($row = $result->fetchRow()) {
			$picklistValues[] = $row['claimitemsdetails_item'];
		}
		switch ($claimType) {

			case "Automobile":
                $arr = array('Automobile');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;

			default:
			    $filteredValues = $picklistValues;
			break;
		}
	
	return $filteredValues;
	}
    
    public static function getLossCodePicklistValues($claimType)
    {
		$db = PearDatabase::getInstance();
		$result = $db->query('SELECT claimitemsdetails_losscode FROM `vtiger_claimitemsdetails_losscode` ORDER BY claimitemsdetails_losscode ASC');
		$picklistValues = [];
		$filteredValues = [];
        while ($row = $result->fetchRow()) {
			$picklistValues[] = $row['claimitemsdetails_losscode'];
		}
		switch ($claimType) {
			case "Cargo":
                $arr = array('Cosmetic', 'Structural', 'Loss of Components', 'Missing', 'Water Damage', 'Inconvenience', 'Mechanical/Electrical');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;
			case "Automobile":
                $arr = array('Cosmetic', 'Structural', 'Missing', 'Inconvenience', 'Mechanical/Electrical', 'Undercarriage');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;
			case "Facility/Residence":
                $arr = array('Cosmetic', 'Structural', 'Environmental', 'Water Damage');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;
			case "Property":
            $arr = array('Yard/Grounds', 'Driveway/Parking Lot', 'Gate', 'Other Physical Structure', 'Automobile');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;
			case "Service Recovery":
            $arr = array('Reassembly', 'Inconvenience', 'HHG Daily Allowance', 'HHG Expense Reimbursement',
							'HHG Furniture Rental', 'Auto Daily Allowance', 'Auto Arrangement of Rental');
				$filteredValues = array_intersect($picklistValues, $arr);
			break;
			default:
                $arr = array();
			break;
		}
	
	return $filteredValues;
	}
	
    public static function getExistingFloorTypePicklistValues($claimType)
    {
		$db = PearDatabase::getInstance();
		$result = $db->query('SELECT claimitemsdetails_existingfloortype FROM `vtiger_claimitemsdetails_existingfloortype` ORDER BY claimitemsdetails_existingfloortype ASC');
		$picklistValues = [];
        while ($row = $result->fetchRow()) {
			$picklistValues[] = $row['claimitemsdetails_existingfloortype'];
		}
		switch ($claimType) {
			case "Cargo":
				//add same logic as "facility/residence" case if need to filter values for this case, if not shows all values
			break;
			case "Automobile":
				//add same logic as "facility/residence" case if need to filter values for this case, if not shows all values
			break;
			case "Facility/Residence":
                $arr = array('Carpet', 'Walls', 'Floors', 'Trim', 'Stairway/Railings', 'Doors', 'Ceiling', 'Elevator', 'Lobby', 'Dock Area');
				$picklistValues = array_intersect($picklistValues, $arr);
			break;
			case "Property":
                $arr = array('Driveway/Yard');
				$picklistValues = array_intersect($picklistValues, $arr);
			break;
			case "Service Recovery":
				//add same logic as "facility/residence" case if need to filter values for this case, if not shows all values
			break;
			default:
				//shows all values
			break;
		}
	
	return $picklistValues;
	}
	
    public static function getClaimTypeFromClaimItem($claimItemId)
    {
		$adb = PearDatabase::getInstance();
		$claimType = $adb->pquery('SELECT claim_type FROM vtiger_claims INNER JOIN vtiger_claimitems ON linked_claim=claimsid WHERE claimitemsid=?', [$claimItemId])->fetchRow()[0];
		return $claimType;
	}
	
    public static function getClaimType($claimID)
    {
		$adb = PearDatabase::getInstance();
		$claimType = $adb->pquery('SELECT claim_type FROM vtiger_claims WHERE claimsid=?', [$claimID])->fetchRow()[0];
		return $claimType;
	}
	
    public static function getClaimItemsHiddenFields($claimID)
    {
	$claimType = ClaimItems_Module_Model::getClaimType($claimID);
	switch ($claimType) {
	    case "Cargo":
        $arr = array('carrier_exception', 'shipper_exception', 'claimitemsdetails_documented',
		    'claimitemsdetails_make', 'claimitemsdetails_year', 'claimitemsdetails_model', 'claimitemsdetails_contactname',
		    'claimitemsdetails_contactphone', 'claimitemsdetails_contactcelltphone', 'claimitemsdetails_contactemail',
		    'claimitemsdetails_location', 'claimitemsdetails_dateofincident', 'claimitemsdetails_descriptiondamage',
		    'claimitemsdetails_existingfloortype', 'claimitemsdetails_existingroom', 'claimitemsdetails_existingnotes',
		    'claimitemsdetails_finalfloortype', 'claimitemsdetails_finalroom', 'claimitemsdetails_finalnotes','claimitemsdetails_facresfloortype',
			'claimitemsdetails_request_date','claimitemsdetails_request_damount','claimitemsdetails_request_days','claimitemsdetails_request_tamount',
			'claimitemsdetails_authorized_date','claimitemsdetails_authorized_damount','claimitemsdetails_authorized_days','claimitemsdetails_authorized_tamount',
		    'claimitemsdetails_facresitem', 'claimitemsdetails_proproomfinal','claimitemsdetails_proproomoriginal');
		break;
	    case "Automobile":
        $arr = array('inventory_number', 'carrier_exception', 'shipper_exception',
		    'tag_color', 'claimitemsdetails_weightofitem',
		    'claimitemsdetails_cartondamage', 'claimitemsdetails_yearpurchased', 'claimitemsdetails_originalcost',
		    'claimitemsdetails_replacementcost', 'claimitemsdetails_contactname',
		    'claimitemsdetails_contactphone', 'claimitemsdetails_contactcelltphone', 'claimitemsdetails_contactemail',
		    'claimitemsdetails_location', 'claimitemsdetails_dateofincident', 'claimitemsdetails_descriptiondamage',
		    'claimitemsdetails_existingfloortype', 'claimitemsdetails_existingroom', 'claimitemsdetails_existingnotes',
		    'claimitemsdetails_finalfloortype', 'claimitemsdetails_finalroom', 'claimitemsdetails_finalnotes','claimitemsdetails_facresfloortype',
		    'claimitemsdetails_request_date','claimitemsdetails_request_damount','claimitemsdetails_request_days','claimitemsdetails_request_tamount',
		    'claimitemsdetails_authorized_date','claimitemsdetails_authorized_damount','claimitemsdetails_authorized_days','claimitemsdetails_authorized_tamount',
		    'claimitemsdetails_facresitem', 'claimitemsdetails_proproomfinal','claimitemsdetails_proproomoriginal');
		break;
	    case "Facility/Residence":
        $arr = array('claimitemsdetails_item', 'inventory_number', 'item_description', 'carrier_exception', 'shipper_exception',
		    'tag_color', 'claimitemsdetails_weightofitem', 'claimitemsdetails_cartondamage',
		    'claimitemsdetails_yearpurchased', 'claimitemsdetails_originalcost', 'claimitemsdetails_replacementcost',
		    'claimitemsdetails_originalconditions', 'claimitemsdetails_exceptions', 'claimitemsdetails_datetaken', 'claimitemsdetails_year',
		    'claimitemsdetails_make', 'claimitemsdetails_model','claimitemsdetails_descriptiondamage',
		    'claimitemsdetails_request_date','claimitemsdetails_request_damount','claimitemsdetails_request_days','claimitemsdetails_request_tamount',
		    'claimitemsdetails_authorized_date','claimitemsdetails_authorized_damount','claimitemsdetails_authorized_days',
		    'claimitemsdetails_authorized_tamount','claimitemsdetails_proproomfinal','claimitemsdetails_proproomoriginal');
		break;
	    case "Property":
        $arr = array('inventory_number', 'carrier_exception', 'shipper_exception', 'tag_color','claimitemsdetails_facresfloortype',
		    'item_description', 'claimitemsdetails_weightofitem', 'claimitemsdetails_weightofitem',
		    'claimitemsdetails_cartondamage', 'claimitemsdetails_yearpurchased',
		    'claimitemsdetails_originalcost', 'claimitemsdetails_replacementcost','claimitemsdetails_originalconditions', 'claimitemsdetails_exceptions',
		    'claimitemsdetails_datetaken', 'claimitemsdetails_year', 'claimitemsdetails_make',
		    'claimitemsdetails_model', 'claimitemsdetails_descriptiondamage',
		    'claimitemsdetails_request_date','claimitemsdetails_request_damount','claimitemsdetails_request_days','claimitemsdetails_request_tamount',
		    'claimitemsdetails_authorized_date','claimitemsdetails_authorized_damount','claimitemsdetails_authorized_days',
		    'claimitemsdetails_authorized_tamount','claimitemsdetails_facresitem','claimitemsdetails_finalroom','claimitemsdetails_existingroom');
		break;
	    case "Service Recovery":
        $arr = array('carrier_exception', 'shipper_exception','claimitemsdetails_facresfloortype',
		    'claimitemsdetails_weightofitem', 'claimitemsdetails_cartondamage', 'claimitemsdetails_yearpurchased',
		    'claimitemsdetails_natureofclaim', 'claimitemsdetails_originalcost', 'claimitemsdetails_replacementcost',
		    'claimitemsdetails_year', 'claimitemsdetails_make', 'claimitemsdetails_model', 'claimitemsdetails_contactname',
		    'claimitemsdetails_contactphone', 'claimitemsdetails_contactcelltphone', 'claimitemsdetails_contactemail',
		    'claimitemsdetails_location', 'claimitemsdetails_dateofincident', 'claimitemsdetails_descriptiondamage',
		    'claimitemsdetails_existingfloortype', 'claimitemsdetails_existingroom', 'claimitemsdetails_existingnotes',
		    'claimitemsdetails_finalfloortype', 'claimitemsdetails_finalroom', 'claimitemsdetails_finalnotes','claimitemsdetails_facresitem',
		    'claimitemsdetails_proproomfinal','claimitemsdetails_proproomoriginal');
		break;
	    default:
        $arr = array();
		break;
	}
	
	return $arr;
    }
    
    public static function displayLikeInventoryItems($claimID)
    {
	$adb = PearDatabase::getInstance();
	
	$claimType = $adb->pquery('SELECT claim_type FROM vtiger_claims WHERE claimsid=?', [$claimID])->fetchRow()[0];
		switch ($claimType) {
	    case "Cargo":
		$show = true;
		break;
	    case "Automobile":
		$show = false;
		break;
	    case "Facility/Residence":
		$show = false;
		break;
	    case "Property":
		$show = false;
		break;
	    case "Service Recovery":
		$show = true;
		break;
	    default:
		$show = true;
		break;
	}
	
	return $show;
    }
    
    public static function getServiceRecoveryFields($recordModel)
    {
	if (strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') !== false && strpos($recordModel->get('claimitemsdetails_losscode'), 'Inconvenience') !== false) {
	    $arr2 = array('claimitemsdetails_request_date',
		'claimitemsdetails_request_damount',
		'claimitemsdetails_request_days',
		'claimitemsdetails_request_tamount',
		'claimitemsdetails_authorized_date',
		'claimitemsdetails_authorized_damount',
		'claimitemsdetails_authorized_days',
		'claimitemsdetails_authorized_tamount');
	} else {
	    $arr2 = array('inventory_number',
		'item_description',
		'tag_color',
		'claimitemsdetails_item',
		'claimitemsdetails_claimantrequest',
		'claimitemsdetails_originalconditions',
		'claimitemsdetails_exceptions',
		'claimitemsdetails_datetaken',
		'claimitemsdetails_documented');
	}

	return $arr2;
    }
}
