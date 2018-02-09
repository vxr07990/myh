<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Record_Model extends Vtiger_Record_Model
{
    protected $memberVanlines = false;
    protected $memberAgents = false;

    public function hasCustomSMTP()
    {
        $db          = PearDatabase::getInstance();
        $sql         = "SELECT user_server FROM `vtiger_users` WHERE id=?";
        $params[]    = $this->getId();
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();

        return ($row[0] == "") ? false : true;

    }

    //Check if it's HQ Version for the branding
    public function getMoveHQVersion()
    {
        return getenv('IGC_MOVEHQ');
    }

    //Check the DB version, not used yet but could be helpful in the future
    public function getDBVersion()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT db_version FROM `database_version`";
        $result = $db->pquery($sql, []);
        $row    = $result->fetchRow();

        return $row[0];
    }

    public function getParentAgents()
    {
        $db          = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');
        $agents      = [1];
        $vanlineids = array();

        foreach(VanlineManager_Module_Model::getAllRecords() as $vline) {
          $vanlineids[] = $vline->get('id');
        }

        foreach(explode('|##|',$currentUser->agent_ids) as $id) {
            $id = trim($id);
            $agents[] = $id;
            if(in_array($id,$vanlineids)) {
              continue;
            }
            foreach(AgentManager_Record_Model::getInstanceById($id,'AgentManager')->getCoordinators() as $coordinator) {
                $agents[] = $coordinator['id'];
            }
        }
        return $agents;

    }

    /**
     * Gets the value of the key . First it will check whether specified key is a property if not it
     *  will get from normal data attribure from base class
     *
     * @param  <string> $key - property or key name
     *
     * @return <object>
     */
    public function get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return parent::get($key);
    }

    public function getMultiAgentValues()
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        /*
        $db = PearDatabase::getInstance();

        $userId = $this->getId();

        $sql = "SELECT `vtiger_users2vanline`.vanlineid FROM `vtiger_users2vanline` JOIN `vtiger_crmentity` ON `vtiger_users2vanline`.vanlineid = `vtiger_crmentity`.crmid WHERE `vtiger_users2vanline`.userid = ? AND `vtiger_crmentity`.deleted = 0";
        $result = $db->pquery($sql, array($userId));
        $row = $result->fetchRow();
        $vanlineId = $row[0];
        //file_put_contents('logs/devLog.log', "\n \$vanlineId : ".print_r($vanlineId,true), FILE_APPEND);
        $sql = "SELECT agency_name FROM `vtiger_agentmanager` JOIN `vtiger_crmentity` ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.crmid WHERE vanline_id = ? AND `vtiger_crmentity`.deleted = 0";
        $result = $db->pquery($sql, array($vanlineId));
        $row = $result->fetchRow();
        $agentNames = array();
        While($row != null){
        $agentNames[] = $row[0];
        $row = $result->fetchRow();}
        //file_put_contents('logs/devLog.log', "\n \$agentNames : ".print_r($agentNames,true), FILE_APPEND);
        return $agentNames; */
    }

    public function getOIEnabled()
    {
        $db          = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');
        $sql         = "SELECT oi_enabled FROM `vtiger_users` WHERE id=?";
        $params[]    = $this->getId();
        $result      = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'oi_enabled');
    }

    /**
     * Sets the value of the key . First it will check whether specified key is a property if not it
     * will set from normal set from base class
     *
     * @param <string> $key - property or key name
     * @param <string> $value
     */
    public function set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
        parent::set($key, $value);

        return $this;
    }

    /**
     * Function to get the Detail View url for the record
     * @return <String> - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getDetailViewName().'&record='.$this->getId();
    }

    /**
     * Function to get the Detail View url for the Preferences page
     * @return <String> - Record Detail View Url
     */
    public function getPreferenceDetailViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view=PreferenceDetail&record='.$this->getId();
    }

    /**
     * Function to get the url for the Profile page
     * @return <String> - Profile Url
     */
    public function getProfileUrl()
    {
        $module = $this->getModule();

        return 'index.php?module=Users&view=ChangePassword&mode=Profile';
    }

    /**
     * Function to get the Edit View url for the record
     * @return <String> - Record Edit View Url
     */
    public function getEditViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getEditViewName().'&record='.$this->getId();
    }

    /**
     * Function to get the Edit View url for the Preferences page
     * @return <String> - Record Detail View Url
     */
    public function getPreferenceEditViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view=PreferenceEdit&record='.$this->getId();
    }

    /**
     * Function to get the Delete Action url for the record
     * @return <String> - Record Delete Action Url
     */
    public function getDeleteUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getDeleteActionName().'User&record='.$this->getId();
    }

    /**
     * Function to check whether the user is an Admin user
     * @return <Boolean> true/false
     */
    public function isAdminUser()
    {
        $adminStatus = $this->get('is_admin');
        if ($adminStatus == 'on') {
            return true;
        }

        return false;
    }

    /**
     * Function to get the module name
     * @return <String> Module Name
     */
    public function getModuleName()
    {
        $module = $this->getModule();
        if ($module) {
            return parent::getModuleName();
        }

        //get from the class propety module_name
        return $this->get('module_name');
    }

    /**
     * Function to save the current Record Model
     */
    public function save()
    {
        parent::save();
        $this->saveTagCloud();
    }

    /**
     * Function to get all the Home Page components list
     * @return <Array> List of the Home Page components
     */
    public function getHomePageComponents()
    {
        $entity             = $this->getEntity();
        $homePageComponents = $entity->getHomeStuffOrder($this->getId());

        return $homePageComponents;
    }

    /**
     * Static Function to get the instance of the User Record model for the current user
     * @return Users_Record_Model instance
     */
    protected static $currentUserModels = [];

    public static function getCurrentUserModel()
    {
        //TODO : Remove the global dependency
        $currentUser = vglobal('current_user');
        if (!empty($currentUser)) {
            // Optimization to avoid object creation every-time
            // Caching is per-id as current_user can get swapped at runtime (ex. workflow)
            $currentUserModel = null;
            if (isset(self::$currentUserModels[$currentUser->id])) {
                $currentUserModel = self::$currentUserModels[$currentUser->id];
                if ($currentUser->column_fields['modifiedtime'] != $currentUserModel->get('modifiedtime')) {
                    $currentUserModel = null;
                }
            }
            if (!$currentUserModel) {
                $currentUserModel                          = self::getInstanceFromUserObject($currentUser);
                self::$currentUserModels[$currentUser->id] = $currentUserModel;
            }

            return $currentUserModel;
        }

        return new self();
    }

    /**
     * Static Function to get the instance of the User Record model from the given Users object
     * @return Users_Record_Model instance
     */
    public static function getInstanceFromUserObject($userObject)
    {
        $objectProperties = get_object_vars($userObject);
        $userModel        = new self();
        foreach ($objectProperties as $properName => $propertyValue) {
            $userModel->$properName = $propertyValue;
        }

        return $userModel->setData($userObject->column_fields)->setModule('Users')->setEntity($userObject);
    }

    /**
     * Static Function to get the instance of all the User Record models
     * @return <Array> - List of Users_Record_Model instances
     */
    public static function getAll($onlyActive = true)
    {
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT id FROM vtiger_users';
        $params = [];
        if ($onlyActive) {
            $sql .= ' WHERE status = ?';
            $params[] = 'Active';
        }
        $result    = $db->pquery($sql, $params);
        $noOfUsers = $db->num_rows($result);
        $users     = [];
        if ($noOfUsers > 0) {
            $focus = new Users();
            for ($i = 0; $i < $noOfUsers; ++$i) {
                $userId    = $db->query_result($result, $i, 'id');
                $focus->id = $userId;
                $focus->retrieve_entity_info($userId, 'Users');
                $userModel                  = self::getInstanceFromUserObject($focus);
                $users[$userModel->getId()] = $userModel;
            }
        }

        return $users;
    }

    /**
     * Function returns the Subordinate users
     * @return <Array>
     */
    public function getSubordinateUsers()
    {
        $privilegesModel = $this->get('privileges');
        if (empty($privilegesModel)) {
            $privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
            $this->set('privileges', $privilegesModel);
        }
        $subordinateUsers     = [];
        $subordinateRoleUsers = $privilegesModel->get('subordinate_roles_users');
        if ($subordinateRoleUsers) {
            foreach ($subordinateRoleUsers as $role => $users) {
                foreach ($users as $user) {
                    $subordinateUsers[$user] = $privilegesModel->get('first_name').' '.$privilegesModel->get('last_name');
                }
            }
        }

        return $subordinateUsers;
    }

    /**
     * Function returns the Users Parent Role
     * @return <String>
     */
    public function getParentRoleSequence()
    {
        $privilegesModel = $this->get('privileges');
        if (empty($privilegesModel)) {
            $privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
            if (!$privilegesModel) {
                return;
            }
            $this->set('privileges', $privilegesModel);
        }

        return $privilegesModel->get('parent_role_seq');
    }

    /**
     * Retrieves an object representation of a "Role" row from the DB.
     * @return stdClass
     */
    private function _getRoleObject()
    {
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT * FROM vtiger_role WHERE roleid = ?';
        $params = [$this->getRole()];
        $result = $db->pquery($sql, $params);
        if ($result->numRows() === 0) {
            return null;
        }
        $row = $result->fetchRow();

        return (object) [
            'id'                     => $row['roleid'],
            'name'                   => $row['rolename'],
            'parentRole'             => $row['parentrole'],
            'depth'                  => $row['depth'],
            'allowAssignedRecordsTo' => $row['allowassignedrecordsto'],
        ];
    }

    /**
     * @param string $roleId
     *
     * @return int|null
     */
    private static function _getGroupIdFromGroup2Role($roleId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT groupid FROM vtiger_group2role WHERE roleid = ?';
        $params = [$roleId];
        $result = $db->pquery($sql, $params);
        if ($result->numRows() === 0) {
            return null;
        }
        $row = $result->fetchRow();

        return $row['groupid'];
    }

    /**
     * @param string $roleId
     *
     * @return int|null
     */
    private static function _getGroupIdFromGroup2Rs($roleId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT groupid FROM vtiger_group2rs WHERE roleandsubid = ?';
        $params = [$roleId];
        $result = $db->pquery($sql, $params);
        if ($result->numRows() === 0) {
            return null;
        }
        $row = $result->fetchRow();

        return $row['groupid'];
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        $userId = $this->getId();
        $role   = $this->_getRoleObject();
        if (!$role) {
            throw new Exception(
                sprintf('Error getting the role object in `%s`.', __METHOD__)
            );
        }
        $groupId = self::_getGroupIdFromGroup2Role($role->id);
        if (!$groupId) {
            $groupId = self::_getGroupIdFromGroup2Rs($role->id);
        }

        return $groupId;
    }

    /**
     * Function returns the current user's role ID.
     * @return string e.g. "H2"
     */
    public function getRole()
    {
        $privilegesModel = $this->get('privileges');
        if (empty($privilegesModel)) {
            $privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
            if (!$privilegesModel) {
                return;
            }
            $this->set('privileges', $privilegesModel);
        }

        return $privilegesModel->get('roleid');
    }

    /**
     * Function returns List of Accessible Users for a Module
     *
     * @param  <String> $module
     *
     * @return <Array of Users_Record_Model>
     */
    public function getAccessibleUsersForModule($module)
    {
        $currentUser          = Users_Record_Model::getCurrentUserModel();
        $curentUserPrivileges = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if ($currentUser->isAdminUser() || $curentUserPrivileges->hasGlobalWritePermission()) {
            $users = $this->getAccessibleUsers("", $module);
        } else {
            $sharingAccessModel = Settings_SharingAccess_Module_Model::getInstance($module);
            if ($sharingAccessModel->isPrivate()) {
                $users = $this->getAccessibleUsers('private', $module);
            } else {
                $users = $this->getAccessibleUsers("", $module);
            }
        }

        return $users;
    }

    /**
     * Function returns List of Accessible Users for a Module
     *
     * @param  <String> $module
     *
     * @return <Array of Users_Record_Model>
     */
    public function getAccessibleGroupForModule($module)
    {
        $currentUser          = Users_Record_Model::getCurrentUserModel();
        $curentUserPrivileges = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if ($currentUser->isAdminUser() || $curentUserPrivileges->hasGlobalWritePermission()) {
            $groups = $this->getAccessibleGroups("", $module);
        } else {
            $sharingAccessModel = Settings_SharingAccess_Module_Model::getInstance($module);
            if ($sharingAccessModel->isPrivate()) {
                $groups = $this->getAccessibleGroups('private', $module);
            } else {
                $groups = $this->getAccessibleGroups("", $module);
            }
        }

        return $groups;
    }

    /**
     * Function to get Images Data
     * @return <Array> list of Image names and paths
     */
    public function getImageDetails()
    {
        $recordId         = $this->getId();
        $db               = PearDatabase::getInstance();
        $imageDetails     = [];
        if ($recordId && $this->get('status') == 'Active') {
            $query     = "SELECT * FROM vtiger_users WHERE id = ?";
            $result    = $db->pquery($query, [$recordId]);
            $imageId   = $db->query_result($result, 0, 'profile_image_id');
            $imagePath = $db->query_result($result, 0, 'profile_image_path');
            $imageName = $db->query_result($result, 0, 'profile_image_name');
            //decode_html - added to handle UTF-8 characters in file names
            $imageOriginalName = decode_html($imageName);
            $imageDetails[]    = [
                'id'      => $imageId,
                'orgname' => $imageOriginalName,
                'path'    => $imagePath.$imageId,
                'name'    => $imageName,
            ];
        }

        return $imageDetails;
    }

    /**
     * Function to get all the accessible users
     * @return <Array>
     */
    public function getAccessibleUsers($private = "", $module = false)
    {
        $currentUserRoleModel = Settings_Roles_Record_Model::getInstanceById($this->getRole());
        if ($currentUserRoleModel) {
            $accessibleUser       = Vtiger_Cache::get('vtiger-'.$this->getRole().'-'.$currentUserRoleModel->get('allowassignedrecordsto'), 'accessibleusers');
            //$userModel = Users_Record_Model::getCurrentUserModel();
            //$accessibleUser = $userModel::getUserAgencyUsers($module);
            if (empty($accessibleUser)) {
                if ($currentUserRoleModel->get('allowassignedrecordsto') === '1' || $private == 'Public') {
                    $accessibleUser = get_user_array(false, "ACTIVE", "", $private, $module);
                } elseif ($currentUserRoleModel->get('allowassignedrecordsto') === '2') {
                    $accessibleUser = $this->getSameLevelUsersWithSubordinates();
                } elseif ($currentUserRoleModel->get('allowassignedrecordsto') === '3') {
                    $accessibleUser = $this->getRoleBasedSubordinateUsers();
                }
                $accessibleUser = filterUserAccessibleUsers($accessibleUser);
                Vtiger_Cache::set('vtiger-'.$this->getRole().'-'.$currentUserRoleModel->get('allowassignedrecordsto'), 'accessibleusers', $accessibleUser);
            }

            return $accessibleUser;
        }
    }

    /**
     * Function to get same level and subordinates Users
     * @return <array> Users
     */
    public function getSameLevelUsersWithSubordinates()
    {
        $currentUserRoleModel = Settings_Roles_Record_Model::getInstanceById($this->getRole());
        $sameLevelRoles       = $currentUserRoleModel->getSameLevelRoles();
        $sameLevelUsers       = $this->getAllUsersOnRoles($sameLevelRoles);
        $subordinateUsers     = $this->getRoleBasedSubordinateUsers();
        foreach ($subordinateUsers as $userId => $userName) {
            $sameLevelUsers[$userId] = $userName;
        }

        return $sameLevelUsers;
    }

    /**
     * Function to get subordinates Users
     * @return <array> Users
     */
    public function getRoleBasedSubordinateUsers()
    {
        $currentUserRoleModel = Settings_Roles_Record_Model::getInstanceById($this->getRole());
        $childernRoles        = $currentUserRoleModel->getAllChildren();
        $users                = $this->getAllUsersOnRoles($childernRoles);
        $currentUserDetail    = [$this->getId() => $this->get('first_name').' '.$this->get('last_name')];
        $users                = $currentUserDetail + $users;

        return $users;
    }

    /**
     * Function to get the users based on Roles
     *
     * @param type $roles
     *
     * @return <array>
     */
    public function getAllUsersOnRoles($roles)
    {
        $db      = PearDatabase::getInstance();
        $roleIds = [];
        foreach ($roles as $key => $role) {
            $roleIds[] = $role->getId();
        }
        if (empty($roleIds)) {
            return [];
        }
        $sql       = 'SELECT userid FROM vtiger_user2role WHERE roleid IN ('.generateQuestionMarks($roleIds).')';
        $result    = $db->pquery($sql, $roleIds);
        $noOfUsers = $db->num_rows($result);
        $userIds   = [];
        $subUsers  = [];
        if ($noOfUsers > 0) {
            for ($i = 0; $i < $noOfUsers; ++$i) {
                $userIds[] = $db->query_result($result, $i, 'userid');
            }
            $query     = 'SELECT id, first_name, last_name FROM vtiger_users WHERE status = ? AND id IN ('.generateQuestionMarks($userIds).')';
            $result    = $db->pquery($query, ['ACTIVE', $userIds]);
            $noOfUsers = $db->num_rows($result);
            for ($j = 0; $j < $noOfUsers; ++$j) {
                $userId            = $db->query_result($result, $j, 'id');
                $firstName         = $db->query_result($result, $j, 'first_name');
                $lastName          = $db->query_result($result, $j, 'last_name');
                $subUsers[$userId] = $firstName.' '.$lastName;
            }
        }

        return $subUsers;
    }
    public function getAccessibleSalesPeople()
    {
        $userRecord = Users_Record_Model::getCurrentUserModel();
        if ($_REQUEST['view'] == 'List' || $userRecord->isAdminUser()) {
            $salesPeople = [];
            $db = PearDatabase::getInstance();
            $sql = "SELECT id, first_name, last_name FROM `vtiger_users`";
            $result = $db->query($sql);
            while ($row =& $result->fetchRow()) {
                $salesPeople[$row['id']] = $row['first_name'].' '.$row['last_name'];
            }
            return $salesPeople;
        }

        $salesPeople = [];
        $members = getCurrentUserMembers();
        $salesPeople = [];
        $db = &PearDatabase::getInstance();
        $res = $db->pquery('SELECT id,first_name,last_name FROM vtiger_users where id IN('.implode(',', $members).')', []);
        if($res)
        {
            while($row = $res->fetchRow())
            {
                $salesPeople[$row['id']] = $row['first_name'] . ' ' . $row['last_name'];
            }
        }
        // the above replaces the (15-30x) slower correct way of doing this
//        foreach ($members as $member) {
//            $memberUserModel = Users_Record_Model::getInstanceById($member, 'Users');
//            $salesPeople[$member] = $memberUserModel->getDisplayName();
//        }
        return $salesPeople;
    }
    public function limitPicklistRoles($roles, $module, $record)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        //file_put_contents("logs/devLog.log", "\n OG ROLES: ".print_r($roles, true), FILE_APPEND);
        /*
        $reducedRoles = array();

        $db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        //file_put_contents("logs/devLog.log", "\n RECORD: ".$record, FILE_APPEND);

        foreach($roles as $key=>$role){
            if($module == 'VanlineManager'){
                $sql = "SELECT vanline_name FROM `vtiger_vanlinemanager`WHERE vanlinemanagerid = ?";
                $result = $db->pquery($sql, array($record));
                $row = $result->fetchRow();
                $vanlineName = $row[0];

                $sql = "SELECT rolename FROM `vtiger_role`WHERE roleid = ?";
                $result = $db->pquery($sql, array($role));
                $row = $result->fetchRow();
                $roleName = $row[0];

                $acceptable = strpos($roleName, $vanlineName);

                if($acceptable === 0 && $roleName != $vanlineName){
                    $reducedRoles[$key] = $role;
                }
            }
            elseif($module == 'AgentManager'){
                $sql = "SELECT agency_name FROM `vtiger_agentmanager`WHERE agentmanagerid = ?";
                $result = $db->pquery($sql, array($record));
                $row = $result->fetchRow();
                $agentName = $row[0];

                $sql = "SELECT rolename FROM `vtiger_role`WHERE roleid = ?";
                $result = $db->pquery($sql, array($role));
                $row = $result->fetchRow();
                $roleName = $row[0];

                $acceptable = strpos($roleName, $agentName);

                if($acceptable === 0 && $roleName != $agentName){
                    $reducedRoles[$key] = $role;
                }
            }
        }
        //file_put_contents("logs/devLog.log", "\n COMPLETED REDUCED ROLES: ".print_r($reducedRoles, true), FILE_APPEND);
        //return $reducedRoles;*/

        return $roles;
    }

    public function getPicklistFlag($module)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //file_put_contents("logs/devLog.log", "\n MODULE: ".$module, FILE_APPEND);
        //old securities
        /* if($module == 'Cubesheets' || $module == 'Calendar' || $module == 'Events' || $module == 'VanlineManager' || $module == 'Surveys' || $module == 'TariffManager' || $module == 'Agents' || $module == 'Vanlines' || $module == 'Services'){
            return 'user';
        } elseif($module == 'Contracts' || $module == 'OPList'){
            return 'multiGroup';
        } else{
            return 'group';
        } */
    }

    public function getExtraPermission($record)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$owner = false;

        $db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

        if($isAdmin){
            $owner = true;
        }

        $userGroups = array();
        $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
        $result = $db->pquery($sql, array($currentUserId));
        $row = $result->fetchRow();

        while($row != NULL){
            $userGroups[] = $row[0];
            $row = $result->fetchRow();
        }

        $userGroupNames = array();

        foreach($userGroups as $group){
            $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
            $result = $db->pquery($sql, array($group));
            $row = $result->fetchRow();
            $userGroupNames[] = $row[0];
        }

        $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();
        $ownerId = $row[0];

        if($ownerId == $currentUserId){
            $owner = true;
        }

        foreach($userGroups as $userGroup){
            if($ownerId == $userGroup){
                $owner = true;
            }
        }

        $sql = "SELECT crmid FROM `vtiger_crmentityrel` WHERE relcrmid=? AND module='Orders' AND relmodule!='Surveys' AND relmodule!='Cubesheets' AND relmodule!='Calendar' AND relmodule!='Orders'";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();

        if($row[0]){
            $record = $row[0];
        } else{
            $sql = "SELECT crmid FROM `vtiger_crmentityrel` WHERE relcrmid=? AND module='Opportunities' AND relmodule!='Surveys'  AND relmodule!='Cubesheets' AND relmodule!='Calendar'";
            $result = $db->pquery($sql, array($record));
            $row = $result->fetchRow();
            if($row[0]){
                $record = $row[0];
            }
        }

        $sql = "SELECT orders_id, potentialid FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();

        //file_put_contents("logs/devLog.log", "\n RECORD: ".$record, FILE_APPEND);
        //file_put_contents("logs/devLog.log", "\n ROW: ".print_r($row, true), FILE_APPEND);

        if((!empty($row[0]) && empty($row[1])) ||(!empty($row[0]) && !empty($row[1]))){
            $record = $row[0];
        }
        elseif(empty($row[0]) && !empty($row[1])){
            $record = $row[1];
        }

        //file_put_contents("logs/devLog.log", "\n getExtraPermission!!!!!! record: ".$record, FILE_APPEND);

        $participatingAgents = array();
        $participatingAgentNames = array();

        $documentsOppsAndOrders = array();

        $sql = "SELECT crmid FROM `vtiger_senotesrel` WHERE notesid=?";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();

        while($row != null){
            $documentsOppsAndOrders[] = $row[0];
            $row = $result->fetchRow();
        }

        if(count($documentsOppsAndOrders) > 0){
            foreach($documentsOppsAndOrders as $documentParent){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($documentParent));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
                $sql2 = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($documentParent));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }
        }

        /*$calendarOppsAndOrders = array();

        $sql = "SELECT crmid FROM `vtiger_seactivityrel` WHERE activityid=?";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();

        while($row != null){
            $calendarOppsAndOrders[] = $row[0];
            $row = $result->fetchRow();
        }

        if(count($calendarOppsAndOrders) > 0){
            foreach($calendarOppsAndOrders as $calendarParent){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($calendarParent));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
                $sql2 = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($calendarParent));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }
        }

        $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();

        //file_put_contents("logs/devLog.log", "\n setype: ".$row[0], FILE_APPEND);

        if($row[0] == 'Contacts'){

            $contactOpps = array();
            $contactOrders = array();

            $sql2 = "SELECT potentialid FROM `vtiger_potential` WHERE contact_id=?";
            $result2 = $db->pquery($sql2, array($record));
            $row2 = $result2->fetchRow();
            while($row2 != null){
                $contactOpps[] = $row2[0];
                $row2 = $result2->fetchRow();
            }

            foreach($contactOpps as $contactOpp){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($contactOpp));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }

            $sql2 = "SELECT ordersid FROM `vtiger_orders` WHERE orders_contacts=?";
            $result2 = $db->pquery($sql2, array($record));
            $row2 = $result2->fetchRow();
            while($row2 != null){
                $contactOrders[] = $row2[0];
                $row2 = $result2->fetchRow();
            }

            foreach($contactOrders as $contactOrder){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($contactOrder));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }

        } elseif($row[0] == 'Accounts'){

            $accountOpps = array();
            $accountOrders = array();

            $sql2 = "SELECT potentialid FROM `vtiger_potential` WHERE related_to=?";
            $result2 = $db->pquery($sql2, array($record));
            $row2 = $result2->fetchRow();
            while($row2 != null){
                $accountOpps[] = $row2[0];
                $row2 = $result2->fetchRow();
            }

            foreach($accountOpps as $accountOpp){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($accountOpp));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }

            $sql2 = "SELECT ordersid FROM `vtiger_orders` WHERE orders_account=?";
            $result2 = $db->pquery($sql2, array($record));
            $row2 = $result2->fetchRow();
            while($row2 != null){
                $accountOrders[] = $row2[0];
                $row2 = $result2->fetchRow();
            }

            foreach($accountOrders as $accountOrder){
                $sql2 = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions=0";
                $result2 = $db->pquery($sql2, array($accountOrder));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $participatingAgents[] = array($row2[0], $row2[1]);
                    $row2 = $result2->fetchRow();
                }
            }
        }

        $sql = "SELECT agentid FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions=0";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();
        while($row != null){
            $participatingAgents[] = $row[0];
            $row = $result->fetchRow();
        }
        $sql = "SELECT agentid FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions=0";
        $result = $db->pquery($sql, array($record));
        $row = $result->fetchRow();
        while($row != null){
            $participatingAgents[] = $row[0];
            $row = $result->fetchRow();
        }

        foreach($participatingAgents as $participatingAgent){
            $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
            $result = $db->pquery($sql, array($participatingAgent));
            $row = $result->fetchRow();
            $participatingAgentNames[] = $row[0];
        }

        foreach($participatingAgentNames as $participatingAgentName){
                foreach($userGroupNames as $groupName){
                    if($groupName == $participatingAgentName){
                        $owner = true;
                    }
                }
            }*/
        //file_put_contents('logs/devLog.log', "\n owner: ".$owner, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n record: ".$record, FILE_APPEND);
        //return $owner;
    }

    public function getUsersByAgent($agentId)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();

        $sql = "SELECT DISTINCT userid FROM `vtiger_user2agency` WHERE agency_code=?";
        $result = $db->pquery($sql, array($agentId));
        if(!$result) {
            return array();
        }

        $userArray = array();
        while($row =& $result->fetchRow()) {
            $userArray[] = $row['userid'];
        }

        return $userArray;*/
    }

    public function getUserAgencyUsers($module, $onlySales = false, $name = true)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

        $recordId = $record;

        $agencyUsers = array();

        $sql = "SELECT roleid FROM `vtiger_user2role` WHERE userid = ?";
        $result = $db->pquery($sql, array($currentUserId));
        $row = $result->fetchRow();
        $roleId = $row[0];

        $sql = "SELECT depth FROM `vtiger_role` WHERE roleid = ?";
        $result = $db->pquery($sql, array($roleId));
        $row = $result->fetchRow();
        $depth = $row[0];

        $agentIds = array();

        if($isAdmin){
            if($module == 'VanlineManager'){
                $sql = "SELECT id, first_name, last_name FROM `vtiger_users` WHERE deleted=0 AND is_admin = 'on'";
            } else{ $sql = "SELECT id, first_name, last_name FROM `vtiger_users` WHERE deleted=0";}
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            while($row != NULL){
                $displayString = '';
                if($row[1]){
                    $displayString .= $row[1]." ";
                }
                $agencyUsers[$row[0]] = $displayString.$row[2];
                $row = $result->fetchRow();
            }
        } elseif($depth == 3){

            $vanlineUsers = array();

            $sql = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid = ?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $vanlineId = $row[0];

            //grab users in same vanline
            $sql = "SELECT userid FROM `vtiger_users2vanline` WHERE vanlineid = ?";
            $result = $db->pquery($sql, array($vanlineId));
            $row = $result->fetchRow();
            $vanlineUsers[] = $row[0];

            //grab users in agents belonging to vanline
            $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id = ?";
            $result = $db->pquery($sql, array($vanlineId));
            $row = $result->fetchRow();
            //file_put_contents("logs/devLog.log", "\n RESULT: ".print_r($result, true), FILE_APPEND);
            while($row != null){
                $sql2 = "SELECT  `vtiger_user2agency`.userid FROM  `vtiger_user2agency` JOIN  `vtiger_users` ON  `vtiger_user2agency`.userid =  `vtiger_users`.id WHERE  `vtiger_user2agency`.agency_code =? AND  `vtiger_users`.deleted =0";
                $result2 = $db->pquery($sql2, array($row[0]));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    $vanlineUsers[] = $row2[0];
                    $row2 = $result2->fetchRow();
                }
                $row = $result->fetchRow();
            }

            //assemble final array of names and ids
            foreach($vanlineUsers as $vanlineUser){
                $sql = "SELECT first_name, last_name FROM `vtiger_users` WHERE id = ?";
                $result = $db->pquery($sql, array($vanlineUser));
                $row = $result->fetchRow();
                $displayString = '';
                if($row[0]){
                    $displayString .= $row[0]." ";
                }
                $agencyUsers[$vanlineUser] = $displayString.$row[1];
            }

        } else{
            //file_put_contents('logs/devLog.log', "\n Here I am!", FILE_APPEND);
            $sql = "SELECT agency_code FROM `vtiger_user2agency` WHERE userid = ?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            while($row != null){
                $agentIds[] = $row[0];
                $row = $result->fetchRow();
            }
            //file_put_contents('logs/devLog.log', "\n agentIds: ".print_r($agentIds, true), FILE_APPEND);
            foreach($agentIds as $agentId){
                $sql2 = "SELECT `vtiger_user2agency`.userid FROM `vtiger_user2agency` JOIN `vtiger_users` ON `vtiger_user2agency`.userid = `vtiger_users`.id WHERE `vtiger_user2agency`.agency_code = ? AND `vtiger_users`.deleted = 0";
                $result2 = $db->pquery($sql2, array($agentId));
                $row2 = $result2->fetchRow();
                while($row2 != null){
                    //$sql = "SELECT depth FROM `vtiger_role` JOIN `vtiger_user2role` ON `vtiger_role`.roleid = `vtiger_user2role`.roleid AND `vtiger_user2role`.userid = ?";
                    //$result = $db->pquery($sql, [$currentUser->getId()]);
                    //$depth = $result->fetchRow()[0];
                    $sqldepth = "SELECT depth FROM `vtiger_role` JOIN `vtiger_user2role` ON `vtiger_role`.roleid = `vtiger_user2role`.roleid AND `vtiger_user2role`.userid = ?";
                    //file_put_contents('logs/devLog.log',"\n BEFORE \n sqldepth : {$sqldepth} \n id : {$row2[0]} \n otherdepth : {$otherdepth}",FILE_APPEND);
                    $resultdepth = $db->pquery($sqldepth, [$row2[0]]);
                    $otherdepth = $resultdepth->fetchRow()[0];
                    //file_put_contents('logs/devLog.log',"\n AFTER \n sqldepth : {$sqldepth} \n id : {$row2[0]} \n otherdepth : {$otherdepth}",FILE_APPEND);
                    $sql3 = "SELECT first_name, last_name FROM `vtiger_users` WHERE id = ?";
                    $result3 = $db->pquery($sql3, array($row2[0]));
                    $row3 = $result3->fetchRow();
                        while($row3 != null){
                            $displayString = '';
                            if($row3[0]){
                                $displayString .= $row3[0]." ";
                            }
                            if($otherdepth > 5) {
                                $agencyUsers[$row2[0]] = $displayString . $row3[1];
                            }
                            $row3 = $result3->fetchRow();
                        }
                    $row2 = $result2->fetchRow();
                }
            }
        }
        //file_put_contents('logs/devLog.log', "\n agencyUsers: ".print_r($agencyUsers, true), FILE_APPEND);
        if($onlySales == true){
            $salesUsers = array();
            foreach($agencyUsers as $key => $name){
                $sql= "SELECT vtiger_role.rolename FROM vtiger_role INNER JOIN vtiger_user2role ON vtiger_role.roleid = vtiger_user2role.roleid INNER JOIN vtiger_users ON vtiger_user2role.userid = vtiger_users.id WHERE vtiger_users.id=?";
                $result = $db->pquery($sql, array($key));
                $row = $result->fetchRow();
                if(strpos($row[0], 'Sales Person') && !array_key_exists($key, $salesUsers)){
                    $salesUsers[$key] = $name;
                }
            }
            return $salesUsers;
            //file_put_contents('logs/devLog.log', "\n SALES PERSON PICKLIST!", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n USER LIST: ".print_r($agencyUsers, true), FILE_APPEND);
        }
        return $agencyUsers;*/
    }

    public function getVanlineGroups()
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $userGroups = array();

        $isAdmin = $userModel->isAdminUser();

        if($isAdmin){
            $sql = "SELECT groupid, groupname FROM `vtiger_groups` WHERE grouptype=?";
            $result = $db->pquery($sql, array(1));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[$row[0]] = $row[1];
                $row = $result->fetchRow();
            }
        } else{
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            while($row =& $result->fetchRow()){
                $sql2 = "SELECT groupname FROM `vtiger_groups` WHERE groupid=? AND grouptype=?";
                $result2 = $db->pquery($sql2, array($row[0], 1));
                $row2 = $result2->fetchRow();
                if(!in_array($row2[0], $userGroups)){
                    $userGroups[$row[0]] = $row2[0];
                }
            }
        }
        return $userGroups;*/
    }

    public function getCurrentUserGroups($record = false, $agentPermissions = false, $allowAdmin = true)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        //file_put_contents('logs/devLog.log', "AP: $agentPermissions", FILE_APPEND);
        /*$db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

        $recordId = $record;

        $userGroups = array();

        $sql = "SELECT roleid FROM `vtiger_user2role` WHERE userid = ?";
        $result = $db->pquery($sql, array($currentUserId));
        $row = $result->fetchRow();
        $roleId = $row[0];

        $sql = "SELECT depth FROM `vtiger_role` WHERE roleid = ?";
        $result = $db->pquery($sql, array($roleId));
        $row = $result->fetchRow();
        $depth = $row[0];


        if($allowAdmin && ($isAdmin || $agentPermissions == 'edit')){
            $sql = "SELECT groupid, groupname FROM `vtiger_groups` WHERE grouptype IS NULL";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[$row[0]] = $row[1];
                $row = $result->fetchRow();
            }
        } elseif($depth == 3){
            $sql = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid = ?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $vanlineId = $row[0];

            $sql = "SELECT agency_name FROM `vtiger_agentmanager` WHERE vanline_id = ?";
            $result = $db->pquery($sql, array($vanlineId));
            $row = $result->fetchRow();
            While($row != null){
            $sql2 = "SELECT groupid FROM `vtiger_groups` WHERE groupname = ?";
            $result2 = $db->pquery($sql2, array($row[0]));
            $row2 = $result2->fetchRow();
            $userGroups[$row2[0]] = $row[0];
            $row = $result->fetchRow();}
        } else{
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            while($row =& $result->fetchRow()){
                $sql2 = "SELECT groupname FROM `vtiger_groups` WHERE groupid=? AND grouptype IS NULL";
                $result2 = $db->pquery($sql2, array($row[0]));
                $row2 = $result2->fetchRow();
                $userGroups[$row[0]] = $row2[0];
            }
        }
        //file_put_contents('logs/devLog.log', "\n USER GROUPS: ".print_r($userGroups, true), FILE_APPEND);
        return $userGroups;*/
    }

    /**
     * Function to get all the accessible groups
     * @return <Array>
     */
    public function getAccessibleGroups($private = "", $module = false)
    {
        //removed in old securities - uncommented
        //TODO:Remove dependence on $_REQUEST for the module name in the below API
        $accessibleGroups = Vtiger_Cache::get('vtiger-'.$private, 'accessiblegroups');
        //$currentUser = Users_Record_Model::getCurrentUserModel();
        //$accessibleGroups = Users_Privileges_Model::getCurrentUserGroups();
        if (!$accessibleGroups) {
            $accessibleGroups = get_group_array(false, "ACTIVE", "", $private, $module);
        }
        $accessibleGroups = filterUserAccessibleGroups($accessibleGroups);
        Vtiger_Cache::set('vtiger-'.$private, 'accessiblegroups', $accessibleGroups);

        return $accessibleGroups;
        //return get_group_array(false, "ACTIVE", "", $private);
        //$userModel = Users_Record_Model::getCurrentUserModel();
        //return $userModel::getCurrentUserGroups();
    }

    /**
     * Function to get privillage model
     * @return $privillage model
     */
    public function getPrivileges()
    {
        $privilegesModel = $this->get('privileges');
        if (empty($privilegesModel)) {
            $privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
            $this->set('privileges', $privilegesModel);
        }

        return $privilegesModel;
    }

    /**
     * Function to get user default activity view
     * @return <String>
     */
    public function getActivityView()
    {
        $activityView = $this->get('activity_view');

        return $activityView;
    }

    /**
     * Function to delete corresponding image
     *
     * @param <type> $imageId
     */
    public function deleteImage($imageId)
    {
        $db          = PearDatabase::getInstance();
        $checkResult = $db->pquery('SELECT smid FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', [$imageId]);
        $smId        = $db->query_result($checkResult, 0, 'smid');
        if ($this->getId() === $smId) {
            $db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', [$imageId]);
            $db->pquery('DELETE FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', [$imageId]);

            return true;
        }

        return false;
    }

    /**
     * Function to get the Day Starts picklist values
     *
     * @param type $name Description
     */
    public static function getDayStartsPicklistValues($stucturedValues)
    {
        $fieldModel             = $stucturedValues['LBL_CALENDAR_SETTINGS'];
        $hour_format            = $fieldModel['hour_format']->getPicklistValues();
        $start_hour             = $fieldModel['start_hour']->getPicklistValues();
        $defaultValues          = ['00:00' => '12:00 AM',
                                   '01:00' => '01:00 AM',
                                   '02:00' => '02:00 AM',
                                   '03:00' => '03:00 AM',
                                   '04:00' => '04:00 AM',
                                   '05:00' => '05:00 AM',
                                   '06:00' => '06:00 AM',
                                   '07:00' => '07:00 AM',
                                   '08:00' => '08:00 AM',
                                   '09:00' => '09:00 AM',
                                   '10:00' => '10:00 AM',
                                   '11:00' => '11:00 AM',
                                   '12:00' => '12:00 PM',
                                   '13:00' => '01:00 PM',
                                   '14:00' => '02:00 PM',
                                   '15:00' => '03:00 PM',
                                   '16:00' => '04:00 PM',
                                   '17:00' => '05:00 PM',
                                   '18:00' => '06:00 PM',
                                   '19:00' => '07:00 PM',
                                   '20:00' => '08:00 PM',
                                   '21:00' => '09:00 PM',
                                   '22:00' => '10:00 PM',
                                   '23:00' => '11:00 PM'];
        $picklistDependencyData = [];
        foreach ($hour_format as $value) {
            if ($value == 24) {
                $picklistDependencyData['hour_format'][$value]['start_hour'] = $start_hour;
            } else {
                $picklistDependencyData['hour_format'][$value]['start_hour'] = $defaultValues;
            }
        }
        if (empty($picklistDependencyData['hour_format']['__DEFAULT__']['start_hour'])) {
            $picklistDependencyData['hour_format']['__DEFAULT__']['start_hour'] = $defaultValues;
        }

        return $picklistDependencyData;
    }

    /**
     * Function returns if tag cloud is enabled or not
     */
    public function getTagCloudStatus()
    {
        $db         = PearDatabase::getInstance();
        $query      = "SELECT visible FROM vtiger_homestuff WHERE userid=? AND stufftype='Tag Cloud'";
        $visibility = $db->query_result($db->pquery($query, [$this->getId()]), 0, 'visible');
        if ($visibility == 0) {
            return true;
        }

        return false;
    }

    /**
     * Function saves tag cloud
     */
    public function saveTagCloud()
    {
        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_homestuff SET visible = ? WHERE userid=? AND stufftype='Tag Cloud'",
                    [$this->get('tagcloud'), $this->getId()]);
    }

    /**
     * Function to get user groups
     *
     * @param type $userId
     *
     * @return <array> - groupId's
     */
    public static function getUserGroups($userId)
    {
        $db       = PearDatabase::getInstance();
        $groupIds = [];
        $query    = "SELECT groupid FROM vtiger_users2group WHERE userid=?";
        $result   = $db->pquery($query, [$userId]);
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $groupId    = $db->query_result($result, $i, 'groupid');
            $groupIds[] = $groupId;
        }

        return $groupIds;
    }

    /**
     * Function returns the users activity reminder in seconds
     * @return string
     */
    /**
     * Function returns the users activity reminder in seconds
     * @return string
     */
    public function getCurrentUserActivityReminderInSeconds()
    {
        $activityReminder          = $this->reminder_interval;
        $activityReminderInSeconds = '';
        if ($activityReminder != 'None') {
            preg_match('/([0-9]+)[\s]([a-zA-Z]+)/', $activityReminder, $matches);
            if ($matches) {
                $number = $matches[1];
                $string = $matches[2];
                if ($string) {
                    switch ($string) {
                        case 'Minute':
                        case 'Minutes':
                            $activityReminderInSeconds = $number * 60;
                            break;
                        case 'Hour':
                            $activityReminderInSeconds = $number * 60 * 60;
                            break;
                        case 'Day':
                            $activityReminderInSeconds = $number * 60 * 60 * 24;
                            break;
                        default:
                            $activityReminderInSeconds = '';
                    }
                }
            }
        }

        return $activityReminderInSeconds;
    }

    /**
     * Function to get the users count
     *
     * @param  <Boolean> $onlyActive - If true it returns count of only acive users else only inactive users
     *
     * @return <Integer> number of users
     */
    public static function getCount($onlyActive = false)
    {
        $db     = PearDatabase::getInstance();
        $query  = 'SELECT 1 FROM vtiger_users ';
        $params = [];
        if ($onlyActive) {
            $query .= ' WHERE status=? ';
            array_push($params, 'active');
        }
        $result     = $db->pquery($query, $params);
        $numOfUsers = $db->num_rows($result);

        return $numOfUsers;
    }

    /**
     * Funtion to get Duplicate Record Url
     * @return <String>
     */
    public function getDuplicateRecordUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getEditViewName().'&record='.$this->getId().'&isDuplicate=true';
    }

    /**
     * Function to get instance of user model by name
     *
     * @param  <String> $userName
     *
     * @return <Users_Record_Model>
     */
    public static function getInstanceByName($userName)
    {
        $db     = PearDatabase::getInstance();
        $result = $db->pquery('SELECT id FROM vtiger_users WHERE user_name = ?', [$userName]);
        if ($db->num_rows($result)) {
            return Users_Record_Model::getInstanceById($db->query_result($result, 0, 'id'), 'Users');
        }

        return false;
    }

    /**
     * Function to delete the current Record Model
     */
    public function delete()
    {
        $this->getModule()->deleteRecord($this);
        return true;
    }

    public function isAccountOwner()
    {
        $db      = PearDatabase::getInstance();
        $query   = 'SELECT is_owner FROM vtiger_users WHERE id = ?';
        $isOwner = $db->query_result($db->pquery($query, [$this->getId()]), 0, 'is_owner');
        if ($isOwner == 1) {
            return true;
        }

        return false;
    }

    public function getActiveAdminUsers()
    {
        $db        = PearDatabase::getInstance();
        $sql       = 'SELECT id FROM vtiger_users WHERE status=? AND is_admin=?';
        $result    = $db->pquery($sql, ['ACTIVE', 'on']);
        $noOfUsers = $db->num_rows($result);
        $users     = [];
        if ($noOfUsers > 0) {
            $focus = new Users();
            for ($i = 0; $i < $noOfUsers; ++$i) {
                $userId    = $db->query_result($result, $i, 'id');
                $focus->id = $userId;
                $focus->retrieve_entity_info($userId, 'Users');
                $userModel                  = self::getInstanceFromUserObject($focus);
                $users[$userModel->getId()] = $userModel;
            }
        }

        return $users;
    }

    public function isFirstTimeLogin($userId)
    {
        $db     = PearDatabase::getInstance();
        $query  = 'SELECT 1 FROM vtiger_crmsetup WHERE userid = ? and setup_status = ?';
        $result = $db->pquery($query, [$userId, 1]);
        if ($db->num_rows($result) == 0) {
            return true;
        }

        return false;
    }

    /**
     * Function to get the user hash
     *
     * @param type $userId
     *
     * @return boolean
     */
    public function getUserHash()
    {
        $db     = PearDatabase::getInstance();
        $query  = 'SELECT user_hash FROM vtiger_users WHERE id = ?';
        $result = $db->pquery($query, [$this->getId()]);
        if ($db->num_rows($result) > 0) {
            return $db->query_result($result, 0, 'user_hash');
        }
    }

    /*
     * Function to delete user permanemtly from CRM and
     * assign all record which are assigned to that user
     * and not transfered to other user to other user
     *
     * @param User Ids of user to be deleted and user
     * to whom records should be assigned
     */
    public function deleteUserPermanently($userId, $newOwnerId)
    {
        $db  = PearDatabase::getInstance();
        $sql = "UPDATE vtiger_crmentity SET smcreatorid=?,smownerid=? WHERE smcreatorid=? AND setype=?";
        $db->pquery($sql, [$newOwnerId, $newOwnerId, $userId, 'ModComments']);
        //update history details in vtiger_modtracker_basic
        $sql = "update vtiger_modtracker_basic set whodid=? where whodid=?";
        $db->pquery($sql, [$newOwnerId, $userId]);
        //update comments details in vtiger_modcomments
        $sql = "update vtiger_modcomments set userid=? where userid=?";
        $db->pquery($sql, [$newOwnerId, $userId]);
        //delete user entries from db
        $sql = "DELETE FROM vtiger_users WHERE id=?";
        $db->pquery($sql, [$userId]);
        $sql = "DELETE FROM vtiger_user2role WHERE userid=?";
        $db->pquery($sql, [$userId]);
    }

    /**
     * Function to get the Display Name for the record
     * @return <String> - Entity Display Name for the record
     */
    public function getDisplayName()
    {
        //@TODO: This may need undone, because the old method returned x number of spaces ( where x = # of entityfields - 1) because the implode
        //return getFullNameFromArray($this->getModuleName(), $this->getData());
        return $this->getDisplayNameById($this->getId());
    }

    /**
     * function to get the display name of a user record by the userid.
     *
     * returns false if there is no display name to return
     *
     * @param int $id <the user id>
     *
     * @return bool|string
     */
    public function getDisplayNameById($id) {
        if (!$id) {
            return false;
        }

        $entityInfo = getEntityFieldNames('Users');
        $fieldsName = $entityInfo['fieldname'];

        if (!is_array($fieldsName)) {
            //@NOTE: returning false because otherwise you could use this to test if an id is a valid user id.
            return false;
        }

        $db = &PearDatabase::getInstance();
        //@TODO: should use these things from the Users class?
        //$table_name = "vtiger_users";
        //$table_index= 'id';
        $stmt = 'SELECT CONCAT_WS(" ", '.implode(',',$fieldsName).') as entityLabel FROM vtiger_users WHERE id=? LIMIT 1';
        $res = $db->pquery($stmt, [$id]);
        if (
            $res &&
            method_exists($res, 'fetchRow')
        ) {
            $row = $res->fetchRow();
            if ($row['entityLabel']) {
                return $row['entityLabel'];
            }
        }
        //Either the mysql failed, or the row doesn't exist.
        return false;
    }

    public function getAccessibleAgents()
    {
        $db     = PearDatabase::getInstance();

        $sql = "SELECT agentmanagerid, agency_name FROM vtiger_agentmanager
							   INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
							   WHERE deleted = 0";

        if ($this->isParentVanLineUser()) {
            $userVanlines = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
            $sql .= " AND vanline_id IN $userVanlines";
        }

        $result = $db->pquery($sql);
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $accessibleAgents[$row['agentmanagerid']] = htmlspecialchars_decode($row['agency_name'], ENT_QUOTES);
            }
        }

        return $accessibleAgents;
    }
    public function AssocArrayMerge($array1, $array2)
    {
        return array_combine(array_merge(array_keys($array1), array_keys($array2)),
                             array_merge(array_values($array1), array_values($array2)));
    }
    public function getPrimaryOwnerForUser($userId = false)
    {
        if ($userId) {
            return getMembersByUser($userId)[0];
        } else {
            return getPermittedAccessible()[0];
        }
    }
    public function getAccessibleOwnersForUser($module,$returnBoth = false, $lookup = false)
    {

        $agents['agents'] = 'agents';
        $vanlines['vanlines'] = 'vanlines';
        $allowBoth = ['Reports', 'PushNotifications']; //Reporrs is not an entity module

//        if ($this->isAdminUser() && in_array($module, $allowBoth)) {
//            //show all the things
//            $agents = $this->AssocArrayMerge($agents, $this->getAccessibleAgentsForUser()?:[]);
//            $vanlines = $this->AssocArrayMerge($vanlines, $this->getAccessibleVanlinesForUser()?:[]);
//            return $this->AssocArrayMerge($agents, $vanlines);
//        } elseif ($this->isVanLineUser() && in_array($module, $allowBoth)) {
//            //show vanline stuff
//            $agents = $this->AssocArrayMerge($agents, $this->getAccessibleAgentsForUser()?:[]);
//            $vanlines = $this->AssocArrayMerge($vanlines, $this->getAccessibleVanlinesForUser()?:[]);
//            //file_put_contents('logs/devLog.log', "\n Agents : ".print_r($agents, true), FILE_APPEND);
//            //file_put_contents('logs/devLog.log', "\n Vanlines : ".print_r($vanlines, true), FILE_APPEND);
//
//            return $this->AssocArrayMerge($agents, $vanlines);
//        } else {
//            //show the agent stuff
//            return $this->AssocArrayMerge($agents, $this->getAccessibleAgentsForUser()?:[]);
//        }

        //OT4482 if the user is vanline show agents ans vanlines in owner fields

        if ( ($returnBoth || in_array($module, $allowBoth) ) && ($this->isAdminUser() || $this->isVanLineUser() || $lookup) ){
            //show all the things
            $agents = $this->AssocArrayMerge($agents, $this->getAccessibleAgentsForUser()?:[]);
            $vanlines = $this->AssocArrayMerge($vanlines, $this->getAccessibleVanlinesForUser()?:[]);
            return $this->AssocArrayMerge($agents, $vanlines);
        } else {
            //show the agent stuff
            $agents = $this->AssocArrayMerge($agents, $this->getAccessibleAgentsForUser()?:[]);
            return $agents;
        }
    }

    public function getBothAccessibleOwnersIdsForUser(){
        $accesibleAgents = $this->getAccessibleOwnersForUser('', true, true);
        unset($accesibleAgents['agents']);
        unset($accesibleAgents['vanlines']);
        return array_keys($accesibleAgents);
    }

    public function getAccessibleAgentsForUser($vanline = null)
    {
        if($this->memberAgents != false) {
            return $this->memberAgents;
        }
        $accessibleAgents = [];
        $db = PearDatabase::getInstance();
        if ($this->isVanLineUser() || $this->isAdminUser()) {
            $sql = 'SELECT agentmanagerid, agency_name, agency_code FROM vtiger_agentmanager
                    INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
                    WHERE deleted = 0';
            if($vanline)
            {
                $sql .= " AND vanline_id = $vanline";
            } else if ($this->get('agent_ids') != '') {
                $userVanlines = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
                $sql .= " AND vanline_id IN $userVanlines";
            }
            $result = $db->pquery($sql, []);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accessibleAgents[$row['agentmanagerid']] = '('.$row['agency_code'].') '.$row['agency_name'];
                }
            }
        } else {
            $sql = "SELECT agentmanagerid, agency_name, agency_code FROM vtiger_agentmanager
                    INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
                    WHERE deleted = 0";

            if ($this->get('agent_ids') != '') {

                $userAgentsExplode = explode(' |##| ', $this->get('agent_ids'));
                $userParentAgents=array();
                foreach ($userAgentsExplode as $key => $userAgentId){
                    $userParentAgents = $this->getAgentParent($userAgentId,$userParentAgents);
                }

                $userAgentsExplode = array_merge($userParentAgents,$userAgentsExplode);

                $userAgents = '('.implode(',', $userAgentsExplode).')';

                $sql .= " AND agentmanagerid IN $userAgents";
            }
            if($vanline) {
                $sql .= " AND vanline_id = $vanline";
            }

            $result = $db->pquery($sql, []);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accessibleAgents[$row['agentmanagerid']] = '('.$row['agency_code'].') '.$row['agency_name'];
                }
            }

            //Put in the first place the default agent for the user.
            if ($this->get('agent_ids') != '') {
                $accessibleAgents1= [];
                $defaultAgent     = explode(' |##| ', $this->get('agent_ids'))[0];
                $defaultAgentName = $accessibleAgents[$defaultAgent];
                unset($accessibleAgents[$defaultAgent]);
                $accessibleAgents1[$defaultAgent] = $defaultAgentName;
                //so I don't know what's wrong that I got here, but you can't add if they aren't arrays...
                //@TODO: if it's not set it means we didn't get the default Agent... this should probably
                //@TODO: through an error to the user of some sort.
                $accessibleAgents                 = $accessibleAgents1 + $accessibleAgents;
            }
        }
        $this->memberAgents = $accessibleAgents;
        return $accessibleAgents;
    }

    function getAgentParent($id,$agentParentIds){
        return $agentParentIds;
        //Shorting this function to remove the parent / child relationship per OT4011
//        global $adb;
//
//        if (!empty($id)){
//            $select = "SELECT `agentmanagerid`,`cf_agent_manager_parent_id`
//                   FROM `vtiger_agentmanager`
//                   INNER JOIN `vtiger_crmentity`
//                   ON `vtiger_agentmanager`.`agentmanagerid` = `vtiger_crmentity`.`crmid`
//                   WHERE `deleted` = 0
//                   AND `agentmanagerid` = ?";
//
//            $result = $adb->pquery($select, array($id));
//            if ($adb->num_rows($result)>0){
//                $infoParent = $adb->fetch_array($result);
//                $cf_agent_manager_parent_id = $infoParent['cf_agent_manager_parent_id'];
//                if ($cf_agent_manager_parent_id && !in_array($cf_agent_manager_parent_id,$agentParentIds)) {
//                    $agentParentIds[] = $cf_agent_manager_parent_id;
//                    $agentParentIds = $this->getAgentParent($cf_agent_manager_parent_id,$agentParentIds);
//                }
//            }
//        }
//        return $agentParentIds;
    }

    public function getAccessibleVanlinesForUser()
    {
        if($this->memberVanlines != false) {
            return $this->memberVanlines;
        }
        $db = PearDatabase::getInstance();
        if ($this->isVanLineUser() || $this->isAdminUser()) {
            $sql = 'SELECT vanlinemanagerid, vanline_name
                    FROM vtiger_vanlinemanager
                    INNER JOIN vtiger_crmentity ON vtiger_vanlinemanager.vanlinemanagerid = vtiger_crmentity.crmid
                    WHERE deleted = 0';
            if ($this->get('agent_ids') != '') {
                $userVanlines = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
                $sql .= " AND vanlinemanagerid IN $userVanlines";
            }
            $result = $db->pquery($sql, []);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accessibleVanlines[$row['vanlinemanagerid']] = $row['vanline_name'];
                }
            } else {

		//OT17730 - Conrado VGS - If the admin user/van line has a list of agents instead of a vanline_id this function returns null. We need to search by agentmanagerid just in case.

		$sql = "SELECT vanlinemanagerid, vanline_name
                    FROM vtiger_vanlinemanager
                    JOIN vtiger_agentmanager ON vtiger_vanlinemanager.vanlinemanagerid = vtiger_agentmanager.vanline_id
                    INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
                    WHERE deleted = 0";
		if ($this->get('agent_ids') != '') {
		    $userAgents = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
		    $sql .= " AND agentmanagerid IN $userAgents";
		}
		$result = $db->pquery($sql, []);
		if ($result && $db->num_rows($result) > 0) {
		    while ($row = $db->fetchByAssoc($result)) {
			$accessibleVanlines[$row['vanlinemanagerid']] = $row['vanline_name'];
		    }
		}
            }
        } else {
            $sql = "SELECT vanlinemanagerid, vanline_name
                    FROM vtiger_vanlinemanager
                    JOIN vtiger_agentmanager ON vtiger_vanlinemanager.vanlinemanagerid = vtiger_agentmanager.vanline_id
                    INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
                    WHERE deleted = 0";
            if ($this->get('agent_ids') != '') {
                $userAgents = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
                $sql .= " AND agentmanagerid IN $userAgents";
            }
            $result = $db->pquery($sql, []);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accessibleVanlines[$row['vanlinemanagerid']] = $row['vanline_name'];
                }
            }
        }

        //file_put_contents('logs/devLog.log', "\n USERS RECORD GET ACCESSIBLE AGENTS: ".print_r($accessibleVanlines, true), FILE_APPEND);
        $this->memberVanlines = $accessibleVanlines;
        return $accessibleVanlines;
    }
    public function getAgentUserRel()
    {
        //short this function it does not appear to be needed and extends the query_string to an unwieldy length.
        return '';

        $agentUserRel = Vtiger_Cache::get('vtiger-multi-agent', 'agent_user_rel-'.$this->id);
        if (empty($agentUserRel)) {
            $agentUserRel = [];
            if ($this->isVanLineUser()) {
                //file_put_contents('logs/devLog.log', "\n USERS RECORD AGENTS: ".print_r($this->getVanlineUserAccessibleAgents(), true), FILE_APPEND);
                $userAgents = $this->getVanlineUserAccessibleAgents();
            } else {
                $userAgents = "'".implode('| ', explode(' |##| ', $this->get('agent_ids')))."'";
            }
            $db = PearDatabase::getInstance();
            if ($this->isAdminUser()) {
                $result = $db->pquery("SELECT agent_ids, id FROM vtiger_users");
            } else {
                $result = $db->pquery("SELECT agent_ids, id FROM vtiger_users  WHERE agent_ids REGEXP $userAgents
                                       UNION
                                       SELECT agent_ids, groupid FROM vtiger_users2group
                                       INNER JOIN vtiger_users ON vtiger_users2group.userid = vtiger_users.id
                                       WHERE agent_ids REGEXP $userAgents",
                                      []);
                /*file_put_contents('logs/devLog.log',
                                  "\n USERS RECORD SQL:
						SELECT agent_ids, id FROM vtiger_users  WHERE agent_ids REGEXP $userAgents
						UNION
						SELECT agent_ids, groupid FROM vtiger_users2group
						INNER JOIN vtiger_users ON vtiger_users2group.userid = vtiger_users.id
						WHERE agent_ids REGEXP $userAgents",
                                  FILE_APPEND);*/
            }
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $agentIds = explode(' |##| ', $row['agent_ids']);
                    foreach ($agentIds as $agentId) {
                        //@NOTE: apparently trailing whitespace is a thing.  so trim it!
                        $agentId = trim($agentId);
                        //assume leading white space is possible.
                        $agentId = ltrim($agentId);
                        $agentUserRel[$agentId][] = $row['id'];
                    }
                }
            }
            Vtiger_Cache::set('vtiger-multi-agent', 'agent_user_rel-'.$this->id, $agentUserRel);
        }

        //file_put_contents('logs/devLog.log', "\n USERS RECORD USER REL: ".print_r($agentUserRel, true), FILE_APPEND);
        return Zend_Json::encode($agentUserRel);
    }

    /**
     * This function returns if the current user is a Sales Person or Sales Manager
     *
     * @return    Boolean
     */
    public function isSalesUser() {
      if (empty($this->get('is_salesuser'))) {
          if ($this->isSalesManager() || $this->isSalesperson()) {
              $this->set('is_salesuser', true);
          } else {
              $this->set('is_salesuser', false);
          }
      }

      return $this->get('is_salesuser');
    }

    public function isSalesperson() {
      $db = PearDatabase::getInstance();
      $userRole      = $this->getRole();

      $result = $db->pquery('SELECT rolename FROM vtiger_role WHERE roleid = ?',[$userRole]);
      if($db->num_rows($result) > 0) {
        $row = $result->fetchRow();
        if($row['rolename'] == 'Salesperson') {
          return true;
        }
      }
      return false;
    }

    public function isSalesManager() {
      $db = PearDatabase::getInstance();
      $userRole      = $this->getRole();

      $result = $db->pquery('SELECT rolename FROM vtiger_role WHERE roleid = ?',[$userRole]);
      if($db->num_rows($result) > 0) {
        $row = $result->fetchRow();
        if($row['rolename'] == 'Sales Manager') {
          return true;
        }
      }
      return false;
    }

    public function isVanLineUser()
    {
        $userVanLine = $this->get('is_vanlineuser');
        if (empty($userVanLine)) {
            if ($this->isParentVanLineUser() || $this->isChildVanLineUser()) {
                $this->set('is_vanlineuser', 'on');
                return true;
            } else {
                $this->set('is_vanlineuser', 'off');
                return false;
            }
        } elseif ($userVanLine == 'on') {
            return true;
        } else {
            return false;
        }
    }

    public function isParentVanLineUser()
    {
        $userVanLine = $this->get('is_parentvanlineuser');
        if (empty($userVanLine)) {
            $userRole      = $this->get('roleid');
            if (getRoleDepth($userRole) == 2) {
                $this->set('is_parentvanlineuser', 'on');
                return true;
            } else {
                $this->set('is_parentvanlineuser', 'off');
                return false;
            }
        } elseif ($userVanLine == 'on') {
            return true;
        } else {
            return false;
        }
    }

    public function isChildVanLineUser()
    {
        $userVanLine = $this->get('is_childvanlineuser');
        if (empty($userVanLine)) {
            $userRole      = $this->get('roleid');
            if (getRoleDepth($userRole) == 3) {
                $this->set('is_childvanlineuser', 'on');
                return true;
            } else {
                $this->set('is_childvanlineuser', 'off');
                return false;
            }
        } elseif ($userVanLine == 'on') {
            return true;
        } else {
            return false;
        }
    }

    public function getVanlineUserAccessibleAgents()
    {
        $db              = PearDatabase::getInstance();
        $accesibleAgents = Vtiger_Cache::get('vtiger-multi-agent', 'vanline_agents-'.$this->id);
        if (empty($accesibleAgents)) {
            $sql = 'SELECT agentmanagerid, agency_name FROM vtiger_agentmanager
					INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid
					WHERE deleted = 0';
            if ($this->get('agent_ids') != '') {
                $userVanlines = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
                $sql .= " AND vanline_id IN $userVanlines";
            }
            $result = $db->pquery($sql, []);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accesibleAgents[$row['agentmanagerid']] = $row['agency_name'];
                }
            }
            Vtiger_Cache::set('vtiger-multi-agent', 'vanline_agents-'.$this->id, $accesibleAgents);
        }

        return $accesibleAgents;
    }

    public function getVanlines()
    {
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT vanlinemanagerid, vanline_name FROM vtiger_vanlinemanager
                   INNER JOIN vtiger_crmentity ON vtiger_vanlinemanager.vanlinemanagerid = vtiger_crmentity.crmid
				   WHERE deleted = 0';

        if ($this->isParentVanLineUser()) {
            $userVanlines = '('.implode(',', explode(' |##| ', $this->get('agent_ids'))).')';
            $sql .= " AND vanlinemanagerid IN $userVanlines";
        }

        $result = $db->pquery($sql, []);
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $vanlineArray[$row['vanlinemanagerid']] = htmlspecialchars_decode($row['vanline_name'], ENT_QUOTES);
            }
        }

        return Zend_Json::encode($vanlineArray);
    }

    public function isAgencyAdmin()
    {
        $userAgencyAdmin = $this->get('is_agency_admin');
        if (empty($userAgencyAdmin)) {
            $userRole = $this->get('roleid');
            $targetRole = 4;
            if(getenv("INSTANCE_NAME") == "sirva") {
                $targetRole = 6;
            }

            if (getRoleDepth($userRole) <= $targetRole) {
                $this->set('is_agency_admin', 'on');
                return true;
            } else {
                $this->set('is_agency_admin', 'off');
                return false;
            }
        } elseif ($userAgencyAdmin == 'on') {
            return true;
        } else {
            return false;
        }
    }
    public function isCoordinator()
    {
        $userCoordinator = $this->get('is_coordinator');
        if (empty($userCoordinator)) {
            $userRole = $this->get('roleid');
            if (getRoleDepth($userRole) == 6) {
                $this->set('is_coordinator', 'on');
                return true;
            } else {
                $this->set('is_coordinator', 'off');
                return false;
            }
        } elseif ($userCoordinator == 'on') {
            return true;
        } else {
            return false;
        }
    }

    //function to pull the user "role depth" rather than checking isAgencyAdmin etc.
    //@TODO: consider adding constant definers for the roles since knowing 4 is sales manager is mystical.
    public function getUserRoleDepth()
    {
        if (!$this->get('user_role_depth')) {
            $userRole = $this->get('roleid');
            $this->set('user_role_depth', getRoleDepth($userRole));
        }
        return $this->get('user_role_depth');
    }

    public function isLowLevelAdminUser()
    {
        if ($this->isParentVanLineUser() || $this->isAgencyAdmin()) {
            return true;
        }

        return false;
    }

    public function getLowLevelAdminSkippedMenus()
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if($currentUserModel->isAdminUser()) {
            return [];
        }

        if ($currentUserModel->isParentVanLineUser()) {
            $availableMenus = array(
                'LBL_USERS',
                'LBL_LIST_WORKFLOWS',
                'MoveEasy'
            );
        } elseif ($currentUserModel->isAgencyAdmin()) {
            $availableMenus = array(
                'LBL_LIST_WORKFLOWS',
                'MoveEasy'
            );
        }

        $adminOnlyList = ['TariffManager', 'Webforms','TooltipManager'];


        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT name FROM vtiger_settings_field WHERE name NOT IN (' . generateQuestionMarks($availableMenus) . ')', array($availableMenus));

        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                $model = Vtiger_Module_Model::getInstance($row['name']);
                if(!$model || !$model->isActive() || in_array($row['name'], $adminOnlyList)) {
                    $skipMenus[] = $row['name'];
                }
            }
        }

        return $skipMenus;
    }

    public function getLowLevelAdminSettingAccess()
    {
    	$accesibleModules =  array(
    	    'System', //Settings Index Page
            'Vtiger', //Pinning AJAX Calls
    		'Users',
    		'Workflows',
            'Picklist',
            'PicklistCustomizer',
            'MoveEasyIntegration',
        );

    	return $accesibleModules;
    }

    public function getDefaultCoordinator($isAVL)
    {
        if (getenv('INSTANCE_NAME') != 'sirva') {
            return null;
        }

        $db = PearDatabase::getInstance();

        $coordField = $isAVL ? 'move_coordinator' : 'move_coordinator_navl';

        $sql = "SELECT $coordField FROM `vtiger_users` WHERE id=?";
        $result = $db->pquery($sql, [$this->getId()]);
        $row = $result->fetchRow();
        if ($row && $row[0]) {
            return $row[0];
        }

        return null;
    }

    public function getUserAgents()
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$userId = $this->getId();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $isAdmin = $currentUser->isAdminUser();
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT agency_code FROM `vtiger_user2agency`";
        if($isAdmin){
            $result = $db->pquery($sql, array());
        }
        else{
            $sql = $sql . " WHERE userid=?";
            $result = $db->pquery($sql, array($userId));
        }
        $agents = array();
        while($row =& $result->fetchRow()) {
            $agents[] = $row[0];
        }

        return $agents;
    */
    }

    public function canAccessAgent($agent)
    {
        return array_key_exists($agent, $this->getAccessibleAgentsForUser());
    }

    public function canAccessVanline($vanline)
    {
        return array_key_exists($vanline, $this->getAccessibleVanlinesForUser());
    }

    public function getUserAgencyGroup()
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        //file_put_contents('logs/assigned_to.log', "\n In getUserAgencyGroup function \n", FILE_APPEND);
        /*$db = PearDatabase::getInstance();
        $userid = $this->getId();
        $row = array();

        $sql = "SELECT agency_name FROM `vtiger_user2agency` JOIN `vtiger_agentmanager` ON `vtiger_user2agency`.agency_code = `vtiger_agentmanager`.agentmanagerid WHERE `vtiger_user2agency`.userid=?";
        $result = $db->pquery($sql, array($userid));
        $row = $result->fetchRow();
        $agencyname = $row[0];

        //file_put_contents('logs/assigned_to.log', "\n \$userid: ".$userid, FILE_APPEND);
        //file_put_contents('logs/assigned_to.log', "\n \$agencyname: ".$agencyname, FILE_APPEND);

        $sql = "SELECT parentrole FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE userid=?";
        $result = $db->pquery($sql, array($userid));
        $row = $result->fetchRow();
        $parentroles_string = $row[0];
        //file_put_contents('logs/assigned_to.log', "\n \$parentroles_string: ".$parentroles_string, FILE_APPEND);

        $parentroles = array();
        $parentroles = explode('::', $parentroles_string);
        //file_put_contents('logs/assigned_to.log', "\n \$parentroles_string: ".print_r($parentroles,true), FILE_APPEND);

        foreach ($parentroles as $role) {
            //file_put_contents('logs/assigned_to.log', "\n In foreach, role: ".$role, FILE_APPEND);
            $sql = "SELECT groupname, `vtiger_groups`.groupid FROM `vtiger_group2rs` JOIN `vtiger_groups` ON `vtiger_group2rs`.groupid = `vtiger_groups`.groupid WHERE roleandsubid=?";
            $result = $db->pquery($sql, array($role));
            $row = $result->fetchRow();
            //file_put_contents('logs/assigned_to.log', "\n \$row: ".print_r($row,true), FILE_APPEND);
            if ($row != NULL) {
                //file_put_contents('logs/assigned_to.log', "\n \$row[0]: ".$row[0], FILE_APPEND);
                //file_put_contents('logs/assigned_to.log', "\n \$row[1]: ".$row[1], FILE_APPEND);

                if($agencyname == $row[0]){
                    return $row[1];
                }
            }
        }
        return false;
    */
    }

    public static function getInstanceById($recordId, $module = 'Users') {
        $userInstance = parent::getInstanceById($recordId, $module);
        if ($userInstance) {
            //@NOTE: Leave this because it's expected but not done?  why? No, seriously why is it not done?
            //@TODO: figure out why.
            $userInstance->id = $userInstance->getId();
        }
        return $userInstance;
    }

    public function allowedToView($record, $roleid) {
        if (!$record) {
            $record = $this->getId();
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if ($currentUserModel->get('id') == $record) {
            return true;
        }

        if ($currentUserModel->isAdminUser() == true) {
            return true;
        }

        //upper level roles that can view underling's records.
        if (
            $currentUserModel->isAdminUser() == true ||
            $currentUserModel->isParentVanLineUser() ||
            (
                getenv('INSTANCE_NAME') == 'uvlc' &&
                $currentUserModel->isAgencyAdmin()
            )
        ) {

            if (!$roleid) {
                $roleid = $this->getRole();
            }

            if ($roleid) {
                $roleDepth = getRoleDepth($roleid);
            } else {
                $roleDepth = 100;
            }

            $currentUserDepth = $currentUserModel->getUserRoleDepth();

            $accessibleAgents = $currentUserModel->getAccessibleAgentsForUser();
            $userModel = Users_Record_Model::getInstanceById($record);
            $targetAccessibleAgents = $userModel->getAccessibleAgentsForUser();

            $intersectedAgents = array_intersect($accessibleAgents, $targetAccessibleAgents);
            if(count($intersectedAgents) == 0) {
                return false;
            }

            if ($currentUserDepth <= $roleDepth) {
                $subordinates = getSubordinateRoleAndUsers($currentUserModel->get('roleid'));
                foreach ($subordinates as $roleID => $userArray) {
                    //if (in_array($record, $userArray)) {
                    if (array_key_exists($record, $userArray)) {
                        if($currentUserDepth == $roleDepth && $_REQUEST['view'] == 'Edit') {
                            return false;
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isMemberOf($agentid) {
        global $adb;
        $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
        $result = $adb->pquery($sql, [$agentid]);
        if(!$result || ($result->fields['setype'] != 'AgentManager' && $result->fields['setype'] != 'VanlineManager')) {
            //Invalid id passed in
            return false;
        }

        if($result->fields['setype'] == 'VanlineManager') {
            $memberArray = $this->getAccessibleVanlinesForUser();
        } else {
            $memberArray = $this->getAccessibleAgentsForUser();
        }

        return array_key_exists($agentid, $memberArray);
    }
}
