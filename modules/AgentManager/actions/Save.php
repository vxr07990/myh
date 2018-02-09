<?php

class AgentManager_Save_Action extends Vtiger_Save_Action
{
    
/* 	public function addNewRole($id, $rolename, $parentroles, $depth, $allowassignedrecordsto, $directparentid, $profileid){
        $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO vtiger_role(roleid, rolename, parentrole, depth, allowassignedrecordsto) VALUES (?,?,?,?,?)';
        $db->pquery($sql, array($id, $rolename, $parentroles, $depth, $allowassignedrecordsto));
        $picklist2RoleSQL = "INSERT INTO vtiger_role2picklist SELECT '".$id."',picklistvalueid,picklistid,sortid
        FROM vtiger_role2picklist WHERE roleid = ?";
        $db->pquery($picklist2RoleSQL, array($directparentid));
        $sql = 'INSERT INTO vtiger_role2profile(roleid, profileid) VALUES (?,?)';
        $params = array($id, $profileid);
        $db->pquery($sql, $params);
    }

    public function addRoleToGroup2rs($memberId, $groupId){
        $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2rs`(roleandsubid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId));
    }

    public function addRoleToGroup2role($memberId, $groupId){
        $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2role`(roleid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId));
    } */
    
    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $db = PearDatabase::getInstance();
        
