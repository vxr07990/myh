<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Assignedvehicles_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/AssignedVehicles.tpl';
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

        $vehiclesIds = explode(' |##| ', $value);
        $displayValue = '';

        foreach ($vehiclesIds as $vehicleId) {
            if ($vehicleId == '') {
                continue;
            }
            try {
                $vehicleRecordModel = Vtiger_Record_Model::getInstanceById($vehicleId, 'Vehicles');
                $displayValue .= $vehicleRecordModel->get('vechiles_unit').' ('.$vehicleRecordModel->get('vehicle_type').')';
            } catch (Exception $e) {
                $displayValue .= '(deleted)';
            }
            if ($vehicleId != end($vehiclesIds)) {
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
