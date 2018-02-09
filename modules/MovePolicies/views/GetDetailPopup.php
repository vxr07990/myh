<?php
class MovePolicies_GetDetailPopup_View extends Vtiger_IndexAjax_View{
    function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $sourceRecord = $request->get('src_record');
        $viewer           = $this->getViewer($request);
        $moduleName = $request->getModule();
        $recordId   = $this->getMovePolicies($sourceRecord);
        if($recordId > 0){
            if (!$this->record) {
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            }
            $recordModel    = $this->record->getRecord();
            $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
            if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
                $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
                $recordModel->hiddenBlocks = $hiddenBlocks;
            }
            $structuredValues = $recordStrucure->getStructure();
            $moduleModel      = $recordModel->getModule();

            $viewer->assign('RECORD', $recordModel);
            $viewer->assign('RECORD_STRUCTURE', $structuredValues);
            $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->assign('MODULE_NAME', $moduleName);
            $tariffItems = $recordModel->getTariffItems();
            unset($tariffItems['items_count']);

            $miscTariffItems = $recordModel->getMiscTariffItems();

            $viewer = $this->getViewer($request);
            $viewer->assign('TARIFF_ITEMS', $tariffItems);
            $viewer->assign('MISC_TARIFF_ITEMS', $miscTariffItems);
        }
        $viewer->assign('RECORD_ID', $recordId);
        echo $viewer->view('DetailPopup.tpl',$moduleName,true);
    }
    function getMovePolicies($sourceRecord){
        global $adb;
        $recordId = 0;
        if(!empty($sourceRecord)){
            $recordModel = Vtiger_Record_Model::getInstanceById($sourceRecord);
            $sourceModule = $recordModel->getModuleName();
            $sql = "SELECT vtiger_movepolicies.movepoliciesid FROM vtiger_movepolicies 
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_movepolicies.movepoliciesid
                    WHERE vtiger_crmentity.deleted = 0 ";
            $params = [];
            switch ($sourceModule){
                case 'Orders':
                    $accountId = $recordModel->get('orders_account');
                    $tariffId = $recordModel->get('tariff_id');
                    $contractId = $recordModel->get('account_contract');
                    break;
                case 'Estimates':
                case 'Actuals':
                    $accountId = $recordModel->get('account_id');
                    $rsTariff = $adb->pquery("SELECT effective_tariff FROM vtiger_quotes WHERE  quoteid = ?",[$sourceRecord]);
                    if($adb->num_rows($rsTariff) > 0 ){
                        $tariffId = $adb->query_result($rsTariff,0,'effective_tariff');
                    }
                    $contractId = $recordModel->get('contract');

                    break;
                case 'Opportunities':
                    $accountId = $recordModel->get('related_to');
                    $contractId = $recordModel->get('oppotunitiescontract');
                default:
                    break;
            }
            if(!empty($accountId)){
                $sql .= " AND vtiger_movepolicies.policies_accountid = ? ";
                $params[] = $accountId;
            }
            if(!empty($tariffId)){
                $sql .= " AND (IFNULL(vtiger_movepolicies.policies_tariffid,0) = 0 OR vtiger_movepolicies.policies_tariffid = ?) ";
                $params[] = $tariffId;
            }
            if(!empty($contractId)){
                $sql .= " AND (IFNULL(vtiger_movepolicies.policies_contractid,0) = 0 OR vtiger_movepolicies.policies_contractid = ?) ";
                $params[] = $contractId;
            }
            $sql.= " ORDER BY vtiger_crmentity.createdtime DESC
                     LIMIT 1";
            $rs  = $adb->pquery($sql,$params);
            if($adb->num_rows($rs) > 0){
                $recordId = $adb->query_result($rs,0,'movepoliciesid');
            }
            return $recordId;
        }
    }
}
