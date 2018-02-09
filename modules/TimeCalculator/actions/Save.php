<?php
class TimeCalculator_Save_Action extends Vtiger_Save_Action
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
        $recordModel = $this->saveRecord($request);
        if($request->get('RelatedSave')) {
            $result['_recordLabel'] = $recordModel->getName();
            $result['_recordId'] = $recordModel->getId();

            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($result);
            $response->emit();
        }else{
            if($request->get('relationOperation')) {
                $parentModuleName = $request->get('sourceModule');
                $parentRecordId = $request->get('sourceRecord');
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
                //TODO : Url should load the related list instead of detail view of record
                $loadUrl = $parentRecordModel->getDetailViewUrl();
            } else if ($request->get('returnToList')) {
                $loadUrl = $recordModel->getModule()->getListViewUrl();
            } else {
                $loadUrl = $recordModel->getDetailViewUrl();
            }
            header("Location: $loadUrl");
        }
    }
}