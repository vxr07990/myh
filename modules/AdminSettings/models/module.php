<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AdminSettings_Module_Model extends Vtiger_Module_Model
{
    public function getdefaulturl()
    {
        return 'index.php?module='.$this->get('name').'&view=index';
    }

    public function getAgentSettigs()
    {
        if (!$db) {
            $db = PearDatabase::getInstance();
        }
        
        $availableAgents = Users_Record_Model::getCurrentUserGroups();
        
        $sql = 'SELECT agentmanagerid, agency_name, valuation_discount, storage_discount, max_share_variance, packing_fee, disable_dispatch, apply_packing_discount, allow_irr_discount, allow_ferry_discount, allow_labor_surcharge_discount
	 FROM `vtiger_agentmanager` LEFT JOIN `vtiger_agencysettings` USING(agentmanagerid)';
        $result = $db->pquery($sql, array());
        $agentInfo;
        while ($row = $result->fetchRow()) {
            //Check if user is allowed to modify agent settings
            //Currently, agents and groups are associated by name
            //Check if agent name is in the array
            //file_put_contents("logs.devLog.log", "\n PARENT ROLES: ".print_r($row['agentmanagerid'], true), FILE_APPEND);
            if (in_array($row['agency_name'], $availableAgents)) {
                $agentInfo[$row['agentmanagerid']] = $row;
            }
        }
        
        return $agentInfo;
    }
    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }
}
