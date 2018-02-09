<?php
class ItemCodes_Duplicate_Action extends Vtiger_Action_Controller{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        if(!empty($recordId)){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);

            $ItemCodesMappingModel=Vtiger_Module_Model::getInstance('ItemCodesMapping');
            if ($ItemCodesMappingModel && $ItemCodesMappingModel->isActive()) {
                $itemCodesMapping = $ItemCodesMappingModel->getItemCodesMapping($recordId);
                $counter = 0;
                foreach ($itemCodesMapping as $itemId =>$itemRecordModel){
                    $counter ++;
                    $_REQUEST['itcmapping_businessline_'.$counter]=explode(" |##| ",$itemRecordModel->get('itcmapping_businessline'));
                    $_REQUEST['itcmapping_billingtype_'.$counter]=explode(" |##| ",$itemRecordModel->get('itcmapping_billingtype'));
                    $_REQUEST['itcmapping_authority_'.$counter]=explode(" |##| ",$itemRecordModel->get('itcmapping_authority'));
                    $_REQUEST['itcmapping_glcode_'.$counter]=$itemRecordModel->get('itcmapping_glcode');
                    $_REQUEST['itcmapping_salesexpense_'.$counter]=$itemRecordModel->get('itcmapping_salesexpense');
                    $_REQUEST['itcmapping_owner_operatorexpense_'.$counter]=$itemRecordModel->get('itcmapping_owner_operatorexpense');
                    $_REQUEST['itcmapping_company_driverexpense_'.$counter]=$itemRecordModel->get('itcmapping_company_driverexpense');
                    $_REQUEST['itcmapping_lease_driverexpense_'.$counter]=$itemRecordModel->get('itcmapping_lease_driverexpense');
                    $_REQUEST['itcmapping_packer_expense_'.$counter]=$itemRecordModel->get('itcmapping_packer_expense');
                    $_REQUEST['itcmapping_3rdparty_serviceexpense_'.$counter]=$itemRecordModel->get('itcmapping_3rdparty_serviceexpense');
                    $_REQUEST['itemcodesmappingid_'.$counter]='';
                }
                $_REQUEST['numMapping']=$counter;
            }
            $recordModel->setId('');
            $recordModel->save();
            $loadUrl = $recordModel->getDetailViewUrl();
            header("Location: $loadUrl");
        }
    }
}