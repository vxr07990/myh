
<?php

class AddressList_Module_Model extends Vtiger_Module_Model
{
    function assignValueForAddressList($viewer,$record = false,$sourceRecord = false){
        $viewer->assign('IS_ACTIVE_ADDRESSLIST', true);
        $fields = $this->getFields('LBL_ADDRESSES');
        if ($sourceRecord) {
            $addressList = $this->getAddressListItem($sourceRecord);
            foreach ($addressList as $index => &$address){
               $address['addresslistid'] = '';
            }
            $viewer->assign('ADDRESSESLIST', $addressList);
        }elseif($record){
            $viewer->assign('ADDRESSESLIST', $this->getAddressListItem($record));
        }
        $viewer->assign('ADDRESSLIST_BLOCK_FIELDS', $fields);
    }
    public function getAddressListItem($recordId, $mode = false)
    {
        $data= [];
        $db = PearDatabase::getInstance();

        $stmt = "SELECT vtiger_addresslist.* FROM `vtiger_addresslist` "
            . " INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = `vtiger_addresslist`.addresslistid"
            . " INNER JOIN `vtiger_addresslistrel` ON `vtiger_addresslistrel`.addresslistid = `vtiger_addresslist`.addresslistid"
            . " WHERE "
            . " vtiger_addresslistrel.crmid = ? "
            . " AND deleted = 0 "
            . " ORDER BY vtiger_addresslistrel.sequence ASC ";
        $params = [$recordId];
        $result = $db->pquery($stmt, $params);
        while ($row =$db->fetchByAssoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    function saveAddressList($request,$relId){
        global $adb;
        for ($index = 1; $index <= $request['numAddress']; $index++) {
            $deleted = $request['address_deleted_'.$index];
            $addresslistid = $request['addresslistid_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($addresslistid);
                $recordModel->delete();
            } else {
                if ($addresslistid == '') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("AddressList");
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
                }
                $recordModel->save();

                //save related record
                if($addresslistid == ''){
                    $adb->pquery("INSERT INTO vtiger_addresslistrel(`addresslistid`,`crmid`,`sequence`) VALUES (?,?,?)",[$recordModel->getId(),$relId,$index]);
                }else{
                    $adb->pquery("UPDATE vtiger_addresslistrel SET `sequence` = ? WHERE `addresslistid` =? AND `crmid` =?",[$index,$recordModel->getId(),$relId]);
                }
            }
        }
        
    }
}