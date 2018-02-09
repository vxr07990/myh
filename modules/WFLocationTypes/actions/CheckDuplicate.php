<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WFLocationTypes_CheckDuplicate_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $warehouse = $request->get('warehouse');
        $prefix = $request->get('prefix');
        $record = $request->get('record');
        $db = PearDatabase::getInstance();

        $sql = "SELECT * FROM `vtiger_wflocationtypes` WHERE wflocationtypes_prefix = ? AND warehouse = ?";

        if($record) {
          $sql .= " AND wflocationtypesid <> ?";
        }

        $check = $db->pquery($sql,[$prefix,$warehouse,$record]);
        $rows = $db->num_rows($check);
        if ($rows == 0) {
          $result = array('success'=>true);
        } else {
          $result = array('success'=>false, 'message'=>vtranslate('LBL_DUPLICATES_EXIST', $moduleName));
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