        /* if(empty($recordId)) {

            $sql = 'SELECT * FROM `vtiger_agentmanager` WHERE agency_name = ?';
            $agent = $request->get('agency_name');
            $result = $db->pquery($sql, array($agent));
            $row = $result->fetchRow();

            if(empty($row)){

                $AgencyAdministratorId = $request->get('assigned_user_id');

                $sql = 'SELECT roleid FROM `vtiger_user2role` WHERE userid = ?';
                $result = $db->pquery($sql, array($AgencyAdministratorId));
                $row = $result->fetchRow();

                $AgencyAdministratorRoleId =  $row[0];

                $sql = 'SELECT parentrole FROM `vtiger_role` WHERE roleid = ?';
                $result = $db->pquery($sql, array($AgencyAdministratorRoleId));
                $row = $result->fetchRow();

                $AgencyAdministratorParentRoles =  $row[0];

                $sql = 'SELECT depth FROM `vtiger_role` WHERE roleid = ?';
                $result = $db->pquery($sql, array($AgencyAdministratorRoleId));
                $row = $result->fetchRow();

                $AgencyAdministratorDepth =  $row[0];

                $roleStartDepth = $AgencyAdministratorDepth;

                //crate agent2 role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdAgent2 = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+1;
                $agency_name = $request->get('agency_name');
                $rolename = $agency_name;
                $parentrolesArray = array($roleIdAgent2);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Agent2 Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdAgent2, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

                //crate agent administrator role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdAgentAdmin = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+2;
                $agency_name = $request->get('agency_name');
                $rolename = $agency_name.' Agent Administrator';
                $parentrolesArray = array($roleIdAgent2, $roleIdAgentAdmin);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Agent Administrator Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdAgentAdmin, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

                //create sales manager role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdSalesManager = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+3;
                $rolename = $agency_name.' Sales Manager';
                $parentrolesArray = array($roleIdAgent2, $roleIdAgentAdmin, $roleIdSalesManager);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Sales Manager Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdSalesManager, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

                //create agency user role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdAgencyUser = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+4;
                $rolename = $agency_name.' Agency User';
                $parentrolesArray = array($roleIdAgent2, $roleIdAgentAdmin, $roleIdSalesManager, $roleIdAgencyUser);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Agency User Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdAgencyUser, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

                //create sales person role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdSalesPerson = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+5;
                $rolename = $agency_name.' Sales Person';
                $parentrolesArray = array($roleIdAgent2, $roleIdAgentAdmin, $roleIdSalesManager, $roleIdAgencyUser, $roleIdSalesPerson);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Sales Person Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdSalesPerson, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

                //create read only user role
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdReadOnlyUser = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+5;
                $rolename = $agency_name.' Read-only User';
                $parentrolesArray = array($roleIdAgent2, $roleIdAgentAdmin, $roleIdSalesManager, $roleIdAgencyUser, $roleIdReadOnlyUser);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
                $profileid=7;
                $this::addNewRole($roleIdReadOnlyUser, $rolename, $parentroles, $depth, 0, $directparentrole, $profileid);

                //create group
                $description = '';
                $groupId = $db->getUniqueId('vtiger_users');
                $groupname = $request->get('agency_name');
                $sql = 'INSERT INTO `vtiger_groups`(groupid, groupname, description) VALUES (?,?,?)';
                $db->pquery($sql, array($groupId, $groupname, $description));
                $this::addRoleToGroup2rs($roleIdSalesManager, $groupId);
                $this::addRoleToGroup2role($roleIdAgentAdmin, $groupId);
                //end create group

                //add agency parent agency admin to user2agency

                $sql = "SELECT id FROM `vtiger_crmentity_seq`";
                $result = $db->pquery($sql, array());
                $row = $result->fetchRow();

                $expectedId = $row[0];

                $expectedId++;

                $sql = "INSERT INTO `vtiger_user2agency` VALUES (?,?)";
                $db->pquery($sql, array($AgencyAdministratorId, $expectedId));

                $sql = "INSERT INTO `vtiger_users2group` VALUES (?,?)";
                $db->pquery($sql, array($groupId, $AgencyAdministratorId));

                //add vanline user to agency group

                //file_put_contents("logs.devLog.log", "\n PARENT ROLES: ".$AgencyAdministratorParentRoles, FILE_APPEND);

                $explodedRoles = explode("::", $AgencyAdministratorParentRoles);

                $vanlineUserRole =  $explodedRoles[3];

                $sql = "SELECT userid FROM `vtiger_user2role` WHERE roleid = ?";
                $result = $db->pquery($sql, array($vanlineUserRole));
                $row = $result->fetchRow();
                $vanlineUserId = $row[0];

                $sql = "INSERT INTO `vtiger_user2agency` VALUES (?,?)";
                $db->pquery($sql, array($vanlineUserId, $expectedId));

                $sql = "INSERT INTO `vtiger_users2group` VALUES (?,?)";
                $db->pquery($sql, array($groupId, $vanlineUserId));

                //create default agency settings
                $sql = 'INSERT INTO `vtiger_agencysettings` VALUES (?,?,?,?,?,?,?,?,?,?)';
                $db->pquery($sql, [$expectedId, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
            }
        }

        else{
            //update group name to match agency name
            $sql = "SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid = ?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();

            $oldAgencyName = $row[0];

            $agent = $request->get('agency_name');

            $sql = "UPDATE `vtiger_groups` SET groupname = ? WHERE groupname = ?";
            $db->pquery($sql, array($agent, $oldAgencyName));

        } */
        
        parent::process($request);
        
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            //file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ')."Preparing to call saveChecklist\n", FILE_APPEND);
            $vehicleLookupModel::saveChecklist($request);
        }
        
        if (isset($expectedId)) {
        }
        /* if(isset($expectedId)) {
            $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` ORDER BY agentmanagerid DESC LIMIT 1";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            $recordId = $row[0];

            if($expectedId != $recordId) {
                return;
            }

            $vanlineId = $request->get('vanline_id');

            $sql = "SELECT tariffid FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND apply_to_all_agents=1";
            $result = $db->pquery($sql, array($vanlineId));

            while($row =& $result->fetchRow()) {
                $sql = "INSERT INTO `vtiger_tariff2agent` (agentid, tariffid) VALUES (?,?)";
                $result = $db->pquery($sql, array($recordId, $row[0]));
            }
        } */
    }
}
