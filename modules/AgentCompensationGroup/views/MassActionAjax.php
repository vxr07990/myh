<?php

class AgentCompensationGroup_MassActionAjax_View extends Vtiger_MassActionAjax_View {
    public function duplicateRecords(Vtiger_Request $request)
    {
        $selectedIds = $request->get('selected_ids');
        $moduleName  = $request->getModule();
        foreach ($selectedIds as $recordId) {
            echo "<div> selectedId: ".$recordId."<br> moduleName : ".$moduleName."</div>";
            $copyRecordModel = Vtiger_Record_Model::getInstanceById($recordId);

            // Copy AgentCompensationItems records
            $AgentCompensationItemsModel=Vtiger_Module_Model::getInstance('AgentCompensationItems');
            if ($AgentCompensationItemsModel && $AgentCompensationItemsModel->isActive()) {
                $itemRecordsList = $AgentCompensationItemsModel->getAgentCompensationItems($recordId);
                $counter = 0;
                foreach ($itemRecordsList as $itemId =>$itemRecordModel){
                    $counter ++;
                    $_REQUEST['agcomitem_name_'.$counter]=$itemRecordModel->get('agcomitem_name');
                    $_REQUEST['agcomitem_bookerdistribution_'.$counter]=$itemRecordModel->get('agcomitem_bookerdistribution');
                    $_REQUEST['agcomitem_origindistribution_'.$counter]=$itemRecordModel->get('agcomitem_origindistribution');
                    $_REQUEST['agcomitem_haulingdistribution_'.$counter]=$itemRecordModel->get('agcomitem_haulingdistribution');
                    $_REQUEST['agcomitem_general_officedistribution_'.$counter]=$itemRecordModel->get('agcomitem_general_officedistribution');
                    $_REQUEST['agcomitem_distribution_'.$counter]=$itemRecordModel->get('agcomitem_distribution');
                    $_REQUEST['itemsid_'.$counter]='';
                }
                $_REQUEST['numAgentItems']=$counter;
            }

            // Copy Escrows records
            $EscrowsModel=Vtiger_Module_Model::getInstance('Escrows');
            if($EscrowsModel && $EscrowsModel->isActive()) {
                $EscrowsList = $EscrowsModel->getEscrows($recordId);
                $counter = 0;
                foreach ($EscrowsList as $itemId =>$rescrowRecordModel){
                    $counter ++;
                    $_REQUEST['escrows_desc_'.$counter]=$rescrowRecordModel->get('escrows_desc');
                    $_REQUEST['escrows_status_'.$counter]=$rescrowRecordModel->get('escrows_status');
                    $_REQUEST['escrows_calculation_type_'.$counter]=$rescrowRecordModel->get('escrows_calculation_type');
                    $_REQUEST['escrows_pct_amount_'.$counter]=$rescrowRecordModel->get('escrows_pct_amount');
                    $_REQUEST['escrows_chargeback_from_'.$counter]=$rescrowRecordModel->get('escrows_chargeback_from');
                    $_REQUEST['escrows_discount_type_'.$counter]=$rescrowRecordModel->get('escrows_discount_type');
                    $_REQUEST['escrows_chargeback_type_'.$counter]=$rescrowRecordModel->get('escrows_chargeback_type');
                    $_REQUEST['escrows_chargeback_to_'.$counter]=$rescrowRecordModel->get('escrows_chargeback_to');
                    $_REQUEST['escrows_from_itemcode_'.$counter]=$rescrowRecordModel->get('escrows_from_itemcode');
                    $_REQUEST['escrows_to_itemcode_'.$counter]=$rescrowRecordModel->get('escrows_to_itemcode');
                    $_REQUEST['escrowsid_'.$counter]='';
                }
                $_REQUEST['numMapping']=$counter;
            }


            // Set value for multiple picklist
            $copyRecordModel->set('agentcompgr_businessline', explode(' |##| ', $copyRecordModel->get("agentcompgr_businessline")));
            $copyRecordModel->set('agentcompgr_billingtype', explode(' |##| ', $copyRecordModel->get("agentcompgr_billingtype")));
            $copyRecordModel->set('agentcompgr_authority', explode(' |##| ', $copyRecordModel->get("agentcompgr_authority")));
            $copyRecordModel->setId('');
            $copyRecordModel->save();
            echo "<div><b>Duplicated!</b></div>";
        }
    }
}
