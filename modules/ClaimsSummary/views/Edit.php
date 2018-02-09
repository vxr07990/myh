<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ClaimsSummary_Edit_View extends Vtiger_Edit_View
{
    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, $display);
        // if this is a relation operation, pull the owner field
        $isRelationOperation = $request->get('relationOperation');
        $sourceModule = $request->get('sourceModule');
        $sourceRecord = $request->get('sourceRecord');
        if ($isRelationOperation && $sourceModule && $sourceRecord) {
            $src = Vtiger_Record_Model::getInstanceById($sourceRecord);
            if ($src) {
                $owner = $src->get('agentid');
                $request->set('agentid', $owner);
                if ($sourceModule == 'Orders') {
		    $orderNo = $src->get('orders_no');
		    
		    if(strpos($orderNo, 'O/F') !== false){
			throw new AppException(vtranslate('LBL_CLAIM_FROM_OVERFLOW', $request->get('module')));
        
		    }
		    
		    
                    $request->set('claimssummary_valuationtype', $src->get('valuation_deductible'));
                    $request->set('claimssummary_declaredvalue', $src->get('total_valuation'));
                    $request->set('claimssummary_contactid', $src->get('orders_contacts'));
                    $request->set('claimssummary_accountid', $src->get('orders_account'));
                    $request->set('business_line', $src->get('business_line'));
                }
            }
        }
    }
}
