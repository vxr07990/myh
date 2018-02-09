<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_Vehiclepicklist_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/VehiclePicklist.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     *
     * @param  <Object> $value
     *
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        $fieldModel = $this->get('field');
        $fieldName = $fieldModel->getName();
        if ($fieldName != 'sales_person') {
            return Vtiger_Language_Handler::getTranslatedString($value, $this->get('field')->getModuleName());
        } elseif ($value != 0) {
            return Users_Record_Model::getInstanceById($value, 'Users')->getDisplayName();
        }
        return '';
    }

    public function getListSearchTemplateName()
    {
        return 'uitypes/ViclePicklist.tpl';
    }
}
