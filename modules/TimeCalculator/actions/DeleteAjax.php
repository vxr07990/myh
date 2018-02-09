<?php
class TimeCalculator_DeleteAjax_Action extends Vtiger_Save_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        if (!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request) {
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record);
        $recordModel->delete();
        $response = new Vtiger_Response();
        $response->emit();
    }
}