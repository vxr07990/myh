<?php

class AgentManager_Module_Model extends Vtiger_Module_Model
{
    public static function getAllRecords()
    {
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT agentmanagerid, agency_name FROM `vtiger_agentmanager` JOIN `vtiger_crmentity` ON crmid=agentmanagerid WHERE deleted=0 ORDER BY agency_name ASC";
        $result = $db->pquery($sql, array());
        
        $records = array(array());
        
        while ($row =& $result->fetchRow()) {
            $recordModel = Vtiger_Record_Model::getInstanceById($row[0]);
            $records[$recordModel->get('vanline_id')][] = $recordModel;
        }
        
        $agents = array(array());
        
        foreach ($records as $vanlineId=>$vanlineAgents) {
            $numAgents = count($vanlineAgents);
            $nextIndex = -1;
            $indexOffset = ceil($numAgents/2);
            foreach ($vanlineAgents as $key=>$agentRecord) {
                if ($key % 2 == 0) {
                    $nextIndex++;
                    $agents[$vanlineId][$nextIndex] = $agentRecord;
                } else {
                    $agents[$vanlineId][$nextIndex+$indexOffset] = $agentRecord;
                }
            }
            ksort($agents[$vanlineId]);
        }
        
        return $agents;
    }
    
    public function isSummaryViewSupported()
    {
        return false;
    }
    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }
}
