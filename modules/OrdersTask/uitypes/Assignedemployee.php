<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Assignedemployee_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/AssignedEmployee.tpl';
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

        $employeeIds = explode(' |##| ', $value);
        $displayValue = '';

        foreach ($employeeIds as $employeeId) {
            if ($employeeId == '') {
                continue;
            }
            try {
                $employeeRecordModel = Vtiger_Record_Model::getInstanceById($employeeId, 'Employees');
                $displayValue .= $employeeRecordModel->get('name').' '.$employeeRecordModel->get('employee_lastname');
                $displayValue .= ' ('.$employeeRecordModel->get('employee_type');
            } catch(Exception $e){
                $displayValue .= '(deleted)';
            }
            //Need to get the assigned role in the task

            if ($_REQUEST['module'] == 'OrdersTask' && $_REQUEST['record']!='') {
                $recordId = vtlib_purify($_REQUEST['record']);

                $db = PearDatabase::getInstance();
				$result = $db->pquery("SELECT emprole_desc AS role FROM vtiger_employeeroles WHERE employeerolesid IN (SELECT role FROM vtiger_orderstasksemprel WHERE taskid=? AND employeeid=?)", array($recordId, $employeeId));
				if ($result && $db->num_rows($result) > 0) {
                    $displayValue .=  ' - ' .  $db->query_result($result, 0, 'role');
                }
                $displayValue .=  ')';
            }

            //            if($employeeRecordModel->get('employee_type')=='Contractor'){
            //                $displayValue .= $employeeRecordModel->get('contractor_prole') . ')';
            //            }else{
            //                $displayValue .= $employeeRecordModel->get('employee_prole') . ')';
            //            }

            if ($employeeId != end($employeeIds)) {
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
