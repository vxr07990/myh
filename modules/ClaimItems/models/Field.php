<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClaimItems_Field_Model extends Vtiger_Field_Model
{
    public function getPicklistValues()
    {
        $fieldDataType = $this->getFieldDataType();
        if ($this->getName() == 'hdnTaxType') {
            return null;
        }
        if ($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist') {
            //$currentUser = Users_Record_Model::getCurrentUserModel();
        if ($this->getName() == 'shared_assigned_to') {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $agents = $currentUser->getAccessibleAgentsForUser();
            $picklistValues = [];
            foreach ($agents as $key => $val) {
                $picklistValues[] = $val;
            }
        } elseif ($this->getName() == 'claimitemsdetails_losscode') {
            if ($_REQUEST[relationOperation] == 'true') {
                $claimId = $_REQUEST[sourceRecord];
                $claimType = ClaimItems_Module_Model::getClaimType($claimId);
            } else {
                $claimItemId = $_REQUEST['record'];
                $claimType = ClaimItems_Module_Model::getClaimTypeFromClaimItem($claimItemId);
            }
            $picklistValues = ClaimItems_Module_Model::getLossCodePicklistValues($claimType);
        } elseif ($this->getName() == 'claimitemsdetails_existingfloortype') {
            if ($_REQUEST[relationOperation] == 'true') {
                $claimId = $_REQUEST[sourceRecord];
                $claimType = ClaimItems_Module_Model::getClaimType($claimId);
            } else {
                $claimItemId = $_REQUEST['record'];
                $claimType = ClaimItems_Module_Model::getClaimTypeFromClaimItem($claimItemId);
            }
            $picklistValues = ClaimItems_Module_Model::getExistingFloorTypePicklistValues($claimType);
        } elseif ($this->getName() == 'claimitemsdetails_finalfloortype') {
            if ($_REQUEST[relationOperation] == 'true') {
                $claimId = $_REQUEST[sourceRecord];
                $claimType = ClaimItems_Module_Model::getClaimType($claimId);
            } else {
                $claimItemId = $_REQUEST['record'];
                $claimType = ClaimItems_Module_Model::getClaimTypeFromClaimItem($claimItemId);
            }
            $picklistValues = ClaimItems_Module_Model::getExistingFloorTypePicklistValues($claimType);
        } elseif ($this->getName() == 'claimitemsdetails_item') {
            if ($_REQUEST[relationOperation] == 'true') {
                $claimId = $_REQUEST[sourceRecord];
                $claimType = ClaimItems_Module_Model::getClaimType($claimId);
            } else {
                $claimItemId = $_REQUEST['record'];
                $claimType = ClaimItems_Module_Model::getClaimTypeFromClaimItem($claimItemId);
            }
            $picklistValues = ClaimItems_Module_Model::getClaimItemPicklistValues($claimType);
        } elseif ($this->isRoleBased()) {
            $userModel = Users_Record_Model::getCurrentUserModel();
            $picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($this->getName(), $userModel->get('roleid'));
        } else {
            $picklistValues = Vtiger_Util_Helper::getPickListValues($this->getName());
        }
            foreach ($picklistValues as $value) {
                $fieldPickListValues[$value] = vtranslate($value, $this->getModuleName());
            }
            return $fieldPickListValues;
        }

        return null;
    }
    
        /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed()
    {
        if($this->name == 'item_status'){
            return false;
        }
        return true;
    }
}
