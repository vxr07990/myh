<?php

class AgentManager_ListView_Model extends Vtiger_ListView_Model
{
    public function getListViewEntries($pagingModel)
    {
        $listViewRecordModels = parent::getListViewEntries($pagingModel);
        
        $db = PearDatabase::getInstance();
        
        //file_put_contents('logs/AgentManagerListView.log', date('Y-m-d H:i:s - ').print_r($listViewRecordModels, true)."\n", FILE_APPEND);

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $currentUserModel->getId();
        $isAdmin = $currentUserModel->isAdminUser();
        $trimmedRecordModels = array();
        
        if (!$isAdmin) {
            $sql = "SELECT depth FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $depth = $row[0];
            
            $validAgencies = array();
            
            if ($depth == 3) {
                file_put_contents('logs/AgentManagerListView.log', "\n depth is 3", FILE_APPEND);
                $sql = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid=?";
                $result = $db->pquery($sql, array($currentUserId));
                $row = $result->fetchRow();
                $vanlineid = $row[0];
                file_put_contents('logs/AgentManagerListView.log', "\n \$vanlineid: ".$vanlineid, FILE_APPEND);
                
                $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id=?";
                $result = $db->pquery($sql, array($vanlineid));
                while ($row =& $result->fetchRow()) {
                    $validAgencies[] = $row[0];
                }
            } else {
                $sql = "SELECT userid, agency_code FROM `vtiger_user2agency` WHERE userid=?";
                $result = $db->pquery($sql, array($currentUserId));
                while ($row =& $result->fetchRow()) {
                    $validAgencies[] = $row[1];
                }
            }
            file_put_contents('logs/AgentManagerListView.log', "\n \$validAgencies: ".print_r($validAgencies, true), FILE_APPEND);
            foreach ($validAgencies as $index => $agencyCode) {
                $trimmedRecordModels[$agencyCode] = $listViewRecordModels[$agencyCode];
            }
        } else {
            $trimmedRecordModels = $listViewRecordModels;
        }
        
        $pagingModel->calculatePageRange($trimmedRecordModels);
        
        //file_put_contents('logs/AgentManagerListView.log', date('Y-m-d H:i:s - ').$currentUserId.": ".$isAdmin."\n", FILE_APPEND);

        //file_put_contents('logs/AgentManagerListView.log', date('Y-m-d H:i:s - ').print_r($trimmedRecordModels, true)."\n", FILE_APPEND);
        return $trimmedRecordModels;
    }
}
