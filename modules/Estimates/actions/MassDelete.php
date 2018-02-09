<?php

class Estimates_MassDelete_Action extends Vtiger_MassDelete_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $recordIds = $this->getRecordsListFromRequest($request);
        foreach ($recordIds as $recordId) {
            $recordInstance = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            if($recordInstance->get('quotestage') == 'Accepted'){
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED_ESTIMATE_ACCEPTED'));
            }
        }
        
    }
}