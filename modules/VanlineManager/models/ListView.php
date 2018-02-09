<?php
class VanlineManager_ListView_Model extends Vtiger_ListView_Model
{
    public function getListViewEntries($pagingModel)
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userRoleDepth = $currentUserModel->getUserRoleDepth();
        if ($userRoleDepth >= 4) {
            $listViewRecordModels = [];
            $vanlinesForUser = $currentUserModel->getAccessibleVanlinesForUser();
            foreach ($vanlinesForUser as $vanlineId => $vanlineName) {
                $listViewRecordModels[] = Vtiger_Record_Model::getInstanceById($vanlineId);
            }
        } else {
            $listViewRecordModels = parent::getListViewEntries($pagingModel);
        }
        //old securities
        //$db = PearDatabase::getInstance();
        //old securities
        //$db = PearDatabase::getInstance();

        //$currentUserModel = Users_Record_Model::getCurrentUserModel();
        //$currentUserId = $currentUserModel->getId();
        //$isAdmin = $currentUserModel->isAdminUser();
        //$trimmedRecordModels = array();
        /*if(!$isAdmin) {
            $validVanlines = array();
            $sql = "SELECT depth FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $depth = $row[0];
            if ($depth <= 4 ) {
                $sql = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
                $result = $db->pquery($sql, array($currentUserId));
                while($row =& $result->fetchRow()) {
                    $validVanlines[] = $row[0];
                    if($row['is_parent'] == 1) {
                        //One of the vanlines the user is associated with is the parent. Display all records
                        $pagingModel->calculatePageRange($listViewRecordModels);
                        return $listViewRecordModels;
                    }
                }
            }
            else {
                $sql = "SELECT userid, agency_code FROM `vtiger_user2agency` WHERE userid=?";
                $result = $db->pquery($sql, array($currentUserId));
                $validAgencies = array();

                $row = $result->fetchRow();
                while($row != NULL) {
                    $validAgencies[] = $row[1];
                    $row = $result->fetchRow();
                }
                foreach($validAgencies as $index => $agencyCode) {
                    $sql = "SELECT vanline_id FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
                    $result = $db->pquery($sql, array($agencyCode));
                    $row = $result->fetchRow();
                    if($row != NULL) {
                        $validVanlines[] = $row[0];
                    }
                }
            }
            foreach($validVanlines as $index => $vanlineId) {
                $trimmedRecordModels[$vanlineId] = $listViewRecordModels[$vanlineId];
            }
        }*/ //else {
            //$trimmedRecordModels = $listViewRecordModels;
        //}

        $pagingModel->calculatePageRange($listViewRecordModels);
        
        //file_put_contents('logs/AgentManagerListView.log', date('Y-m-d H:i:s - ').$currentUserId.": ".$isAdmin."\n", FILE_APPEND);
        //file_put_contents('logs/AgentManagerListView.log', date('Y-m-d H:i:s - ').print_r($trimmedRecordModels, true)."\n", FILE_APPEND);
        return $listViewRecordModels;
    }
}
