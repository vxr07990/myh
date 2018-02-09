<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Multiagent_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/MultiAgent.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        $db = PearDatabase::getInstance();
        $displayValues = array();
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        $brokenValue = explode(' |##| ', $value);
        foreach ($brokenValue as $selectedAgent) {
            $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid = ?";
            $result = $db->pquery($sql, array($selectedAgent));
            $row = $result->fetchRow();
            $displayValues[] = $row[0];
        }
        $displayValues = implode(' |##| ', $displayValues);
        return str_ireplace(' |##| ', ', ', $displayValues);
    }
    
    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        return $value;
    }
    
    
    public function getListSearchTemplateName()
    {
        return 'uitypes/MultiAgentFieldSearchView.tpl';
    }
}
