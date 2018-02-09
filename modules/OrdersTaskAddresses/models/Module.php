
<?php

class OrdersTaskAddresses_Module_Model extends Vtiger_Module_Model
{
    function assignValueForOrdersTaskAddresses(&$viewer,$record = false,$sourceRecord = false){
        $viewer->assign('IS_ACTIVE_ADDRESS', true);
        $fields = $this->getFields('LBL_ADDRESS_DETAIL');
        unset($fields['orderstask_id']);
        if ($sourceRecord) {
            $addressList = $this->getAddressesItem($sourceRecord);
            foreach ($addressList as $index => &$address){
               $address['orderstaskaddressesid'] = '';
            }
            $viewer->assign('ADDRESSES', $addressList);
        }elseif($record){
            $viewer->assign('ADDRESSES', $this->getAddressesItem($record));
        }
        $viewer->assign('ADDRESSES_BLOCK_FIELDS', $fields);
    }
    public function getAddressesItem($recordId)
    {
        $data= [];
        $db = PearDatabase::getInstance();

        $stmt = "SELECT vtiger_orderstaskaddresses.* FROM `vtiger_orderstaskaddresses` "
            . " INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = `vtiger_orderstaskaddresses`.orderstaskaddressesid"
            . " WHERE vtiger_orderstaskaddresses.orderstask_id = ? AND deleted = 0 ";
        
        $params = [$recordId];
        $result = $db->pquery($stmt, $params);
        while ($row =$db->fetchByAssoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    function saveAddresses($request,$relId){
        global $adb;
        for ($index = 1; $index <= $request['numAddress']; $index++) {
            $deleted = $request['address_deleted_'.$index];
            $addresslistid = $request['orderstask_address_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($addresslistid);
                $recordModel->delete();
            } else {
                if ($addresslistid == '') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("OrdersTaskAddresses");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($addresslistid);
                    $recordModel->set('id', $addresslistid);
                    $recordModel->set('mode', 'edit');
                }
                $fieldModelList = $recordModel->getModule()->getFields();
                foreach ($fieldModelList as $fieldName => $fieldModel) {
                    $fieldValue = $request[$fieldName.'_'.$index];
                    $fieldDataType = $fieldModel->getFieldDataType();
                    if ($fieldDataType == 'time') {
                        $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                    }
                    if ($fieldValue) {
                        if (!is_array($fieldValue)) {
                            $fieldValue = trim($fieldValue);
                        }
                        $recordModel->set($fieldName, $fieldValue);
                    }
                    if($fieldName == 'orderstask_id'){
                        $recordModel->set('orderstask_id',$relId);
                    }
                }
                $recordModel->save();
            }
        }
        
    }
}