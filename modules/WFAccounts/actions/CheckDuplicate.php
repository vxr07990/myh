<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/27/2017
 * Time: 12:29 PM
 */
class WFAccounts_CheckDuplicate_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $accountName = $request->get('accountname');
        $record = $request->get('record');

        if ($record) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        }

        $recordModel->set('label', $accountName);
        if (!$recordModel->checkDuplicate($request->get('agentid'))) {
            $result = array('success'=>false);
        } else {
            $result = array('success'=>true, 'message'=>vtranslate('LBL_DUPLICATES_EXIST', $moduleName));
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
