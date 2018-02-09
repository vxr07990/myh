<?php

class Claims_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $db         = PearDatabase::getInstance();
        $moduleName = $request->getModule();
        $record     = $request->get('record');

        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);

            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $paymentList = [];
        if (!empty($record)) {
            $paymentList = Claims_Module_Model::getPaymentList($record);
        }
        $viewer->assign('PAYMENT_LIST', $paymentList);
        
        $statusChangeList = [];
        if (!empty($record)) {
            $statusChangeList = Claims_Module_Model::getStatusChangeList($record, $recordModel->get("claims_status_statusgrid"), $recordModel->get("claims_reason_statusgrid"));
        }
        $viewer->assign('STATUS_LIST', $statusChangeList);
        
        $claimsItemsArray = Claims_Module_Model::getClaimsItemsArr($request);
        $viewer->assign('CLAIMS_ITEMS_COUNT', count($claimsItemsArray));
        $viewer->assign('CLAIMS_ITEMS_ARRAY', $claimsItemsArray);
        $viewer->assign('CLAIMS_ITEMS_ARRAY_HEADER', Claims_Module_Model::getClaimsItemsArrHeader($request));
        
        $summaryTable = Claims_Module_Model::getSummaryTable($record);
        $viewer->assign('SUMMARY_TABLE', $summaryTable);
        $viewer->assign('FLAG', "Edit");
	
	if(!empty($record)){
	    $viewer->assign('CLAIM_SUMMARY_ID', $recordModel->get('claimssummary_id'));
	}  else {
	    $viewer->assign('CLAIM_SUMMARY_ID', $request->get('sourceRecord'));
	}
	
        
    //participants block
    $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $viewer->assign('SERVICE_PROVIDER_RESPO', true);
        //call to get db data
        $list = Claims_Module_Model::getGridItems('spr', $record);
            $viewer->assign('SERVICE_PROVIDER_LIST', $list);
        }
        parent::process($request);
    }
}
