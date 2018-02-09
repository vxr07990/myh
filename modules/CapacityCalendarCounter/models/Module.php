<?php

class CapacityCalendarCounter_Module_Model extends Vtiger_Module_Model
{
    public function getCapacityCalendarCounter($recordId = false)
    {
        $rows = array();
        $db              = PearDatabase::getInstance();
        $sql             = 'SELECT * FROM `vtiger_capacitycalendarcounter` 
                            INNER JOIN vtiger_crmentity ON `vtiger_crmentity`.crmid = `vtiger_capacitycalendarcounter`.capacitycalendarcounterid 
                            WHERE vtiger_crmentity.deleted = 0 AND capacitycalendarcounter_relcrmid=?';
        $result          = $db->pquery($sql, [$recordId]);

        if ($db->num_rows($result)>0) {
            while ($row=$db->fetchByAssoc($result)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function saveCapacityCalendarCounter($request, $relId)
    {
        for ($index = 1; $index <= $request['numAgents']; $index++) {
            if (!$request['capacitycalendarcounterId_'.$index]) {
                continue;
            }
            $deleted = $request['capacitycalendarcounterDelete_'.$index];
            $participantId = $request['capacitycalendarcounterId_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                $recordModel->delete();
            } else {
                if ($participantId == 'none') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("CapacityCalendarCounter");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                    $recordModel->set('id', $participantId);
                    $recordModel->set('mode', 'edit');
                }
                $recordModel->set('calendar_code', $request['calendar_code_'.$index]);
                $recordModel->set('order_task_field', $request['order_task_field_'.$index]);
                $recordModel->set('capacitycalendarcounter_relcrmid', $relId);
                $recordModel->save();
            }
        }
    }
    public function getAvailableCalendarCodes($participatingAgent = null, $searchValue = '')
    {
        $db = PearDatabase::getInstance();
        $availableCalendarCodeIds = array();
        if($participatingAgent){
            $sql = "SELECT capacitycalendarcounterid FROM vtiger_capacitycalendarcounter "
                    . "INNER JOIN vtiger_crmentity ON vtiger_capacitycalendarcounter.capacitycalendarcounterid = vtiger_crmentity.crmid "
                    . "INNER JOIN vtiger_agents ON vtiger_agents.agentmanager_id = vtiger_crmentity.agentid "
                    . "WHERE deleted = 0 AND  vtiger_agents.agentsid = $participatingAgent ";
            if($searchValue != ''){
                $sql .= " AND calendar_code LIKE '%$searchValue%' ";
            }
            $result = $db->pquery($sql);
            if($db->num_rows($result) > 0){
                while($row = $db->fetchByAssoc($result)){
                    $availableCalendarCodeIds[] = $row['capacitycalendarcounterid'];
                }
            }
        }
        return $availableCalendarCodeIds;
    }
}
