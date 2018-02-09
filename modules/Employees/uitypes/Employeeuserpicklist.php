<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Employees_Employeeuserpicklist_UIType extends Vtiger_Base_UIType
{

    public function getTemplateName(){
        return 'uitypes/EmployeeUserpicklist.tpl';
    }

    public function getDisplayValue($value){

        if($value == '0' || !$value){
            return '';
        }

		$userModel = Users_Record_Model::getCleanInstance('Users');
		$userModel->set('id', $value);
		$detailViewUrl = $userModel->getDetailViewUrl();

		$displayvalue[] = "<a href=" .$detailViewUrl. ">" .getOwnerName($value). "</a>&nbsp";
        $displayvalue = implode(',', $displayvalue);
        return $displayvalue;
    }
	
    public function getListSearchTemplateName(){
        return 'uitypes/EmployeeUserpicklistFieldSearchView.tpl';
    }
}
