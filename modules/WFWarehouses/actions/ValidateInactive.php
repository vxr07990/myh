<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/26/2017
 * Time: 5:22 PM
 */

class WFWarehouses_ValidateInactive_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $warehouse = $request->get('warehouse');
        $checkStatus = $request->get('invalidStatus');
        $db = PearDatabase::getInstance();

        $sql = "SELECT * FROM `vtiger_wflocations` 
                LEFT JOIN `vtiger_crmentity` ON wflocationsid = crmid
                WHERE wflocations_status = ?
                AND wflocation_warehouse = ?
                AND `vtiger_crmentity`.deleted = 0";

        $check = $db->pquery($sql,[$checkStatus,$warehouse]);
        $rows = $db->num_rows($check);
        if ($rows == 0) {
            $result = array('success'=>true);
        } else {
            $result = array('success'=>false, 'message'=>vtranslate('LBL_ACTIVES_EXIST', $moduleName));
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
