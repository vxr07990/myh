<?php

class RevenueGroupingItem_Module_Model extends Vtiger_Module_Model
{
    public function getRevenueGroupingItem($recordId = false)
    {
        $rows = array();
        $db              = PearDatabase::getInstance();
        $sql             = 'SELECT * FROM `vtiger_revenuegroupingitem`
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_revenuegroupingitem.revenuegroupingitemid
                            WHERE revenuegroupingitem_relcrmid=? AND vtiger_crmentity.deleted =0';
        $result          = $db->pquery($sql, [$recordId]);

        if ($db->num_rows($result)>0) {
            while ($row=$db->fetchByAssoc($result)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function saveRevenueGroupingItem($request, $relId)
    {
        global $adb;
        for ($index = 1; $index <= $request['numAgents']; $index++) {
            if (!$request['revenuegroupingitemId_'.$index]) {
                continue;
            }
            $deleted = $request['revenuegroupingitemDelete_'.$index];
            $participantId = $request['revenuegroupingitemId_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                $recordModel->delete();
            } else {
                if ($participantId == 'none') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("RevenueGroupingItem");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                    $recordModel->set('id', $participantId);
                    $recordModel->set('mode', 'edit');
                }
                $recordModel->set('revenuegroup', $request['revenuegroup_'.$index]);
                $recordModel->set('invoicesequence', $request['invoicesequence_'.$index]);
                $recordModel->set('revenuegroupingitem_relcrmid', $relId);
                $recordModel->save();

                // Set agentid for Revenue Grouping Item record
                $adb->pquery("update `vtiger_crmentity` set `agentid`=? where `crmid`=?", array($request['agentid'], $recordModel->getId()));
            }
        }
    }
}
