<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MoveRoles_FilterByBusinesslineForAccount_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $accountid = $request->get('accountid');
        $business_line = $request->get('business_line');
        if (getenv('IGC_MOVEHQ') || getenv('INSTANCE_NAME') == 'national') {
            global $adb;
            $rs = $adb->pquery("SELECT vtiger_additional_roles.*,vtiger_employeeroles.emprole_desc,CONCAT(vtiger_employees.name,' ',vtiger_employees.employee_lastname) as employee_name
            FROM
                vtiger_additional_roles
            JOIN
            vtiger_employeeroles 
            ON(vtiger_employeeroles.employeerolesid = vtiger_additional_roles.role)
            JOIN vtiger_employees
            ON (vtiger_employees.employeesid = vtiger_additional_roles.user)
            WHERE account_id = ? AND commodity LIKE ?",array($accountid,"%$business_line%"));
            $data = array();
            if($count = $adb->num_rows($rs) >0){
                $i = 0;
                while ($result = $adb->fetchByAssoc($rs)){
                    $data[$i]['user'] = $result['user'];
                    $data[$i]['role'] = $result['role'];
                    $data[$i]['emprole_desc'] = $result['emprole_desc'];
                    $data[$i]['employee_name'] = $result['employee_name'];
                    $i++;
                }
            }
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();        
        }

    }

}
