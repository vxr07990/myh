<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AdminSettings_SaveSettings_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $agentSettings = $request->get('agencySettings');
        $responseMsg = '';
        
        if (!$db) {
            $db = PearDatabase::getInstance();
        }
        
        $availableAgents = Users_Record_Model::getCurrentUserGroups();
        $updateAgent;
        
        $sql = 'SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid = ?';
        $result = $db->pquery($sql, [$agentSettings['agentmanagerid']]);
        
        if ($db->num_rows($result) > 0) {
            //Make sure agency exists
            $updateAgent = $result->fetchRow()[0];
        
            //Check if user is allowed to modify agent settings
            //Currently, agents and groups are associated by name
            //Check if agent name is in the array
            if (!in_array($updateAgent, $availableAgents)) {
                $responseMsg = vtranslate('LBL_FAILURE_MESSAGE_PERM', 'AdminSettings');
            } else {
                $sql = 'UPDATE `vtiger_agencysettings` SET valuation_discount = ?,
															storage_discount = ?,
															max_share_variance = ?,
															packing_fee = ?,
															disable_dispatch = ?,
															apply_packing_discount = ?,
															allow_irr_discount = ?,
															allow_ferry_discount = ?,
															allow_labor_surcharge_discount = ?
														WHERE agentmanagerid = ?';
                $result = $db->pquery($sql, array(
                                                $agentSettings['valuation_discount'],
                                                $agentSettings['storage_discount'],
                                                $agentSettings['max_share_variance'],
                                                $agentSettings['packing_fee'],
                                                $agentSettings['disable_dispatch'],
                                                $agentSettings['apply_packing_discount'],
                                                $agentSettings['allow_irr_discount'],
                                                $agentSettings['allow_ferry_discount'],
                                                $agentSettings['allow_labor_surcharge_discount'],
                                                $agentSettings['agentmanagerid']
                                            ));
                $responseMsg = vtranslate('LBL_SUCCESS_MESSAGE', 'AdminSettings');
            }
        } else {
            $responseMsg = vtranslate('LBL_FAILURE_MESSAGE_EXISTS', 'AdminSettings');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($responseMsg);
        $response->emit();
    }
}
