<?php
class CommissionPlansItem_Module_Model extends Vtiger_Module_Model
{
    public function getCommissionPlansItem($recordId, $mode = false)
    {
        $data= [];
        $db = PearDatabase::getInstance();

        $stmt = "SELECT * FROM `vtiger_commissionplansitem` "
            . " INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = `vtiger_commissionplansitem`.commissionplansitemid"
            . " WHERE "
            . " commissionplansfilterid = ? "
            . " AND deleted = 0";
        $params = [$recordId];
        $result = $db->pquery($stmt, $params);
        while ($row =&$result->fetchRow()) {
            $data[] = $row;
        }
        return $data;
    }
    public function saveCommissionPlansItem($request, $relId)
    {
        $moduleModel = Vtiger_Module_Model::getInstance('CommissionPlansItem');
        for ($index = 1; $index <= $request['numItems']; $index++) {
            $deleted = $request['CommissionPlansItem_deleted_'.$index];
            $participantId = $request['CommissionPlansItem_id_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                $recordModel->delete();
            } else {
                if (!$participantId) {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("CommissionPlansItem");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                    $recordModel->set('id', $participantId);
                    $recordModel->set('mode', 'edit');
                }

                $fieldModelList = $moduleModel->getFields();
                foreach ($fieldModelList as $fieldName => $fieldModel) {
                    $fieldValue = $request[$fieldName.'_'.$index];
                    $fieldDataType = $fieldModel->getFieldDataType();
                    if ($fieldDataType == 'time') {
                        $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                    }
                        if (!is_array($fieldValue)) {
                            $fieldValue = trim($fieldValue);
                        }
                        $recordModel->set($fieldName, $fieldValue);

                    if ($fieldName=='commissionplansfilterid') {
                        $recordModel->set($fieldName, $relId);
                    }
                }
                $recordModel->save();
            }
        }
    }
}
