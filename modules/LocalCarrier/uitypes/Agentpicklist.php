<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class LocalCarrier_Agentpicklist_UIType extends Vtiger_Agentpicklist_UIType
{

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        if ($value == '') {
            return;
        }

        $displayValue = '';
        $agentRecordModel = Vtiger_Record_Model::getInstanceById($value, 'AgentManager');
        $vanlineRecordModel = Vtiger_Record_Model::getInstanceById($value, 'VanlineManager');

        if ($agentRecordModel->get('agency_name')) {
            $displayValue = '<a href="/index.php?module=AgentManager&view=Detail&record=' . $value . '">'
                            . $agentRecordModel->get('agency_name') . ' ('.$agentRecordModel->get('agency_code').')'
                            . '</a>';
        } else {
            $displayValue = $vanlineRecordModel->get('vanline_name');
        }

        return $displayValue;
    }
}
