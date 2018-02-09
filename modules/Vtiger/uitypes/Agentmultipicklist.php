<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_Agentmultipicklist_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/AgentMultipicklist.tpl';
    }
    
    public function getAgentDisplay($agents)
    {
        $agentDisplays = [];
        foreach ($agents as $agentId => $agentName) {
            //$agentRecordModel = Vtiger_Record_Model::getInstanceById($agentId);
            //$agentDisplayValue = $agentRecordModel->get('agency_name').' ('.$agentRecordModel->get('agency_code').')';
            //$agentDisplays[$agentId] = $agentDisplayValue;
            $agentDisplays[$agentId] = $this->getDisplayValue($agentId);
        }
        return $agentDisplays;
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }

        $agentsIds = explode(' |##| ', $value);
        $displayValue = '';

        foreach ($agentsIds as $agentId) {
            if ($agentId == '') {
                continue;
            }

            $db = &PearDatabase::getInstance();
            $res = $db->pquery('SELECT CONCAT(agency_name,agency_code) AS display_name FROM vtiger_agentmanager WHERE agentmanagerid=?',
                               [$agentId]);
            if($db->num_rows($res) == 0)
            {
                $res = $db->pquery('SELECT vanline_name AS display_name FROM vtiger_vanlinemanager WHERE vanlinemanagerid=?',
                                   [$agentId]);
            }
            if($res && $row = $res->fetchRow())
            {
                $displayValue.= $row['display_name'];
            } else {
                $displayValue.= '--';
            }

            if ($agentId != end($agentsIds)) {
                $displayValue .= ', ';
            }
        }

        return $displayValue;
    }

    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        return $value;
    }
}
