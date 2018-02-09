<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_Tariffservice_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/Tariffservice.tpl';
    }
    
    public function getAppliedServices($effectiveId)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT tariffservicesid, service_name FROM vtiger_tariffservices WHERE effective_date = $effectiveId AND rate_type <> 'Service Base Charge'");
        $applicableServices = [];
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $applicableServices[$row['tariffservicesid']] = $row['service_name'];
            }
        }

        return $applicableServices;
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
        return str_ireplace(' |##| ', ', ', $value);
    }
    
    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        return $value;
    }
}
