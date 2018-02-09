<?php
class OPList_GetOpListTypes_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    public function process(Vtiger_Request $request)
    {
        $db            = PearDatabase::getInstance();
        $smowner = $request->get('smowner');
        $recordId = $request->get('record') ?: 0;

        $availableTypes = [];
        $sql = "SELECT move_type FROM `vtiger_move_type` WHERE presence = 1";
        $result = $db->pquery($sql, []);
        //pulls all active move types from DB
        while ($row =& $result->fetchRow()) {
            $availableTypes[$row['move_type']] = vtranslate($row['move_type'], 'Opportunities');
        }

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $agentList = [$smowner];
        $agentList = array_merge($agentList, array_keys($currentUser->getAccessibleVanlinesForUser()));
        $params = [];
        foreach($agentList as $id) {
            $params[] = $id;
        }
        $params[] = $recordId;

        $sql = 'SELECT op_move_type FROM `vtiger_oplist` JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid=`vtiger_crmentity`.crmid
				WHERE `vtiger_crmentity`.agentid IN (' . generateQuestionMarks($agentList) . ') AND `vtiger_crmentity`.crmid != ? AND `vtiger_crmentity`.deleted = 0';

        $result        = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            foreach (explode(' |##| ', $row['op_move_type']) as $usedValue) {
                unset($availableTypes[$usedValue]);
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($availableTypes);
        $response->emit();
    }

    public function getPermissionLevel()
    {
        $db = PearDatabase::getInstance();
        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $sql = "SELECT * FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid=`vtiger_role`.roleid WHERE userid=?";
        $result = $db->pquery($sql, [$currentUserId]);
        $row = $result->fetchRow();
        $role = $row['rolename'];
        $depth = $row['depth'];

        if ($currentUserId == 1) {
            //it's our admin account
            return ["PermissionLevel"=>"IGCAdmin", "Role"=>$role, "Depth"=>$depth, "Agents"=>$this->getAllAgents()];
        }
        if ($userModel->isAdminUser()) {
            //it's an admin account that isn't ours
            return ["PermissionLevel"=>"SysAdmin", "Role"=>$role, "Depth"=>$depth, "Agents"=>$this->getAllAgents()];
        }

        if ($depth <= 3) {
            //it's a vanline user
            $agents = $userModel->getVanlineUserAccessibleAgents();
            $vanlines = $userModel->getAccessibleVanlinesForUser();
            if ($depth <= 2) {
                $permissionLevel = 'ParentVanline';
            } else {
                $permissionLevel = 'Vanline';
            }
            if (count($vanlines) > 0) {
                return ["PermissionLevel"=>$permissionLevel,"Vanlines"=>$vanlines, "Agents"=>$agents, "Role"=>$role, "Depth"=>$depth];
            }
        } else {
            //it's an agency user
            $agents = explode(' |##| ', $userModel->get('agent_ids'));
            if (count($agents) > 0) {
                return ["PermissionLevel"=>"Agent","Agents"=>$agents, "Role"=>$role, "Depth"=>$depth];
            }
        }

        //fail gracefully (should never hit this)
        return ["PermissionLevel"=>"Agent", "Role"=>$role, "Depth"=>$depth];

        /*$vanlines = [];
        $sql = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
        $result = $db->pquery($sql, array($currentUserId));
        while($row =& $result->fetchRow()) {
            if($row['is_parent'] == 1) {
                return ["PermissionLevel"=>"ParentVanline","for"=>$row['vanlineid'], "Role"=>$role, "Depth"=>$depth];
            } else {
                $vanlines[] = $row['vanlineid'];
            }
        }
        if(count($vanlines) > 0){
            return ["PermissionLevel"=>"Vanline","for"=>$vanlines, "Role"=>$role, "Depth"=>$depth];
        }
        $agents = [];
        $sql = "SELECT `vtiger_agentmanager`.agentmanagerid FROM `vtiger_user2agency` JOIN `vtiger_agentmanager`
                ON `vtiger_user2agency`.agency_code=`vtiger_agentmanager`.agentmanagerid WHERE userid=?";
        $result = $db->pquery($sql, array($currentUserId));
        while($row =& $result->fetchRow()) {
            $agents[] = $row['agentmanagerid'];
        }
        if(count($agents) > 0){
            return ["PermissionLevel"=>"Agent","for"=>$agents, "Role"=>$role, "Depth"=>$depth];
        }*/
    }

    public function getAllAgents()
    {
        $db = PearDatabase::getInstance();
        $agents = [];
        $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager`";
        $result = $db->pquery($sql);
        while ($row =& $result->fetchRow()) {
            $agents[] = $row['agentmanagerid'];
        }
        return $agents;
    }
}
