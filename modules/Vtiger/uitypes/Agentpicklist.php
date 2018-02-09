<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_Agentpicklist_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/AgentPicklist.tpl';
    }

    public function getAgentDisplay($agentId)
    {
        //$agentRecordModel = Vtiger_Record_Model::getInstanceById($agentId);
        //$agentDisplayValue = $agentRecordModel->get('agency_name').' ('.$agentRecordModel->get('agency_code').')';
        //return $agentDisplayValue;
        return $this->getDisplayValue($agentId);
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        if ($value == '' || $value == 0) {
            return;
        }
        $db = &PearDatabase::getInstance();
        if(getenv('IGC_MOVEHQ')){
            $res = $db->pquery("SELECT CONCAT('(',agency_code, ') ',agency_name) AS display_name FROM vtiger_agentmanager WHERE agentmanagerid=?",
                               [$value]);
        } else {
            $res = $db->pquery('SELECT CONCAT(agency_name,agency_code) AS display_name FROM vtiger_agentmanager WHERE agentmanagerid=?',
                               [$value]);
        }
        if($db->num_rows($res) == 0)
        {
            $res = $db->pquery('SELECT vanline_name AS display_name FROM vtiger_vanlinemanager WHERE vanlinemanagerid=?',
                               [$value]);
        }
        if($res && $row = $res->fetchRow())
        {
            return $row['display_name'];
        }
        return '--';
    }

    public function getListSearchTemplateName()
    {
        return 'uitypes/AgentPicklistFieldSearchView.tpl';
    }
}
