<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ZoneAdmin_Module_Model extends Vtiger_Module_Model
{
    public function getAddressZone($state, $zipCode)
    {
        $db = PearDatabase::getInstance();
        $zone = '';
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = $currentUser->getAccessibleOwnersForUser('', true, true);
        unset($accesibleAgents['agents']);
        unset($accesibleAgents['vanlines']);
        $accesibleAgents = array_keys($accesibleAgents);

        if($zipCode != ''){
            $result = $db->pquery("SELECT zoneadminid FROM vtiger_zoneadmin 
            INNER JOIN vtiger_crmentity ON vtiger_zoneadmin.zoneadminid = vtiger_crmentity.crmid
            WHERE deleted=0 AND  zip_code like '%" . $db->sql_escape_string($zipCode) .  "%' AND agentid IN ( ". generateQuestionMarks($accesibleAgents)." )", [$accesibleAgents]);
        }

        if ($zipCode != '' && $db->num_rows($result) == 0 && $state !='') {
            $result = $db->pquery("SELECT zoneadminid FROM vtiger_zoneadmin 
            INNER JOIN vtiger_crmentity ON vtiger_zoneadmin.zoneadminid = vtiger_crmentity.crmid
            WHERE deleted=0 AND za_state like '%" . $db->sql_escape_string($state) .  "%' AND agentid IN ( ". generateQuestionMarks($accesibleAgents)." )", [$accesibleAgents]);
        }

        if ($db->num_rows($result) > 0) {
            $zone = $db->query_result($result, 0, 'zoneadminid');
        }

        return $zone;
    }
    
    public function isSummaryViewSupported()
    {
        return false;
    }

    public static function getZoneAdminDisplayValue($zoneAdminID){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT za_zone FROM vtiger_zoneadmin WHERE zoneadminid = ?",array($zoneAdminID));
		
		if($db->num_rows($result)){
			$returnVar = $db->query_result($result, 0, "za_zone");
		}else{
			$returnVar = "";
		}
		return $returnVar;
	}
}
