<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('include/database/Postgres8.php');
require_once('include/utils/utils.php');
require_once('include/utils/GetUserGroups.php');
include_once('config.php');
require_once("include/events/include.inc");
require_once 'includes/runtime/Cache.php';
global $log;
/** To retreive the mail server info resultset for the specified user
 * * @param $user -- The user object:: Type Object
 *
 * @returns  the mail server info resultset
 */
function getMailServerInfo($user)
{
    global $log;
    $log->debug("Entering getMailServerInfo(".$user->user_name.") method ...");
    global $adb;
    $sql    = "SELECT * FROM vtiger_mail_accounts WHERE status=1 AND user_id=?";
    $result = $adb->pquery($sql, [$user->id]);
    $log->debug("Exiting getMailServerInfo method ...");

    return $result;
}

/** To get the Role of the specified user
 *
 * @param $userid -- The user Id:: Type integer
 *
 * @returns  vtiger_roleid :: Type String
 */
function fetchUserRole($userid)
{
    global $log;
    $log->debug("Entering fetchUserRole(".$userid.") method ...");
    global $adb;
    $sql    = "SELECT roleid FROM vtiger_user2role WHERE userid=?";
    $result = $adb->pquery($sql, [$userid]);
    $roleid = $adb->query_result($result, 0, "roleid");
    $log->debug("Exiting fetchUserRole method ...");
    return $roleid;
}

/** Function to get the lists of groupids releated with an user
 * This function accepts the user id as arguments and
 * returns the groupids related with the user id
 * as a comma seperated string
 */
function fetchUserGroupids($userid)
{
    global $log;
    $log->debug("Entering fetchUserGroupids(".$userid.") method ...");
    global $adb;
    $focus = new GetUserGroups();
    $focus->getAllUserGroups($userid);
    //Asha: Remove implode if not required and if so, also remove explode functions used at the recieving end of this function
    $groupidlists = implode(",", $focus->user_groups);
    $log->debug("Exiting fetchUserGroupids method ...");

    return $groupidlists;
}

/** Function to get all the vtiger_tab utility action permission for the specified vtiger_profile
 *
 * @param $profileid -- Profile Id:: Type integer
 *
 * @returns  Tab Utility Action Permission Array in the following format:
 * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                                |
 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getTabsUtilityActionPermission($profileid)
{
    global $log;
    $log->debug("Entering getTabsUtilityActionPermission(".$profileid.") method ...");
    global $adb;
    $check      = [];
    $temp_tabid = [];
    $sql1       = "SELECT * FROM vtiger_profile2utility WHERE profileid=? ORDER BY(tabid)";
    $result1    = $adb->pquery($sql1, [$profileid]);
    $num_rows1  = $adb->num_rows($result1);
    for ($i = 0; $i < $num_rows1; $i++) {
        $tab_id = $adb->query_result($result1, $i, 'tabid');
        if (!in_array($tab_id, $temp_tabid)) {
            $temp_tabid[] = $tab_id;
            $access       = [];
        }
        $action_id          = $adb->query_result($result1, $i, 'activityid');
        $per_id             = $adb->query_result($result1, $i, 'permission');
        $access[$action_id] = $per_id;
        $check[$tab_id]     = $access;
    }
    $log->debug("Exiting getTabsUtilityActionPermission method ...");

    return $check;
}

/**This Function returns the Default Organisation Sharing Action Array for all modules whose sharing actions are editable
 * The result array will be in the following format:
 * Arr=(tabid1=>Sharing Action Id,
 *      tabid2=>SharingAction Id,
 *            |
 *            |
 *            |
 *      tabid3=>SharingAcion Id)
 */
function getDefaultSharingEditAction()
{
    global $log;
    $log->debug("Entering getDefaultSharingEditAction() method ...");
    global $adb;
    //retreiving the standard permissions
    $sql           = "SELECT * FROM vtiger_def_org_share WHERE editstatus=0";
    $result        = $adb->pquery($sql, []);
    $permissionRow = $adb->fetch_array($result);
    do {
        for ($j = 0; $j < count($permissionRow); $j++) {
            $copy[$permissionRow[1]] = $permissionRow[2];
        }
    } while ($permissionRow = $adb->fetch_array($result));
    $log->debug("Exiting getDefaultSharingEditAction method ...");

    return $copy;
}

/**This Function returns the Default Organisation Sharing Action Array for modules with edit status in (0,1)
 * The result array will be in the following format:
 * Arr=(tabid1=>Sharing Action Id,
 *      tabid2=>SharingAction Id,
 *            |
 *            |
 *            |
 *      tabid3=>SharingAcion Id)
 */
function getDefaultSharingAction()
{
    global $log;
    $log->debug("Entering getDefaultSharingAction() method ...");
    global $adb;
    //retreivin the standard permissions
    $sql           = "SELECT * FROM vtiger_def_org_share WHERE editstatus IN(0,1)";
    $result        = $adb->pquery($sql, []);
    $permissionRow = $adb->fetch_array($result);
    do {
        for ($j = 0; $j < count($permissionRow); $j++) {
            $copy[$permissionRow[1]] = $permissionRow[2];
        }
    } while ($permissionRow = $adb->fetch_array($result));
    $log->debug("Exiting getDefaultSharingAction method ...");

    return $copy;
}

/**This Function returns the Default Organisation Sharing Action Array for all modules
 * The result array will be in the following format:
 * Arr=(tabid1=>Sharing Action Id,
 *      tabid2=>SharingAction Id,
 *            |
 *            |
 *            |
 *      tabid3=>SharingAcion Id)
 */
function getAllDefaultSharingAction()
{
    global $log;
    $log->debug("Entering getAllDefaultSharingAction() method ...");
    global $adb;
    $copy = [];
    //retreiving the standard permissions
    $sql      = "SELECT * FROM vtiger_def_org_share";
    $result   = $adb->pquery($sql, []);
    $num_rows = $adb->num_rows($result);
    for ($i = 0; $i < $num_rows; $i++) {
        $tabid        = $adb->query_result($result, $i, 'tabid');
        $permission   = $adb->query_result($result, $i, 'permission');
        $copy[$tabid] = $permission;
    }
    $log->debug("Exiting getAllDefaultSharingAction method ...");

    return $copy;
}

/** Function to update user to vtiger_role mapping based on the userid
 *
 * @param $roleid -- Role Id:: Type varchar
 * @param $userid User Id:: Type integer
 */
function updateUser2RoleMapping($roleid, $userid)
{
    global $log;
    $log->debug("Entering updateUser2RoleMapping(".$roleid.",".$userid.") method ...");
    global $adb;
    //Check if row already exists
    $sqlcheck    = "SELECT * FROM vtiger_user2role WHERE userid=?";
    $resultcheck = $adb->pquery($sqlcheck, [$userid]);
    if ($adb->num_rows($resultcheck) == 1) {
        $sqldelete     = "DELETE FROM vtiger_user2role WHERE userid=?";
        $delparams     = [$userid];
        $result_delete = $adb->pquery($sqldelete, $delparams);
    }
    $sql    = "INSERT INTO vtiger_user2role(userid,roleid) VALUES(?,?)";
    $params = [$userid, $roleid];
    $result = $adb->pquery($sql, $params);
    $log->debug("Exiting updateUser2RoleMapping method ...");
}

/** Function to get the vtiger_role name from the vtiger_roleid
 *
 * @param $roleid -- Role Id:: Type varchar
 * @returns $rolename -- Role Name:: Type varchar
 */
function getRoleName($roleid)
{
    global $log;
    $log->debug("Entering getRoleName(".$roleid.") method ...");
    global $adb;
    $sql1     = "SELECT * FROM vtiger_role WHERE roleid=?";
    $result   = $adb->pquery($sql1, [$roleid]);
    $rolename = $adb->query_result($result, 0, "rolename");
    $log->debug("Exiting getRoleName method ...");

    return $rolename;
}

function getRoleDepth($roleid)
{
    global $adb;
    global $roleDepthCache;

    //@TODO: temp cast on this broken bone.
    //@TODO; want to move this into the currentUserRecordModel maybe?
    if (isset($roleDepthCache[$roleid])) {
        $roleDepth = $roleDepthCache[$roleid];
    } else {
        $sql       = "SELECT depth FROM vtiger_role WHERE roleid=?";
        $result    = $adb->pquery($sql, [$roleid]);
        $roleDepth = $adb->query_result($result, 0, "depth");
        $roleDepthCache[$roleid] = $roleDepth;
    }

    return $roleDepth;
}

/** Function to check if the currently logged in user is permitted to perform the specified action
 *
 * @param $module     -- Module Name:: Type varchar
 * @param $actionname -- Action Name:: Type varchar
 * @param $recordid   -- Record Id:: Type integer
 *
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isPermitted($module, $actionname, $record_id = '')
{
	$debugCounter = 0;
    global $log;
    $log->debug("Entering isPermitted(".$module.",".$actionname.",".$record_id.") method ...");
	//file_put_contents('logs/devLog.log', "\n Entering isPermitted(".$module.",".$actionname.",".$record_id.") method ...", FILE_APPEND);

    //NOTE: Define these at the top for general use.
    $current_user = Users_Record_Model::getCurrentUserModel();
    $tabid        = getTabid($module);
    $actionid     = getActionid($actionname);
    $depth        = getRoleDepth($current_user->get('roleid'));

    //@NOTE: These must be AFTER the above things.
    require ('include/utils/LoadUserPrivileges.php');
    require ('include/utils/LoadUserSharingPrivileges.php');
    $permission = "no";

    if (($module == 'Users' || $module == 'Home' || $module == 'uploads') && $_REQUEST['parenttab'] != 'Settings') {
        //These modules dont have security right now
        $permission = "yes";
        $log->debug("Exiting isPermitted method ...");
        //file_put_contents('logs/devLog.log', "\n 1", FILE_APPEND);
        return $permission;
    }
    if (in_array($module,['WFLocationTypes','WFStatus','WFSlotConfiguration', 'WFConditions', 'WFActivityCodes']) && $record_id != '' && ($actionname == 'EditView' || $actionname == 'Delete')) {
        $sql    = "SELECT * FROM `vtiger_".strtolower($module)."` WHERE is_default = 1 AND ".strtolower($module)."id = ?";
        $result = $adb->pquery($sql, [$record_id]);
        if ($adb->num_rows($result)) {
            return 'no';
        }
    }

    if($record_id != '' && $actionname == 'Delete') {
        $sql    = "SELECT * FROM `vtiger_crmentity_flags` WHERE (in_use = 1  OR prevent_delete = 1) AND crmid = ?";
        $result = $adb->pquery($sql, [$record_id]);
        if ($adb->num_rows($result)) {
            return 'no';
        }
    }

    if($record_id != '' && ($actionname == 'EditView')) {
        $sql    = "SELECT * FROM `vtiger_crmentity_flags` WHERE prevent_edit = 1 AND crmid = ?";
        $result = $adb->pquery($sql, [$record_id]);
        if ($adb->num_rows($result)) {
            return 'no';
        }
    }



    //If it is an autos only estimate or booked, it cannot be edited (TFS2851)
    if(
        !empty($record_id) &&
        $record_id != '' &&
        getenv("INSTANCE_NAME") == "sirva" &&
        $module == 'Estimates' &&
        $actionname == 'EditView'
    ){
        global $adb;
        $sql      = "SELECT quoteid FROM vtiger_quotes WHERE quoteid=? && effective_tariff = (SELECT tariffmanagerid FROM vtiger_tariffmanager WHERE tariffmanagername = 'Auto Only')";
        $result   = $adb->pquery($sql, [$record_id]);
        if($adb->num_rows($result)){
            return 'no';
        }else if(getenv('INSTANCE_NAME') == 'sirva') {
            $sql = "SELECT `vtiger_potential`.`sales_stage`, `vtiger_potential`.`register_sts` FROM `vtiger_potential`
                    JOIN `vtiger_quotes` ON `vtiger_quotes`.`potentialid` = `vtiger_potential`.potentialid
                    WHERE `vtiger_quotes`.quoteid = ?
                    AND `vtiger_quotes`.`is_primary` = 1";
            $res = $adb->pquery($sql, [$record_id]);
            if($adb->num_rows($res) > 0) {
                $row = $res->fetchRow();
                $locked_types = ['Closed Won','Closed Lost'];
                if(in_array($row['sales_stage'], $locked_types) !== false && $row['register_sts'] == 1) {
                    return 'no';
                }
            }
        }
    }

	//full list view for everyone on agents detailview?
    if (
        getenv('INSTANCE_NAME') == 'sirva' &&
        $module == 'Agents' &&
        $actionname == 'DetailView'
    ) {
        return 'yes';
    }

    // special graebel permissions
    if(getenv('INSTANCE_NAME') == 'graebel') {
        if (
            $module == 'Vehicles' &&
            in_array($actionname, ['Save', 'Edit', 'Delete', 'EditView', 'Import', 'QuickCreate', 'SaveAJax'])
        ) {
            if (!\MoveCrm\InputUtils::CheckboxToBool($current_user->get('vehicles_edit_permission'))) {
                return 'no';
            }
        } elseif (
            $record_id &&
            $module == 'Employees' &&
            in_array($actionname, ['Save', 'Edit', 'Delete', 'EditView', 'Import', 'QuickCreate', 'SaveAJax'])
        ) {
            $employee = Employees_Record_Model::getInstanceById($record_id);
            if (preg_match('/Driver/', $employee->get('employee_prole')) || preg_match('/Driver/', $employee->get('contractor_prole'))) {
                if (!\MoveCrm\InputUtils::CheckboxToBool($current_user->get('drivers_edit_permission'))) {

                    return 'no';
                }
            }
        } elseif (
            $record_id &&
            (
                $module == 'Calendar' ||
                $module == 'Events'
            ) &&
            in_array($actionname, ['Edit','EditView','Save','SaveAjax','View','DetailView']))
        {
            // check for creator
            $res = $adb->pquery('SELECT 1 FROM vtiger_crmentity WHERE crmid=? AND smcreatorid=?', [$record_id, $current_user->id]);
            if($adb->num_rows($res) > 0)
            {
                return 'yes';
            }
        }
      }
    if (getenv("INSTANCE_NAME") == "graebel") {
        if ($actionname == 'DetailView') {
            if ($module == 'Orders') {
                return 'yes';
            }
            if ($module == 'Contracts') {
                return 'yes';
            }
        }
    }

    //if(($module == 'Accounts' || $module == 'Contracts') && $actionname == 'DetailView') {
    //    return 'yes';
    //}
    //Users_Record_Model->getVanlines();

    //Check is the user has access to the record base in the agent_id
    //if (empty($current_user->get('is_vanlineuser'))) {
    //    $current_user->isVanLineUser();
    //    global $current_user;
    //}
    if (
        !empty($record_id) &&
        $record_id != '' &&
        !$current_user->isAdminUser()
    ) {
        if (
            $module == 'VanlineManager' &&
            array_key_exists($record_id, $current_user->getAccessibleVanlinesForUser()) &&
            $actionname != 'EditView' &&
            $actionname != 'Delete'
        ) {
            //file_put_contents('logs/devLog.log', "\n 2", FILE_APPEND);
            return 'yes';
        }
        if ($module == 'AgentManager') {
            if (array_key_exists($record_id, $current_user->getAccessibleAgentsForUser()) && $actionname != 'EditView' && $actionname != 'Delete') {
                return 'yes';
            } elseif ($current_user->isAgencyAdmin() && $actionname == 'EditView') {
                return 'yes';
            }
        }

        $recordAgentOwner = getRecordAgentOwner($record_id);
		$accessible = getPermittedAccessible();

        //OT4509 - PVL owned records can be read by the agencies that belong to the vanline - We used the field uitype to filtered the "shared" modules
        $fieldInstance = Vtiger_Cache::get('agentid_field_instance', $module);

        if( $fieldInstance == null ){
            $fieldInstance = Vtiger_Field_Model::getInstance('agentid', Vtiger_Module_Model::getInstance($module));
            Vtiger_Cache::set('agentid_field_instance', $module, $fieldInstance);
        }

        //OT4869 - Remove the view condition. Whether the user can edit, view, delete will depend on the profile
        if(
            $fieldInstance->uitype == '1020' &&
            !$current_user->isVanlineUser()
        ){
            //target module allow owner to both vanlines and agent. Let's add the vanline ID to accesible agents
            $accessible = $current_user->getBothAccessibleOwnersIdsForUser();
        }

        if (
            $actionname=='DetailView' &&
            (
                $module == 'Accounts' ||
                $module == 'Contracts'
            )
        ) {
            $accessible = array_merge($accessible, array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleVanlinesForUser()));
        }

        if (
            getRoleDepth($current_user->get('roleid')) == 7 &&
            (
                $module == 'Opportunities' ||
                $module == 'Orders' ||
                $module == 'Leads'
            )
        ) {
            //salesperson logic
            $salesPersonId = getRecordSalesPerson($record_id);
            if ($current_user->getId() != $salesPersonId && $salesPersonId) {
                return 'no';
            }
        }

		//participating agent logic
        if ($module != 'LeadSourceManager') {
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                //removed module requirement from participant check so it would fire for related records
                //workaround for read only users
                //hashing out participants + role permissions needs to be done more thoroughly
                if (
                    isParticipatingAgentAccesible($accessible, $record_id, $actionname, $module) &&
                    !(
                        $depth==8 &&
                        $actionname=='EditView'
                    )
                ) {
                    return "yes";
                }
            }
        }
        $actionid    = getActionid($actionname);
		//file_put_contents('logs/devLog.log', "\n record assigned user: ".print_r(getRecordOwnerId($record_id), true)."\n CUID: ".$current_user->getId(), FILE_APPEND);
        if (
            $recordAgentOwner &&
            $recordAgentOwner != null &&
            !in_array($recordAgentOwner, $accessible) &&
            !in_array($current_user->getId(), getRecordOwnerId($record_id))
        ) {
            //file_put_contents('logs/devLog.log', "\n 6", FILE_APPEND);
            return 'no';
        }
        if (
            $recordAgentOwner &&
            $recordAgentOwner != null &&
            in_array($recordAgentOwner, $accessible) &&
            !(
                $depth==8 &&
                $actionid < 3 &&
                $actionid > 4
            )
        ) {
			return "yes";
		}
    }
    //Checking the Access for the Settings Module
    if (
        $module == 'Settings' ||
        $module == 'Administration' ||
        $module == 'System' ||
        $_REQUEST['parenttab'] == 'Settings'
    ) {
        if (!$current_user->isAdminUser()) {
            $permission = "no";
        } else {
            $permission = "yes";
        }
        $log->debug("Exiting isPermitted method ...");
        //file_put_contents('logs/devLog.log', "\n 7", FILE_APPEND);
        return $permission;
    }

    if ($actionname == 'DuplicatesHandling' && getenv('INSTANCE_NAME') == 'sirva'){
        return 'no';
    }
    //Retreiving the Tabid and Action Id
    $tabid       = getTabid($module);
    $actionid    = getActionid($actionname);
    $checkModule = $module;
    if ($checkModule == 'Events') {
        $checkModule = 'Calendar';
    }
    if (vtlib_isModuleActive($checkModule)) {
        //Checking whether the user is admin
        if ($current_user->isAdminUser()) {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 8", FILE_APPEND);
            return 'yes';
        }
        //If no actionid, then allow action is vtiger_tab permission is available
        if ($actionid === '') {
            if ($profileTabsPermission[$tabid] == 0) {
                $permission = "yes";
                $log->debug("Exiting isPermitted method ...");
            } else {
                $permission = "no";
            }

            //file_put_contents('logs/devLog.log', "\n 9", FILE_APPEND);
            return $permission;
        }
        $action = getActionname($actionid);
        //Checking for view all permission
        if ($profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
            if ($actionid == 3 || $actionid == 4) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 10", FILE_APPEND);
                return 'yes';
            }
        }
        //Checking for edit all permission
        if ($profileGlobalPermission[2] == 0) {
            if ($actionid == 3 || $actionid == 4 || $actionid == 0 || $actionid == 1) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 11", FILE_APPEND);
                return 'yes';
            }
        }
        //Checking for vtiger_tab permission
        if ($profileTabsPermission[$tabid] != 0) {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 12", FILE_APPEND);
            return 'no';
        }
        //Checking for Action Permission
        if ($module != 'Events' && strlen($profileActionPermission[$tabid][$actionid]) < 1 && $profileActionPermission[$tabid][$actionid] == '') {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 13", FILE_APPEND);
            return 'yes';
        }
        if ($profileActionPermission[$tabid][$actionid] != 0 && $profileActionPermission[$tabid][$actionid] != '') {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 14", FILE_APPEND);
            return 'no';
        }
        //Checking and returning true if recorid is null
        if ($record_id == '') {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 15", FILE_APPEND);
            return 'yes';
        }
        //If modules is Products,Vendors,Faq,PriceBook then no sharing
        if ($record_id != '') {
            if (getTabOwnedBy($module) == 1) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 16", FILE_APPEND);
                return 'yes';
            }
        }
        //Retreiving the RecordOwnerId
        $recOwnType     = '';
        $recOwnId       = '';
        $recordOwnerArr = getRecordOwnerId($record_id);
        foreach ($recordOwnerArr as $type => $id) {
            $recOwnType = $type;
            $recOwnId   = $id;
        }
        //Retreiving the default Organisation sharing Access
        $others_permission_id = $defaultOrgSharingPermission[$tabid];
        if ($recOwnType == 'Users') {
            //Checking if the Record Owner is the current User
            if ($current_user->id == $recOwnId) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 17", FILE_APPEND);
                return 'yes';
            }
            //Checking if the Record Owner is the Subordinate User
            foreach ($subordinate_roles_users as $roleid => $userids) {
                if (in_array($recOwnId, $userids)) {
                    $permission = 'yes';
                    if ($module == 'Calendar') {
                        $permission = isCalendarPermittedByRoleDepth($record_id,$actionname);
                    }
                    $log->debug("Exiting isPermitted method ...");

                    //file_put_contents('logs/devLog.log', "\n 18", FILE_APPEND);
                    return $permission;
                }
            }
        } elseif ($recOwnType == 'Groups') {
            //Checking if the record owner is the current user's group
            if (in_array($recOwnId, $current_user_groups)) {
                $permission = 'yes';
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 19", FILE_APPEND);
                return 'yes';
            }
        }
		//accessible agents check
        if (in_array($recordAgentOwner, $accessible)) {
			return 'yes';
		}
        //Checking for Default Org Sharing permission
        if ($others_permission_id == 0) {
            if ($actionid == 1 || $actionid == 0) {
                if ($module == 'Calendar') {
                    $permission = isCalendarPermittedByRoleDepth($record_id,$actionname);
                } else {
                    $permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
                }
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 20", FILE_APPEND);
                return $permission;
            } elseif ($actionid == 2) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 21", FILE_APPEND);
                return 'no';
            } else {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 22", FILE_APPEND);
                return 'yes';
            }
        } elseif ($others_permission_id == 1) {
            if ($actionid == 2) {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 23", FILE_APPEND);
                return 'no';
            } else {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 24", FILE_APPEND);
                return 'yes';
            }
        } elseif ($others_permission_id == 2) {
            $log->debug("Exiting isPermitted method ...");

            //file_put_contents('logs/devLog.log', "\n 25", FILE_APPEND);
            return 'yes';
        } elseif ($others_permission_id == 3) {
            if ($actionid == 3 || $actionid == 4) { //View
                if ($module == 'Calendar') {
                    $permission = isCalendarPermittedByRoleDepth($record_id,$actionname);
                } else {
                    $permission = isReadPermittedBySharing($module, $tabid, $actionid, $record_id);
                }
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 26 $permission", FILE_APPEND);
                return $permission;
            } elseif ($actionid == 0 || $actionid == 1) { //Edit
                if ($module == 'Calendar') { // index=0 and detailview=1
                    $permission = isCalendarPermittedByRoleDepth($record_id,$actionname);
                } else {
                    $permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
                }
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 27", FILE_APPEND);
                return $permission;
            } elseif ($actionid == 2) {

                //file_put_contents('logs/devLog.log', "\n 28", FILE_APPEND);
                return 'no';
            } else {
                $log->debug("Exiting isPermitted method ...");

                //file_put_contents('logs/devLog.log', "\n 29", FILE_APPEND);
                return 'yes';
            }
        } else {
            $permission = "yes";
        }
    } else {
        $permission = "no";
    }

    $log->debug("Exiting isPermitted method ...");

    //file_put_contents('logs/devLog.log', "\n 30", FILE_APPEND);
    return $permission;
}

function getPermittedAccessible()
{
	$current_user     = Users_Record_Model::getCurrentUserModel();
	$db = PearDatabase::getInstance();
	$accessible = [];
    if ($current_user->isAdminUser()) {
		$sql = "SELECT agentmanagerid FROM `vtiger_agentmanager`";
		$result = $db->pquery($sql, []);
        while ($row =& $result->fetchRow()) {
			$accessible[] = $row['agentmanagerid'];
		}
		return $accessible;
    } else {
		if ($current_user->agent_ids != '' && !$current_user->isVanlineUser()) {
			$accessible = explode(' |##| ', $current_user->agent_ids);
		} elseif ($current_user->isVanlineUser()) {
			$accessibleAgents = $current_user->getVanlineUserAccessibleAgents();
			$accessibleVanlines = $current_user->getAccessibleVanlinesForUser();
			$accessible = $current_user->AssocArrayMerge($accessibleAgents, $accessibleVanlines);
			//@NOTE if accessible isn't what you expected it's this, I extrapolated the fix.
			//$accessible = array_flip($accessible);
			$accessible         = array_keys($accessible);
			//file_put_contents('logs/devLog.log', "\n accessible: ".print_r($accessible, true), FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n accessibleAgents: ".print_r($accessibleAgents, true), FILE_APPEND);
			//file_put_contents('logs/devLog.log', "\n accessibleVanlines: ".print_r($accessibleVanlines, true), FILE_APPEND);
		}
		return $accessible;
	}
}

/** Function to check if the currently logged in user has Read Access due to Sharing for the specified record
 *
 * @param $module   -- Module Name:: Type varchar
 * @param $actionid -- Action Id:: Type integer
 * @param $recordid -- Record Id:: Type integer
 * @param $tabid    -- Tab Id:: Type integer
 *
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadPermittedBySharing($module, $tabid, $actionid, $record_id)
{
	//file_put_contents('logs/devLog.log', "\n Entering isReadPermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...", FILE_APPEND);
    global $log;
    $log->debug("Entering isReadPermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
    global $adb;
    //global $current_user;
	$current_user      = Users_Record_Model::getCurrentUserModel();
    require ('include/utils/LoadUserSharingPrivileges.php');
    $ownertype         = '';
    $ownerid           = '';
    $sharePer          = 'no';
    $sharingModuleList = getSharingModuleList();
	//file_put_contents('logs/devLog.log', "\n share mods: ".print_r($sharingModuleList, true), FILE_APPEND);
    if (!in_array($module, $sharingModuleList)) {
        $sharePer = 'no';
		//file_put_contents('logs/devLog.log', "\n 1", FILE_APPEND);
        return $sharePer;
    }
    $recordOwnerArr = getRecordOwnerId($record_id);
    foreach ($recordOwnerArr as $type => $id) {
        $ownertype = $type;
        $ownerid   = $id;
    }
    $varname      = $module."_share_read_permission";
    $read_per_arr = $$varname;
	//file_put_contents('logs/devLog.log', "\n ownerType: $ownertype", FILE_APPEND);
	//file_put_contents('logs/devLog.log', "\n read_per_arr: ".print_r($read_per_arr, true), FILE_APPEND);
    if ($ownertype == 'Users') {
		//assigned user id is a user
        //Checking the Read Sharing Permission Array in Role Users
        $read_role_per = $read_per_arr['ROLE'];
        foreach ($read_role_per as $roleid => $userids) {
            if (in_array($ownerid, $userids)) {
                $sharePer = 'yes';
                $log->debug("Exiting isReadPermittedBySharing method ...");
				//file_put_contents('logs/devLog.log', "\n 2", FILE_APPEND);
                return $sharePer;
            }
        }
        //Checking the Read Sharing Permission Array in Groups Users
        $read_grp_per = $read_per_arr['GROUP'];
        foreach ($read_grp_per as $grpid => $userids) {
            if (in_array($ownerid, $userids)) {
                $sharePer = 'yes';
                $log->debug("Exiting isReadPermittedBySharing method ...");
				//file_put_contents('logs/devLog.log', "\n 3", FILE_APPEND);
                return $sharePer;
            }
        }
    } elseif ($ownertype == 'Groups') {
		//assigned user id is a group
        $read_grp_per = $read_per_arr['GROUP'];
        if (array_key_exists($ownerid, $read_grp_per)) {
            $sharePer = 'yes';
            $log->debug("Exiting isReadPermittedBySharing method ...");
			//file_put_contents('logs/devLog.log', "\n 4", FILE_APPEND);
            return $sharePer;
        }
    }
    //Checking for the Related Sharing Permission
    $relatedModuleArray = $related_module_share[$tabid];
    if (is_array($relatedModuleArray)) {
        foreach ($relatedModuleArray as $parModId) {
            $parRecordOwner = getParentRecordOwner($tabid, $parModId, $record_id);
            if (sizeof($parRecordOwner) > 0) {
                $parModName           = getTabname($parModId);
                $rel_var              = $parModName."_".$module."_share_read_permission";
                $read_related_per_arr = $$rel_var;
                $rel_owner_type       = '';
                $rel_owner_id         = '';
                foreach ($parRecordOwner as $rel_type => $rel_id) {
                    $rel_owner_type = $rel_type;
                    $rel_owner_id   = $rel_id;
                }
                if ($rel_owner_type == 'Users') {
                    //Checking in Role Users
                    $read_related_role_per = $read_related_per_arr['ROLE'];
                    foreach ($read_related_role_per as $roleid => $userids) {
                        if (in_array($rel_owner_id, $userids)) {
                            $sharePer = 'yes';
                            $log->debug("Exiting isReadPermittedBySharing method ...");
							//file_put_contents('logs/devLog.log', "\n 5", FILE_APPEND);
                            return $sharePer;
                        }
                    }
                    //Checking in Group Users
                    $read_related_grp_per = $read_related_per_arr['GROUP'];
                    foreach ($read_related_grp_per as $grpid => $userids) {
                        if (in_array($rel_owner_id, $userids)) {
                            $sharePer = 'yes';
                            $log->debug("Exiting isReadPermittedBySharing method ...");
							//file_put_contents('logs/devLog.log', "\n 6", FILE_APPEND);
                            return $sharePer;
                        }
                    }
                } elseif ($rel_owner_type == 'Groups') {
                    $read_related_grp_per = $read_related_per_arr['GROUP'];
                    if (array_key_exists($rel_owner_id, $read_related_grp_per)) {
                        $sharePer = 'yes';
                        $log->debug("Exiting isReadPermittedBySharing method ...");
						//file_put_contents('logs/devLog.log', "\n 7", FILE_APPEND);
                        return $sharePer;
                    }
                }
            }
        }
    }
    $log->debug("Exiting isReadPermittedBySharing method ...");
	//file_put_contents('logs/devLog.log', "\n 8", FILE_APPEND);
    return $sharePer;
}

/** Function to check if the currently logged in user has Write Access due to Sharing for the specified record
 *
 * @param $module   -- Module Name:: Type varchar
 * @param $actionid -- Action Id:: Type integer
 * @param $recordid -- Record Id:: Type integer
 * @param $tabid    -- Tab Id:: Type integer
 *
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id)
{
    global $log;
    $log->debug("Entering isReadWritePermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
    global $adb;
    global $current_user;
    require ('include/utils/LoadUserSharingPrivileges.php');
    $ownertype         = '';
    $ownerid           = '';
    $sharePer          = 'no';
    $sharingModuleList = getSharingModuleList();
    if (!in_array($module, $sharingModuleList)) {
        $sharePer = 'no';

        return $sharePer;
    }
    $recordOwnerArr = getRecordOwnerId($record_id);
    foreach ($recordOwnerArr as $type => $id) {
        $ownertype = $type;
        $ownerid   = $id;
    }
    $varname       = $module."_share_write_permission";
    $write_per_arr = $$varname;
    if ($ownertype == 'Users') {
        //Checking the Write Sharing Permission Array in Role Users
        $write_role_per = $write_per_arr['ROLE'];
        foreach ($write_role_per as $roleid => $userids) {
            if (in_array($ownerid, $userids)) {
                $sharePer = 'yes';
                $log->debug("Exiting isReadWritePermittedBySharing method ...");

                return $sharePer;
            }
        }
        //Checking the Write Sharing Permission Array in Groups Users
        $write_grp_per = $write_per_arr['GROUP'];
        foreach ($write_grp_per as $grpid => $userids) {
            if (in_array($ownerid, $userids)) {
                $sharePer = 'yes';
                $log->debug("Exiting isReadWritePermittedBySharing method ...");

                return $sharePer;
            }
        }
    } elseif ($ownertype == 'Groups') {
        $write_grp_per = $write_per_arr['GROUP'];
        if (array_key_exists($ownerid, $write_grp_per)) {
            $sharePer = 'yes';
            $log->debug("Exiting isReadWritePermittedBySharing method ...");

            return $sharePer;
        }
    }
    //Checking for the Related Sharing Permission
    $relatedModuleArray = $related_module_share[$tabid];
    if (is_array($relatedModuleArray)) {
        foreach ($relatedModuleArray as $parModId) {
            $parRecordOwner = getParentRecordOwner($tabid, $parModId, $record_id);
            if (sizeof($parRecordOwner) > 0) {
                $parModName            = getTabname($parModId);
                $rel_var               = $parModName."_".$module."_share_write_permission";
                $write_related_per_arr = $$rel_var;
                $rel_owner_type        = '';
                $rel_owner_id          = '';
                foreach ($parRecordOwner as $rel_type => $rel_id) {
                    $rel_owner_type = $rel_type;
                    $rel_owner_id   = $rel_id;
                }
                if ($rel_owner_type == 'Users') {
                    //Checking in Role Users
                    $write_related_role_per = $write_related_per_arr['ROLE'];
                    foreach ($write_related_role_per as $roleid => $userids) {
                        if (in_array($rel_owner_id, $userids)) {
                            $sharePer = 'yes';
                            $log->debug("Exiting isReadWritePermittedBySharing method ...");

                            return $sharePer;
                        }
                    }
                    //Checking in Group Users
                    $write_related_grp_per = $write_related_per_arr['GROUP'];
                    foreach ($write_related_grp_per as $grpid => $userids) {
                        if (in_array($rel_owner_id, $userids)) {
                            $sharePer = 'yes';
                            $log->debug("Exiting isReadWritePermittedBySharing method ...");

                            return $sharePer;
                        }
                    }
                } elseif ($rel_owner_type == 'Groups') {
                    $write_related_grp_per = $write_related_per_arr['GROUP'];
                    if (array_key_exists($rel_owner_id, $write_related_grp_per)) {
                        $sharePer = 'yes';
                        $log->debug("Exiting isReadWritePermittedBySharing method ...");

                        return $sharePer;
                    }
                }
            }
        }
    }
    $log->debug("Exiting isReadWritePermittedBySharing method ...");

    return $sharePer;
}

/** Function to get the Profile Global Information for the specified vtiger_profileid
 *
 * @param $profileid -- Profile Id:: Type integer
 *
 * @returns Profile Gloabal Permission Array in the following format:
 * $profileGloblaPermisson=Array($viewall_actionid=>permission, $editall_actionid=>permission)
 */
function getProfileGlobalPermission($profileid)
{
    global $log;
    $log->debug("Entering getProfileGlobalPermission(".$profileid.") method ...");
    global $adb;
    $sql      = "SELECT * FROM vtiger_profile2globalpermissions WHERE profileid=?";
    $result   = $adb->pquery($sql, [$profileid]);
    $num_rows = $adb->num_rows($result);
    for ($i = 0; $i < $num_rows; $i++) {
        $act_id        = $adb->query_result($result, $i, "globalactionid");
        $per_id        = $adb->query_result($result, $i, "globalactionpermission");
        $copy[$act_id] = $per_id;
    }
    $log->debug("Exiting getProfileGlobalPermission method ...");

    return $copy;
}

/** Function to get the Profile Tab Permissions for the specified vtiger_profileid
 *
 * @param $profileid -- Profile Id:: Type integer
 *
 * @returns Profile Tabs Permission Array in the following format:
 * $profileTabPermisson=Array($tabid1=>permission, $tabid2=>permission,........., $tabidn=>permission)
 */
function getProfileTabsPermission($profileid)
{
    global $log;
    $log->debug("Entering getProfileTabsPermission(".$profileid.") method ...");
    global $adb;
    $sql      = "SELECT * FROM vtiger_profile2tab WHERE profileid=?";
    $result   = $adb->pquery($sql, [$profileid]);
    $num_rows = $adb->num_rows($result);
    $copy     = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $tab_id        = $adb->query_result($result, $i, "tabid");
        $per_id        = $adb->query_result($result, $i, "permissions");
        $copy[$tab_id] = $per_id;
    }
    // TODO This is temporarily required, till we provide a hook/entry point for Emails module.
    // Once that is done, Webmails need to be removed permanently.
    $emailsTabId   = getTabid('Emails');
    $webmailsTabid = getTabid('Webmails');
    if (array_key_exists($emailsTabId, $copy)) {
        $copy[$webmailsTabid] = $copy[$emailsTabId];
    }
    $log->debug("Exiting getProfileTabsPermission method ...");

    return $copy;
}

/** Function to get the Profile Action Permissions for the specified vtiger_profileid
 *
 * @param $profileid -- Profile Id:: Type integer
 *
 * @returns Profile Tabs Action Permission Array in the following format:
 *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                                |
 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileActionPermission($profileid)
{
    global $log;
    $log->debug("Entering getProfileActionPermission(".$profileid.") method ...");
    global $adb;
    $check      = [];
    $temp_tabid = [];
    $sql1       = "SELECT * FROM vtiger_profile2standardpermissions WHERE profileid=?";
    $result1    = $adb->pquery($sql1, [$profileid]);
    $num_rows1  = $adb->num_rows($result1);
    for ($i = 0; $i < $num_rows1; $i++) {
        $tab_id = $adb->query_result($result1, $i, 'tabid');
        if (!in_array($tab_id, $temp_tabid)) {
            $temp_tabid[] = $tab_id;
            $access       = [];
        }
        $action_id          = $adb->query_result($result1, $i, 'operation');
        $per_id             = $adb->query_result($result1, $i, 'permissions');
        $access[$action_id] = $per_id;
        $check[$tab_id]     = $access;
    }
    $log->debug("Exiting getProfileActionPermission method ...");

    return $check;
}

/** Function to get the Standard and Utility Profile Action Permissions for the specified vtiger_profileid
 *
 * @param $profileid -- Profile Id:: Type integer
 *
 * @returns Profile Tabs Action Permission Array in the following format:
 *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                                |
 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileAllActionPermission($profileid)
{
    global $log;
    $log->debug("Entering getProfileAllActionPermission(".$profileid.") method ...");
    global $adb;
    global $getProfileAllActionPermissionCache;
    global $recreatingUserPrivilegeFiles;
    if ($recreatingUserPrivilegeFiles && isset($getProfileAllActionPermissionCache[$profileid])) {
        return $getProfileAllActionPermissionCache[$profileid];
    }
    $actionArr = getProfileActionPermission($profileid);
    $utilArr   = getTabsUtilityActionPermission($profileid);
    foreach ($utilArr as $tabid => $act_arr) {
        $act_tab_arr = $actionArr[$tabid];
        foreach ($act_arr as $utilid => $util_perr) {
            $act_tab_arr[$utilid] = $util_perr;
        }
        $actionArr[$tabid] = $act_tab_arr;
    }
    $log->debug("Exiting getProfileAllActionPermission method ...");

    if ($recreatingUserPrivilegeFiles) {
        $getProfileAllActionPermissionCache[$profileid] = $actionArr;
    }
    return $actionArr;
}

/** Function to get all  the vtiger_role information
 * @returns $allRoleDetailArray-- Array will contain the details of all the vtiger_roles. RoleId will be the key:: Type array
 */
function getAllRoleDetails()
{
    global $log;
    $log->debug("Entering getAllRoleDetails() method ...");
    global $adb;
    $role_det = [];
    $query    = "SELECT * FROM vtiger_role";
    $result   = $adb->pquery($query, []);
    $num_rows = $adb->num_rows($result);
    for ($i = 0; $i < $num_rows; $i++) {
        $each_role_det = [];
        $roleid        = $adb->query_result($result, $i, 'roleid');
        $rolename      = $adb->query_result($result, $i, 'rolename');
        $roledepth     = $adb->query_result($result, $i, 'depth');
        $sub_roledepth = $roledepth + 1;
        $parentrole    = $adb->query_result($result, $i, 'parentrole');
        $sub_role      = '';
        //getting the immediate subordinates
        $query1    = "SELECT * FROM vtiger_role WHERE parentrole LIKE ? AND depth=?";
        $res1      = $adb->pquery($query1, [$parentrole."::%", $sub_roledepth]);
        $num_roles = $adb->num_rows($res1);
        if ($num_roles > 0) {
            for ($j = 0; $j < $num_roles; $j++) {
                if ($j == 0) {
                    $sub_role .= $adb->query_result($res1, $j, 'roleid');
                } else {
                    $sub_role .= ','.$adb->query_result($res1, $j, 'roleid');
                }
            }
        }
        $each_role_det[]   = $rolename;
        $each_role_det[]   = $roledepth;
        $each_role_det[]   = $sub_role;
        $role_det[$roleid] = $each_role_det;
    }
    $log->debug("Exiting getAllRoleDetails method ...");

    return $role_det;
}

/** Function to get the vtiger_role information of the specified vtiger_role
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleInfoArray-- RoleInfoArray in the following format:
 *                $roleInfo=Array($roleId=>Array($rolename,$parentrole,$roledepth,$immediateParent));
 */
function getRoleInformation($roleid)
{
    global $log;
    $log->debug("Entering getRoleInformation(".$roleid.") method ...");
    global $adb;
    $query             = "SELECT * FROM vtiger_role WHERE roleid=?";
    $result            = $adb->pquery($query, [$roleid]);
    $rolename          = $adb->query_result($result, 0, 'rolename');
    $parentrole        = $adb->query_result($result, 0, 'parentrole');
    $roledepth         = $adb->query_result($result, 0, 'depth');
    $parentRoleArr     = explode('::', $parentrole);
    $immediateParent   = $parentRoleArr[sizeof($parentRoleArr) - 2];
    $roleDet           = [];
    $roleDet[]         = $rolename;
    $roleDet[]         = $parentrole;
    $roleDet[]         = $roledepth;
    $roleDet[]         = $immediateParent;
    $roleInfo          = [];
    $roleInfo[$roleid] = $roleDet;
    $log->debug("Exiting getRoleInformation method ...");

    return $roleInfo;
}

/** Function to get the vtiger_role related vtiger_users
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleUsers-- Role Related User Array in the following format:
 *                $roleUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleUsers($roleId)
{
    global $log;
    $log->debug("Entering getRoleUsers(".$roleId.") method ...");
    global $adb;
    global $getRoleUsersCache;
    global $recreatingUserPrivilegeFiles;
    if ($recreatingUserPrivilegeFiles && isset($getRoleUsersCache[$roleId])) {
        return $getRoleUsersCache[$roleId];
    }
    // could probably check for deleted=0 here as well, but not sure
    $query            = "SELECT vtiger_user2role.*,vtiger_users.* FROM vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid WHERE roleid=?";
    $result           = $adb->pquery($query, [$roleId]);
    $num_rows         = $adb->num_rows($result);
    $roleRelatedUsers = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $roleRelatedUsers[$adb->query_result($result, $i, 'userid')] = getFullNameFromQResult($result, $i, 'Users');
    }
    //God damn it Conrado? Why?
    //$roleRelatedUsers = filterUserAccessibleUsers($roleRelatedUsers);
    $log->debug("Exiting getRoleUsers method ...");

    if ($recreatingUserPrivilegeFiles) {
        $getRoleUsersCache[$roleId] = $roleRelatedUsers;
    }
    return $roleRelatedUsers;
}

/** Function to get the vtiger_role related user ids
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleUserIds-- Role Related User Array in the following format:
 *                $roleUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleUserIds($roleId)
{
    global $log;
    $log->debug("Entering getRoleUserIds(".$roleId.") method ...");
    global $adb;
    $query            = "SELECT vtiger_user2role.*,vtiger_users.user_name FROM vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid WHERE roleid=?";
    $result           = $adb->pquery($query, [$roleId]);
    $num_rows         = $adb->num_rows($result);
    $roleRelatedUsers = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $roleRelatedUsers[] = $adb->query_result($result, $i, 'userid');
    }
    $log->debug("Exiting getRoleUserIds method ...");

    return $roleRelatedUsers;
}

/** Function to get the vtiger_role and subordinate vtiger_users
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubUsers-- Role and Subordinates Related Users Array in the following format:
 *                $roleSubUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleAndSubordinateUsers($roleId)
{
    global $log;
    $log->debug("Entering getRoleAndSubordinateUsers(".$roleId.") method ...");
    global $adb;
    $roleInfoArr      = getRoleInformation($roleId);
    $parentRole       = $roleInfoArr[$roleId][1];
    $query            =
        "SELECT vtiger_user2role.*,vtiger_users.user_name FROM vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE vtiger_role.parentrole LIKE ?";
    $result           = $adb->pquery($query, [$parentRole."%"]);
    $num_rows         = $adb->num_rows($result);
    $roleRelatedUsers = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $roleRelatedUsers[$adb->query_result($result, $i, 'userid')] = $adb->query_result($result, $i, 'user_name');
    }
    $log->debug("Exiting getRoleAndSubordinateUsers method ...");
    $roleRelatedUsers = filterUserAccessibleUsers($roleRelatedUsers);

    return $roleRelatedUsers;
}

/** Function to get the vtiger_role and subordinate Information for the specified vtiger_roleId
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubInfo-- Role and Subordinates Information array in the following format:
 *                $roleSubInfo=Array($roleId1=>Array($rolename,$parentrole,$roledepth,$immediateParent), $roleId2=>Array($rolename,$parentrole,$roledepth,$immediateParent),.....);
 */
function getRoleAndSubordinatesInformation($roleId)
{
    global $log;
    $log->debug("Entering getRoleAndSubordinatesInformation(".$roleId.") method ...");
    global $adb;
    static $roleInfoCache = [];
    if (!empty($roleInfoCache[$roleId])) {
        return $roleInfoCache[$roleId];
    }
    $roleDetails   = getRoleInformation($roleId);
    $roleInfo      = $roleDetails[$roleId];
    $roleParentSeq = $roleInfo[1];
    $query         = "SELECT * FROM vtiger_role WHERE parentrole LIKE ? ORDER BY parentrole ASC";
    $result        = $adb->pquery($query, [$roleParentSeq."%"]);
    $num_rows      = $adb->num_rows($result);
    $roleInfo      = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $roleid            = $adb->query_result($result, $i, 'roleid');
        $rolename          = $adb->query_result($result, $i, 'rolename');
        $roledepth         = $adb->query_result($result, $i, 'depth');
        $parentrole        = $adb->query_result($result, $i, 'parentrole');
        $roleDet           = [];
        $roleDet[]         = $rolename;
        $roleDet[]         = $parentrole;
        $roleDet[]         = $roledepth;
        $roleInfo[$roleid] = $roleDet;
    }
    $roleInfoCache[$roleId] = $roleInfo;
    $log->debug("Exiting getRoleAndSubordinatesInformation method ...");

    return $roleInfo;
}

/** Function to get the vtiger_role and subordinate vtiger_role ids
 *
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubRoleIds-- Role and Subordinates RoleIds in an Array in the following format:
 *                $roleSubRoleIds=Array($roleId1,$roleId2,........,$roleIdn);
 */
function getRoleAndSubordinatesRoleIds($roleId)
{
    global $log;
    $log->debug("Entering getRoleAndSubordinatesRoleIds(".$roleId.") method ...");
    global $adb;
    $roleDetails   = getRoleInformation($roleId);
    $roleInfo      = $roleDetails[$roleId];
    $roleParentSeq = $roleInfo[1];
    $query         = "SELECT * FROM vtiger_role WHERE parentrole LIKE ? ORDER BY parentrole ASC";
    $result        = $adb->pquery($query, [$roleParentSeq."%"]);
    $num_rows      = $adb->num_rows($result);
    $roleInfo      = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $roleid     = $adb->query_result($result, $i, 'roleid');
        $roleInfo[] = $roleid;
    }
    $log->debug("Exiting getRoleAndSubordinatesRoleIds method ...");

    return $roleInfo;
}

/** Function to delete the vtiger_role related sharing rules
 *
 * @param $roleid -- RoleId :: Type varchar
 */
function deleteRoleRelatedSharingRules($roleId)
{
    global $log;
    $log->debug("Entering deleteRoleRelatedSharingRules(".$roleId.") method ...");
    global $adb;
    $dataShareTableColArr = ['vtiger_datashare_grp2role'   => 'to_roleid',
                             'vtiger_datashare_grp2rs'     => 'to_roleandsubid',
                             'vtiger_datashare_role2group' => 'share_roleid',
                             'vtiger_datashare_role2role'  => 'share_roleid::to_roleid',
                             'vtiger_datashare_role2rs'    => 'share_roleid::to_roleandsubid',
                             'vtiger_datashare_rs2grp'     => 'share_roleandsubid',
                             'vtiger_datashare_rs2role'    => 'share_roleandsubid::to_roleid',
                             'vtiger_datashare_rs2rs'      => 'share_roleandsubid::to_roleandsubid'];
    foreach ($dataShareTableColArr as $tablename => $colname) {
        $colNameArr = explode('::', $colname);
        $query      = "SELECT shareid FROM ".$tablename." WHERE ".$colNameArr[0]."=?";
        $params     = [$roleId];
        if (sizeof($colNameArr) > 1) {
            $query .= " or ".$colNameArr[1]."=?";
            array_push($params, $roleId);
        }
        $result   = $adb->pquery($query, $params);
        $num_rows = $adb->num_rows($result);
        for ($i = 0; $i < $num_rows; $i++) {
            $shareid = $adb->query_result($result, $i, 'shareid');
            deleteSharingRule($shareid);
        }
    }
    $log->debug("Exiting deleteRoleRelatedSharingRules method ...");
}

/** Function to delete the group related sharing rules
 *
 * @param $roleid -- RoleId :: Type varchar
 */
function deleteGroupRelatedSharingRules($grpId)
{
    global $log;
    $log->debug("Entering deleteGroupRelatedSharingRules(".$grpId.") method ...");
    global $adb;
    $dataShareTableColArr = ['vtiger_datashare_grp2grp'    => 'share_groupid::to_groupid',
                             'vtiger_datashare_grp2role'   => 'share_groupid',
                             'vtiger_datashare_grp2rs'     => 'share_groupid',
                             'vtiger_datashare_role2group' => 'to_groupid',
                             'vtiger_datashare_rs2grp'     => 'to_groupid'];
    foreach ($dataShareTableColArr as $tablename => $colname) {
        $colNameArr = explode('::', $colname);
        $query      = "SELECT shareid FROM ".$tablename." WHERE ".$colNameArr[0]."=?";
        $params     = [$grpId];
        if (sizeof($colNameArr) > 1) {
            $query .= " or ".$colNameArr[1]."=?";
            array_push($params, $grpId);
        }
        $result   = $adb->pquery($query, $params);
        $num_rows = $adb->num_rows($result);
        for ($i = 0; $i < $num_rows; $i++) {
            $shareid = $adb->query_result($result, $i, 'shareid');
            deleteSharingRule($shareid);
        }
    }
    $log->debug("Exiting deleteGroupRelatedSharingRules method ...");
}

/** Function to get userid and username of all vtiger_users
 * @returns $userArray -- User Array in the following format:
 * $userArray=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getAllUserName()
{
    global $log;
    $log->debug("Entering getAllUserName() method ...");
    global $adb;
    $query        = "SELECT * FROM vtiger_users WHERE deleted=0";
    $result       = $adb->pquery($query, []);
    $num_rows     = $adb->num_rows($result);
    $user_details = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $userid                = $adb->query_result($result, $i, 'id');
        $username              = getFullNameFromQResult($result, $i, 'Users');
        $user_details[$userid] = $username;
    }
    $log->debug("Exiting getAllUserName method ...");

    return $user_details;
}

/** Function to get groupid and groupname of all vtiger_groups
 * @returns $grpArray -- Group Array in the following format:
 * $grpArray=Array($grpid1=>$grpname, $grpid2=>$grpname,............,$grpidn=>$grpname);
 */
function getAllGroupName()
{
    global $log;
    $log->debug("Entering getAllGroupName() method ...");
    global $adb;
    $query         = "SELECT * FROM vtiger_groups";
    $result        = $adb->pquery($query, []);
    $num_rows      = $adb->num_rows($result);
    $group_details = [];
    for ($i = 0; $i < $num_rows; $i++) {
        $grpid                 = $adb->query_result($result, $i, 'groupid');
        $grpname               = $adb->query_result($result, $i, 'groupname');
        $group_details[$grpid] = $grpname;
    }
    $log->debug("Exiting getAllGroupName method ...");

    return $group_details;
}

/** This function is to delete the organisation level sharing rule
 * It takes the following input parameters:
 *     $shareid -- Id of the Sharing Rule to be updated
 */
function deleteSharingRule($shareid)
{
    global $log;
    $log->debug("Entering deleteSharingRule(".$shareid.") method ...");
    global $adb;
    $query2  = "SELECT * FROM vtiger_datashare_module_rel WHERE shareid=?";
    $res     = $adb->pquery($query2, [$shareid]);
    $typestr = $adb->query_result($res, 0, 'relationtype');
    $tabname = getDSTableNameForType($typestr);
    $query3  = "delete from $tabname where shareid=?";
    $adb->pquery($query3, [$shareid]);
    $query4 = "DELETE FROM vtiger_datashare_module_rel WHERE shareid=?";
    $adb->pquery($query4, [$shareid]);
    //deleting the releated module sharing permission
    $query5 = "DELETE FROM vtiger_datashare_relatedmodule_permission WHERE shareid=?";
    $adb->pquery($query5, [$shareid]);
    $log->debug("Exiting deleteSharingRule method ...");
}

/** Function get the Data Share Table Names
 * @returns the following Date Share Table Name Array:
 *  $dataShareTableColArr=Array('GRP::GRP'=>'datashare_grp2grp',
 *                    'GRP::ROLE'=>'datashare_grp2role',
 *                    'GRP::RS'=>'datashare_grp2rs',
 *                    'ROLE::GRP'=>'datashare_role2group',
 *                    'ROLE::ROLE'=>'datashare_role2role',
 *                    'ROLE::RS'=>'datashare_role2rs',
 *                    'RS::GRP'=>'datashare_rs2grp',
 *                    'RS::ROLE'=>'datashare_rs2role',
 *                    'RS::RS'=>'datashare_rs2rs');
 */
function getDataShareTableName()
{
    global $log;
    $log->debug("Entering getDataShareTableName() method ...");
    $dataShareTableColArr = ['GRP::GRP'   => 'vtiger_datashare_grp2grp',
                             'GRP::ROLE'  => 'vtiger_datashare_grp2role',
                             'GRP::RS'    => 'vtiger_datashare_grp2rs',
                             'ROLE::GRP'  => 'vtiger_datashare_role2group',
                             'ROLE::ROLE' => 'vtiger_datashare_role2role',
                             'ROLE::RS'   => 'vtiger_datashare_role2rs',
                             'RS::GRP'    => 'vtiger_datashare_rs2grp',
                             'RS::ROLE'   => 'vtiger_datashare_rs2role',
                             'RS::RS'     => 'vtiger_datashare_rs2rs'];
    $log->debug("Exiting getDataShareTableName method ...");

    return $dataShareTableColArr;
}

/** Function to get the Data Share Table Name from the speciified type string
 *
 * @param $typeString -- Datashare Type Sting :: Type Varchar
 *
 * @returns Table Name -- Type Varchar
 */
function getDSTableNameForType($typeString)
{
    global $log;
    $log->debug("Entering getDSTableNameForType(".$typeString.") method ...");
    $dataShareTableColArr = getDataShareTableName();
    $tableName            = $dataShareTableColArr[$typeString];
    $log->debug("Exiting getDSTableNameForType method ...");

    return $tableName;
}

/** This function is to retreive the vtiger_profiles associated with the  the specified user
 * It takes the following input parameters:
 *     $userid -- The User Id:: Type Integer
 *This function will return the vtiger_profiles associated to the specified vtiger_users in an Array in the following format:
 *     $userProfileArray=(profileid1,profileid2,profileid3,...,profileidn);
 */
function getUserProfile($userId)
{
    global $log;
    $log->debug("Entering getUserProfile(".$userId.") method ...");
    global $adb;
    $roleId   = fetchUserRole($userId);
    $profArr  = [];
    $sql1     = "SELECT profileid FROM vtiger_role2profile WHERE roleid=?";
    $result1  = $adb->pquery($sql1, [$roleId]);
    $num_rows = $adb->num_rows($result1);
    for ($i = 0; $i < $num_rows; $i++) {
        $profileid = $adb->query_result($result1, $i, "profileid");
        $profArr[] = $profileid;
    }
    $log->debug("Exiting getUserProfile method ...");

    return $profArr;
}

/** To retreive the global permission of the specifed user from the various vtiger_profiles associated with the user
 *
 * @param $userid -- The User Id:: Type Integer
 *
 * @returns  user global permission  array in the following format:
 *     $gloabalPerrArray=(view all action id=>permission,
 * edit all action id=>permission)                            );
 */
function getCombinedUserGlobalPermissions($userId)
{
    global $log;
    $log->debug("Entering getCombinedUserGlobalPermissions(".$userId.") method ...");
    global $adb;
    $profArr           = getUserProfile($userId);
    $no_of_profiles    = sizeof($profArr);
    $userGlobalPerrArr = [];
    $userGlobalPerrArr = getProfileGlobalPermission($profArr[0]);
    if ($no_of_profiles != 1) {
        for ($i = 1; $i < $no_of_profiles; $i++) {
            $tempUserGlobalPerrArr = getProfileGlobalPermission($profArr[$i]);
            foreach ($userGlobalPerrArr as $globalActionId => $globalActionPermission) {
                if ($globalActionPermission == 1) {
                    $now_permission = $tempUserGlobalPerrArr[$globalActionId];
                    if ($now_permission == 0) {
                        $userGlobalPerrArr[$globalActionId] = $now_permission;
                    }
                }
            }
        }
    }
    $log->debug("Exiting getCombinedUserGlobalPermissions method ...");

    return $userGlobalPerrArr;
}

/** To retreive the vtiger_tab permissions of the specifed user from the various vtiger_profiles associated with the user
 *
 * @param $userid -- The User Id:: Type Integer
 *
 * @returns  user global permission  array in the following format:
 *     $tabPerrArray=(tabid1=>permission,
 *               tabid2=>permission)                            );
 */
function getCombinedUserTabsPermissions($userId)
{
    global $log;
    $log->debug("Entering getCombinedUserTabsPermissions(".$userId.") method ...");
    global $adb;
    $profArr        = getUserProfile($userId);
    $no_of_profiles = sizeof($profArr);
    $userTabPerrArr = [];
    $userTabPerrArr = getProfileTabsPermission($profArr[0]);
    if ($no_of_profiles != 1) {
        for ($i = 1; $i < $no_of_profiles; $i++) {
            $tempUserTabPerrArr = getProfileTabsPermission($profArr[$i]);
            foreach ($userTabPerrArr as $tabId => $tabPermission) {
                if ($tabPermission == 1) {
                    $now_permission = $tempUserTabPerrArr[$tabId];
                    if ($now_permission == 0) {
                        $userTabPerrArr[$tabId] = $now_permission;
                    }
                }
            }
        }
    }
    $homeTabid = getTabid('Home');
    if (!array_key_exists($homeTabid, $userTabPerrArr)) {
        $userTabPerrArr[$homeTabid] = 0;
    }
    $log->debug("Exiting getCombinedUserTabsPermissions method ...");

    return $userTabPerrArr;
}

/** To retreive the vtiger_tab acion permissions of the specifed user from the various vtiger_profiles associated with the user
 *
 * @param $userid -- The User Id:: Type Integer
 *
 * @returns  user global permission  array in the following format:
 *     $actionPerrArray=(tabid1=>permission,
 *               tabid2=>permission);
 */
function getCombinedUserActionPermissions($userId)
{
    global $log;
    $log->debug("Entering getCombinedUserActionPermissions(".$userId.") method ...");
    global $adb;
    $profArr        = getUserProfile($userId);
    $no_of_profiles = sizeof($profArr);
    $actionPerrArr  = [];
    $actionPerrArr  = getProfileAllActionPermission($profArr[0]);
    if ($no_of_profiles != 1) {
        for ($i = 1; $i < $no_of_profiles; $i++) {
            $tempActionPerrArr = getProfileAllActionPermission($profArr[$i]);
            foreach ($actionPerrArr as $tabId => $perArr) {
                foreach ($perArr as $actionid => $per) {
                    if ($per == 1) {
                        $now_permission = $tempActionPerrArr[$tabId][$actionid];
                        if ($now_permission == 0 && $now_permission != "") {
                            $actionPerrArr[$tabId][$actionid] = $now_permission;
                        }
                    }
                }
            }
        }
    }
    $log->debug("Exiting getCombinedUserActionPermissions method ...");

    return $actionPerrArr;
}

/** To retreive the parent vtiger_role of the specified vtiger_role
 *
 * @param $roleid -- The Role Id:: Type varchar
 *
 * @returns  parent vtiger_role array in the following format:
 *     $parentRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getParentRole($roleId)
{
    global $log;
    $log->debug("Entering getParentRole(".$roleId.") method ...");
    $roleInfo          = getRoleInformation($roleId);
    $parentRole        = $roleInfo[$roleId][1];
    $tempParentRoleArr = explode('::', $parentRole);
    $parentRoleArr     = [];
    foreach ($tempParentRoleArr as $role_id) {
        if ($role_id != $roleId) {
            $parentRoleArr[] = $role_id;
        }
    }
    $log->debug("Exiting getParentRole method ...");

    return $parentRoleArr;
}

/** To retreive the subordinate vtiger_roles of the specified parent vtiger_role
 *
 * @param $roleid -- The Role Id:: Type varchar
 *
 * @returns  subordinate vtiger_role array in the following format:
 *     $subordinateRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getRoleSubordinates($roleId)
{
    global $log;
    $log->debug("Entering getRoleSubordinates(".$roleId.") method ...");
    // Look at cache first for information
    $roleSubordinates = VTCacheUtils::lookupRoleSubordinates($roleId);
    if ($roleSubordinates === false) {
        global $adb;
        $roleDetails      = getRoleInformation($roleId);
        $roleInfo         = $roleDetails[$roleId];
        $roleParentSeq    = $roleInfo[1];
        $query            = "SELECT * FROM vtiger_role WHERE parentrole LIKE ? ORDER BY parentrole ASC";
        $result           = $adb->pquery($query, [$roleParentSeq."::%"]);
        $num_rows         = $adb->num_rows($result);
        $roleSubordinates = [];
        for ($i = 0; $i < $num_rows; $i++) {
            $roleid             = $adb->query_result($result, $i, 'roleid');
            $roleSubordinates[] = $roleid;
        }
        // Update cache for re-use
        VTCacheUtils::updateRoleSubordinates($roleId, $roleSubordinates);
    }
    $log->debug("Exiting getRoleSubordinates method ...");

    return $roleSubordinates;
}

/** To retreive the subordinate vtiger_roles and vtiger_users of the specified parent vtiger_role
 *
 * @param $roleid -- The Role Id:: Type varchar
 *
 * @returns  subordinate vtiger_role array in the following format:
 *     $subordinateRoleUserArray=(roleid1=>Array(userid1,userid2,userid3),
 * vtiger_roleid2=>Array(userid1,userid2,userid3)
 * |
 * |
 * vtiger_roleidn=>Array(userid1,userid2,userid3));
 */
function getSubordinateRoleAndUsers($roleId)
{
    global $log;
    $log->debug("Entering getSubordinateRoleAndUsers(".$roleId.") method ...");
    global $adb;
    $subRoleAndUsers  = [];
    $subordinateRoles = getRoleSubordinates($roleId);
    array_unshift($subordinateRoles, $roleId);
    foreach ($subordinateRoles as $subRoleId) {
        $userArray                   = getRoleUsers($subRoleId);
        $subRoleAndUsers[$subRoleId] = $userArray;
    }
    $log->debug("Exiting getSubordinateRoleAndUsers method ...");

    return $subRoleAndUsers;
}

function getCurrentUserProfileList()
{
    global $log;
    $log->debug("Entering getCurrentUserProfileList() method ...");
    global $current_user;
    require ('include/utils/LoadUserPrivileges.php');
    $profList = [];
    $i        = 0;
    foreach ($current_user_profiles as $profid) {
        array_push($profList, $profid);
        $i++;
    }
    $log->debug("Exiting getCurrentUserProfileList method ...");

    return $profList;
}

function getCurrentUserGroupList()
{
    global $log;
    $log->debug("Entering getCurrentUserGroupList() method ...");
    global $current_user;
    require ('include/utils/LoadUserPrivileges.php');
    $grpList = [];
    if (sizeof($current_user_groups) > 0) {
        $i = 0;
        foreach ($current_user_groups as $grpid) {
            array_push($grpList, $grpid);
            $i++;
        }
    }
    $log->debug("Exiting getCurrentUserGroupList method ...");

    return $grpList;
}

function getWriteSharingGroupsList($module)
{
    global $log;
    $log->debug("Entering getWriteSharingGroupsList(".$module.") method ...");
    global $adb;
    global $current_user;
    $grp_array = [];
    $tabid     = getTabid($module);
    $query     = "SELECT sharedgroupid FROM vtiger_tmp_write_group_sharing_per WHERE userid=? AND tabid=?";
    $result    = $adb->pquery($query, [$current_user->id, $tabid]);
    $num_rows  = $adb->num_rows($result);
    for ($i = 0; $i < $num_rows; $i++) {
        $grp_id      = $adb->query_result($result, $i, 'sharedgroupid');
        $grp_array[] = $grp_id;
    }
    $shareGrpList = constructList($grp_array, 'INTEGER');
    $log->debug("Exiting getWriteSharingGroupsList method ...");

    return $shareGrpList;
}

function constructList($array, $data_type)
{
    global $log;
    $log->debug("Entering constructList(".$array.",".$data_type.") method ...");
    $list = [];
    if (sizeof($array) > 0) {
        $i = 0;
        foreach ($array as $value) {
            if ($data_type == "INTEGER") {
                array_push($list, $value);
            } elseif ($data_type == "VARCHAR") {
                array_push($list, "'".$value."'");
            }
            $i++;
        }
    }
    $log->debug("Exiting constructList method ...");

    return $list;
}

function getListViewSecurityParameter($module)
{
    global $log;
    $log->debug("Entering getListViewSecurityParameter(".$module.") method ...");
    global $adb;
    $tabid = getTabid($module);
    global $current_user;
    if ($current_user) {
        require ('include/utils/LoadUserPrivileges.php');
        require ('include/utils/LoadUserSharingPrivileges.php');
    }
    if ($module == 'Leads') {
        $sec_query .= " and (
						vtiger_crmentity.smownerid in($current_user->id)
						or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%')
						or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")
						or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'Accounts') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) ".
                      "or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') ".
                      "or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'Contacts') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) ".
                      "or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') ".
                      "or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'Potentials') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) ".
                      "or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') ".
                      "or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")";
        $sec_query .= " or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'HelpDesk') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") ";
        $sec_query .= " or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'Emails') {
        $sec_query .= " and vtiger_crmentity.smownerid=".$current_user->id." ";
    } elseif ($module == 'Calendar') {
        require_once('modules/Calendar/CalendarCommon.php');
        $shared_ids = getSharedCalendarId($current_user->id);
        if (isset($shared_ids) && $shared_ids != '') {
            $condition = " or (vtiger_crmentity.smownerid in($shared_ids) and vtiger_activity.visibility = 'Public')";
        } else {
            $condition = null;
        }
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) $condition or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%')";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " or ((vtiger_groups.groupid in (".implode(",", $current_user_groups).")))";
        }
        $sec_query .= ")";
    } elseif ($module == 'Quotes') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")";
        //Adding crteria for group sharing
        $sec_query .= " or ((";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")))) ";
    } elseif ($module == 'PurchaseOrder') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'SalesOrder') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")";
        //Adding crteria for group sharing
        $sec_query .= " or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } elseif ($module == 'Invoice') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")";
        //Adding crteria for group sharing
        $sec_query .= " or ((";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")))) ";
    } elseif ($module == 'Campaigns') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") or ((";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")))) ";
    } elseif ($module == 'Documents') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ") or ((";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")))) ";
    } elseif ($module == 'Products') {
        $sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) ".
                      "or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".
                      $current_user_parent_role_seq.
                      "::%') ".
                      "or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      ")";
        $sec_query .= " or (";
        if (sizeof($current_user_groups) > 0) {
            $sec_query .= " vtiger_groups.groupid in (".implode(",", $current_user_groups).") or ";
        }
        $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".
                      $current_user->id.
                      " and tabid=".
                      $tabid.
                      "))) ";
    } else {
        $modObj    = CRMEntity::getInstance($module);
        $sec_query = $modObj->getListViewSecurityParameter($module);
    }
    $log->debug("Exiting getListViewSecurityParameter method ...");

    return $sec_query;
}

function get_current_user_access_groups($module)
{
    global $log;
    $log->debug("Entering get_current_user_access_groups(".$module.") method ...");
    global $adb, $noof_group_rows;
    $current_user_group_list  = getCurrentUserGroupList();
    $sharing_write_group_list = getWriteSharingGroupsList($module);
    $query                    = "SELECT groupname,groupid FROM vtiger_groups";
    $params                   = [];
    if (count($current_user_group_list) > 0 && count($sharing_write_group_list) > 0) {
        $query .= " where (groupid in (".generateQuestionMarks($current_user_group_list).") or groupid in (".generateQuestionMarks($sharing_write_group_list)."))";
        array_push($params, $current_user_group_list, $sharing_write_group_list);
        $result          = $adb->pquery($query, $params);
        $noof_group_rows = $adb->num_rows($result);
    } elseif (count($current_user_group_list) > 0) {
        $query .= " where groupid in (".generateQuestionMarks($current_user_group_list).")";
        array_push($params, $current_user_group_list);
        $result          = $adb->pquery($query, $params);
        $noof_group_rows = $adb->num_rows($result);
    } elseif (count($sharing_write_group_list) > 0) {
        $query .= " where groupid in (".generateQuestionMarks($sharing_write_group_list).")";
        array_push($params, $sharing_write_group_list);
        $result          = $adb->pquery($query, $params);
        $noof_group_rows = $adb->num_rows($result);
    }
    $log->debug("Exiting get_current_user_access_groups method ...");

    return $result;
}

/** Function to get the Group Id for a given group groupname
 *
 * @param $groupname -- Groupname
 *
 * @returns Group Id -- Type Integer
 */
function getGrpId($groupname)
{
    global $log;
    $log->debug("Entering getGrpId(".$groupname.") method ...");
    global $adb;
    $groupid = Vtiger_Cache::get('group', $groupname);
    if (!$groupid && $groupid !== 0) {
        $result  = $adb->pquery("SELECT groupid FROM vtiger_groups WHERE groupname=?", [$groupname]);
        $groupid = ($adb->num_rows($result) > 0)?$adb->query_result($result, 0, 'groupid'):0;
        Vtiger_Cache::set('group', $groupname, $groupid);
    }
    $log->debug("Exiting getGrpId method ...");

    return $groupid;
}

/** Function to check permission to access a vtiger_field for a given user
 *
 * @param $fld_module -- Module :: Type String
 * @param $userid     -- User Id :: Type integer
 * @param $fieldname  -- Field Name :: Type varchar
 * @returns $rolename -- Role Name :: Type varchar
 */
function getFieldVisibilityPermission($fld_module, $userid, $fieldname, $accessmode = 'readonly')
{
    global $log;
    $log->debug("Entering getFieldVisibilityPermission(".$fld_module.",".$userid.",".$fieldname.") method ...");
    global $adb;
    global $current_user;
    // Check if field is in-active
    $fieldActive = isFieldActive($fld_module, $fieldname);
    if ($fieldActive == false) {
        return '1';
    }
    $currentUserId = $userid;
    require ('include/utils/LoadUserPrivileges.php');
    /* Asha: Fix for ticket #4508. Users with View all and Edit all permission will also have visibility permission for all fields */
    if ($is_admin || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
        $log->debug("Exiting getFieldVisibilityPermission method ...");

        return '0';
    } else {
        //get vtiger_profile list using userid
        $profilelist = getCurrentUserProfileList();
        //get tabid
        $tabid = getTabid($fld_module);
        if (count($profilelist) > 0) {
            if ($accessmode == 'readonly') {
                $query =
                    "SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid IN (".
                    generateQuestionMarks($profilelist).
                    ") AND vtiger_field.fieldname= ? AND vtiger_field.presence IN (0,2) GROUP BY vtiger_field.fieldid";
            } else {
                $query =
                    "SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid IN (".
                    generateQuestionMarks($profilelist).
                    ") AND vtiger_field.fieldname= ? AND vtiger_field.presence IN (0,2) GROUP BY vtiger_field.fieldid";
            }
            $params = [$tabid, $profilelist, $fieldname];
        } else {
            if ($accessmode == 'readonly') {
                $query =
                    "SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.fieldname= ? AND vtiger_field.presence IN (0,2) GROUP BY vtiger_field.fieldid";
            } else {
                $query =
                    "SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.fieldname= ? AND vtiger_field.presence IN (0,2) GROUP BY vtiger_field.fieldid";
            }
            $params = [$tabid, $fieldname];
        }
        //Postgres 8 fixes
        if ($adb->dbType == "pgsql") {
            $query = fixPostgresQuery($query, $log, 0);
        }
        $result = $adb->pquery($query, $params);
        $log->debug("Exiting getFieldVisibilityPermission method ...");
        // Returns value as a string
        if ($adb->num_rows($result) == 0) {
            return '1';
        }

        return ($adb->query_result($result, "0", "visible")."");
    }
}

/** Function to check permission to access the column for a given user
 *
 * @param $userid     -- User Id :: Type integer
 * @param $tablename  -- tablename :: Type String
 * @param $columnname -- columnname :: Type String
 * @param $module     -- Module Name :: Type varchar
 */
function getColumnVisibilityPermission($userid, $columnname, $module, $accessmode = 'readonly')
{
    global $adb, $log;
    $log->debug("in function getcolumnvisibilitypermission $columnname -$userid");
    $tabid = getTabid($module);
    // Look at cache if information is available.
    $cacheFieldInfo = VTCacheUtils::lookupFieldInfoByColumn($tabid, $columnname);
    $fieldname      = false;
    if ($cacheFieldInfo === false) {
        $res       = $adb->pquery("SELECT fieldname FROM vtiger_field WHERE tabid=? AND columnname=? AND vtiger_field.presence IN (0,2)", [$tabid, $columnname]);
        $fieldname = $adb->query_result($res, 0, 'fieldname');
    } else {
        $fieldname = $cacheFieldInfo['fieldname'];
    }

    return getFieldVisibilityPermission($module, $userid, $fieldname, $accessmode);
}

/** Function to get the permitted module name Array with presence as 0
 * @returns permitted module name Array :: Type Array
 */
function getPermittedModuleNames()
{
    global $log;
    $log->debug("Entering getPermittedModuleNames() method ...");
    global $current_user;
    $permittedModules = [];
    if (!$current_user->id && method_exists($current_user, 'getId')) {
        //@TODO: this might need done elsewhere.
        $current_user->id = $current_user->getId();
    }
    require ('include/utils/LoadUserPrivileges.php');
    include('tabdata.php');
    if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
        foreach ($tab_seq_array as $tabid => $seq_value) {
            if ($seq_value === 0 && $profileTabsPermission[$tabid] === 0) {
                $permittedModules[] = getTabModuleName($tabid);
            }
        }
    } else {
        foreach ($tab_seq_array as $tabid => $seq_value) {
            if ($seq_value === 0) {
                $permittedModules[] = getTabModuleName($tabid);
            }
        }
    }
    $log->debug("Exiting getPermittedModuleNames method ...");

    return $permittedModules;
}

/**
 * Function to get the permitted module id Array with presence as 0
 * @global Users $current_user
 * @return Array Array of accessible tabids.
 */
function getPermittedModuleIdList()
{
    global $current_user;
    $permittedModules = [];
    require ('include/utils/LoadUserPrivileges.php');
    include('tabdata.php');
    if ($is_admin == false && $profileGlobalPermission[1] == 1 &&
        $profileGlobalPermission[2] == 1
    ) {
        foreach ($tab_seq_array as $tabid => $seq_value) {
            if ($seq_value === 0 && $profileTabsPermission[$tabid] === 0) {
                $permittedModules[] = ($tabid);
            }
        }
    } else {
        foreach ($tab_seq_array as $tabid => $seq_value) {
            if ($seq_value === 0) {
                $permittedModules[] = ($tabid);
            }
        }
    }
    $homeTabid = getTabid('Home');
    if (!in_array($homeTabid, $permittedModules)) {
        $permittedModules[] = $homeTabid;
    }

    return $permittedModules;
}

/** Function to recalculate the Sharing Rules for all the vtiger_users
 * This function will recalculate all the sharing rules for all the vtiger_users in the Organization and will write them in flat vtiger_files
 */
function RecalculateSharingRules()
{
    global $log;
    $log->debug("Entering RecalculateSharingRules() method ...");
    global $adb;
    require_once('modules/Users/CreateUserPrivilegeFile.php');
    $query    = "SELECT id FROM vtiger_users WHERE deleted=0";
    $result   = $adb->pquery($query, []);
    $num_rows = $adb->num_rows($result);
    for ($i = 0; $i < $num_rows; $i++) {
        $id = $adb->query_result($result, $i, 'id');
        createUserPrivilegesfile($id);
        createUserSharingPrivilegesfile($id);
    }
    $log->debug("Exiting RecalculateSharingRules method ...");
}

/** Function to get the list of module for which the user defined sharing rules can be defined
 * @returns Array:: Type array
 */
function getSharingModuleList($eliminateModules = false)
{
    global $log;
    $sharingModuleArray = [];
    global $adb;
    if (empty($eliminateModules)) {
        $eliminateModules = [];
    }
    // Module that needs to be eliminated explicitly
    if (!in_array('Calendar', $eliminateModules)) {
        $eliminateModules[] = 'Calendar';
    }
    if (!in_array('Events', $eliminateModules)) {
        $eliminateModules[] = 'Events';
    }
    $query = "SELECT name FROM vtiger_tab WHERE presence=0 AND ownedby = 0 AND isentitytype = 1";
    $query .= " AND name NOT IN('".implode("','", $eliminateModules)."')";
    $result = $adb->query($query);
    while ($resrow = $adb->fetch_array($result)) {
        $sharingModuleArray[] = $resrow['name'];
    }

    return $sharingModuleArray;
}

function isCalendarPermittedByRoleDepth($record_id,$actionname){
    global $adb;
    $allActionsCalendar = array('Edit','EditView','Save','SaveAjax','View','DetailView','Delete');
    $changActionsCalendar = array('Edit','EditView','Save','SaveAjax','Delete');
    $watchActionsCalendar = array('View','DetailView');
    $currentUserModel = Users_Record_Model::getCurrentUserModel();

    $permission = Vtiger_Cache::get('calendar_permissions_' . $currentUserModel->id, $record_id . '_' . $actionname);

    if(empty($permission)){
    $permission = 'no';
        $accesibleAgents =  $currentUserModel->getAccessibleOwnersForUser('Calendar', true, true);
        unset($accesibleAgents['agents']);
        unset($accesibleAgents['vanlines']);
        $members = getMembersByRecord(array_keys($accesibleAgents));

        $ownerId = getRecordOwnerId($record_id);
        $userOwner = $ownerId['Users'];
        $groupOwner = $ownerId['Groups'];
        $accessibleGroups = $currentUserModel->getUserGroups($currentUserModel->id);

        if($userOwner != '' && in_array($userOwner, $members) && in_array($actionname, $watchActionsCalendar)){
            $permission = 'yes'; //Public for readonly
        }

        if($groupOwner !='' && in_array($groupOwner, $accessibleGroups) && in_array($actionname, $watchActionsCalendar)){
            $permission = 'yes'; //Public for readonly
        }

        if (in_array($actionname, $changActionsCalendar)){
            $accesibleUsersByProfile = array_keys($currentUserModel->getSameLevelUsersWithSubordinates());
            $accesibleUsers = array_intersect($accesibleUsersByProfile, $members);
            $accesibleIds =  array_merge($accesibleUsers,$accessibleGroups);

            $sql = 'SELECT 1 FROM vtiger_crmentity WHERE crmid=? AND smownerid IN ( '. generateQuestionMarks($accesibleIds).')';
            $res = $adb->pquery($sql, array($record_id, $accesibleIds));
            if($adb->num_rows($res) > 0){
            $permission = 'yes';
            }else{
            $permission = 'no';
        }
    }

        Vtiger_Cache::set('calendar_permissions_' . $currentUserModel->id, $record_id . '_' . $actionname, $permission);
    }
    return $permission;
}

/** Function to check if the field is Active
 * @params  $modulename -- Module Name :: String Type
 *         $fieldname  -- Field Name  :: String Type
 */
function isFieldActive($modulename, $fieldname)
{
    $fieldid = getFieldid(getTabid($modulename), $fieldname, true);

    return ($fieldid !== false);
}

/**
 * @param String $module - module name for which query needs to be generated.
 * @param Users  $user   - user for which query needs to be generated.
 *
 * @return String Access control Query for the user.
 */
function getNonAdminAccessControlQuery($module, $user, $scope = '')
{
    $instance = CRMEntity::getInstance($module);

    return $instance->getNonAdminAccessControlQuery($module, $user, $scope);
}

function appendFromClauseToQuery($query, $fromClause)
{
    $query     = preg_replace('/\s+/', ' ', $query);
    $condition = substr($query, strripos($query, ' where '), strlen($query));
    $newQuery  = substr($query, 0, strripos($query, ' where '));
    $query     = $newQuery.$fromClause.$condition;

    return $query;
}

function filterUserAccessibleUsers($usersArray)
{
    $currentUser = Users_Record_Model::getCurrentUserModel();
    /*if($currentUser->isVanLineUser()) {
        $accessibleAgents = $currentUser->getVanlineUserAccessibleAgents();
        $accessibleVanlines = $currentUser->getAccessibleVanlinesForUser();
        $accessible = $currentUser->AssocArrayMerge($accessibleAgents, $accessibleVanlines);
        //@NOTE if accessible isn't what you expected it's this, I extrapolated the fix.
        //$accessible = array_flip($accessible);
        $accessible         = array_keys($accessible);
        //file_put_contents("logs/devLog.log", "\n getVanlineUserAccessibleAgents: ".print_r($accessibleAgents, true), FILE_APPEND);
    } else{
        $accessible = explode(' |##| ', $currentUser->get('agent_ids'));
    }*/
	$accessible = getPermittedAccessible();
    //file_put_contents("logs/devLog.log", "\n accessible user filter: ".print_r($accessible, true), FILE_APPEND);
    if (is_array($accessible) && !$currentUser->isAdminUser()) {
        $accessibleUser = Vtiger_Cache::get('vtiger-multi-agent', 'accessibleusers-'.$currentUser->id);
        if (empty($accessibleUser)) {
            $db               = PearDatabase::getInstance();
            // All users should be able to assign to all levels. Commenting limiters.
            //$currentUserDepth = getRoleDepth($currentUser->getRole());
            $ownerList        = '('.implode(',', $accessible).')';
            $query            = "SELECT id FROM vtiger_users
			INNER JOIN vtiger_user2role ON `vtiger_users`.id = `vtiger_user2role`.userid
			INNER JOIN vtiger_role ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE (agent_ids IN $ownerList";
            foreach ($accessible as $name => $id) {
                $query .= " OR agent_ids LIKE '% $id' OR agent_ids LIKE '% $id %' OR agent_ids LIKE '$id %'";
            }
            $query .= ")";
            //Remove above line and uncomment below line to restore limiter.
            //$query .= ") AND `vtiger_role`.depth >= $currentUserDepth";
            $result = $db->pquery($query);
            //file_put_contents('logs/devLog.log', "\n Agent field query: ".print_r($result, true), FILE_APPEND);
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $accessibleUsers[] = $row['id'];
                }
                Vtiger_Cache::set('vtiger-multi-agent', 'accesible_users-'.$currentUser->id, $accessibleUsers);
            }
        }
        //file_put_contents("logs/devLog.log", "\n accessible users: ".print_r($accessibleUsers, true), FILE_APPEND);
        if (!empty($accessibleUsers) && is_array($accessibleUsers)) {
            foreach ($usersArray as $userId => $userName) {
                if (!in_array($userId, $accessibleUsers)) {
                    unset($usersArray[$userId]);
                }
            }
        }
    }

    return $usersArray;
}

function getSubordinateUsers()
{
	//gets users with accessible agent set
    $currentUser      = Users_Record_Model::getCurrentUserModel();
    $db               = PearDatabase::getInstance();
    $currentUserDepth = getRoleDepth($currentUser->getRole());
	$userAccessible   = getPermittedAccessible();
    $ownerList        = '('.implode(',', $userAccessible).')';
    $query            = "SELECT id FROM vtiger_users
	INNER JOIN vtiger_user2role ON `vtiger_users`.id = `vtiger_user2role`.userid
	INNER JOIN vtiger_role ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE (agent_ids IN $ownerList";
    foreach ($userAccessible as $agentId) {
        $query .= " OR agent_ids LIKE '% $agentId' OR agent_ids LIKE '% $agentId %' OR agent_ids LIKE '$agentId %'";
    }
    // Added check to allow admin records through if they are tied to the parent record.
    $query .= ") AND `vtiger_role`.depth > $currentUserDepth OR `vtiger_role`.depth = 1";
    $result = $db->pquery($query);
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetchByAssoc($result)) {
            $accessibleUsers[] = $row['id'];
        }
    }
    return $accessibleUsers;
}

function getRecordAgentOwner($recordId)
{
    global $adb;
    $agentId = VTCacheUtils::lookupRecordAgentOwner($recordId);
    if ($agentId === false) {
        $query  = "SELECT agentid FROM vtiger_crmentity WHERE crmid = ?";
        $result = $adb->pquery($query, [$recordId]);
        if ($adb->num_rows($result) > 0) {
            $agentId = $adb->query_result($result, 0, 'agentid');
            // Update cache for re-use
            VTCacheUtils::updateRecordAgentOwner($recordId, $agentId);
        }
    }
    return $agentId;
}

function getRecordSalesPerson($recordId)
{
    $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
    return $recordModel->get('sales_person');
    //file_put_contents('logs/devLog.log', "\n SALES PERSON: ".$recordModel->get('sales_person'), FILE_APPEND);
}

//Generates where clause for query generator (list view filtering)
function getListviewAgentCondition($module)
{
    $currentUser = Users_Record_Model::getCurrentUserModel();
   /* if ($currentUser->get('agent_ids') != '' && !$currentUser->isVanLineUser()) {
        //file_put_contents('logs/devLog.log', "\n Not VL", FILE_APPEND);
        $accesibleAgents = explode(' |##| ', $currentUser->get('agent_ids'));
        //file_put_contents('logs/devLog.log', "\n ACC AGENTS: ".print_r($accesibleAgents, true), FILE_APPEND);
    } elseif ($currentUser->isVanLineUser()) {
        $accesibleAgents = $currentUser->getVanlineUserAccessibleAgents();
    }*/
	$accessibleAgents = getPermittedAccessible();
    if (is_array($accesibleAgents) && !$currentUser->isAdminUser()) {
        $agentCondition = Vtiger_Cache::get('vtiger-multi-agent', 'listviewehere-'.$currentUser->id);
        //file_put_contents('logs/devLog.log', "\n Not Admin", FILE_APPEND);
        if (empty($agentCondition)) {
            if ($module == 'Users') {
                $agentCondition = 'vtiger_users.agent_ids IN ('.implode(',', $accesibleAgents).')';
                Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$currentUser->id, $agentCondition);
            } else {
                $accessibleGroups = $currentUser->getUserGroups($currentUser->id);
                //file_put_contents('logs/devLog.log', "\n Accessible Groups: ".print_r($accessibleGroups, true), FILE_APPEND);
                /*
                $agentCondition = '(vtiger_crmentity.agentid IN ('.implode(',', $accesibleAgents).') OR vtiger_crmentity.smownerid IN ('.$currentUser->id;
                if ($accessibleGroups) {
                    $agentCondition .= ','.implode(',', $accessibleGroups);
                }
                $agentCondition .= '))';
                */
                $agentCondition = '(vtiger_crmentity.agentid IN ('.implode(',', $accesibleAgents).')';
                Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$currentUser->id, $agentCondition);
            }
            Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$currentUser->id, $agentCondition);
        }

        //file_put_contents('logs/devLog.log', "\n AGENT CON: $agentCondition", FILE_APPEND);
        return $agentCondition;
    }

    return false;
}

//Modifies query generator where clauses for list view filtering
function getListViewOwnerCondition($module, $ownerAgent=false)
{
    $currentUser = Users_Record_Model::getCurrentUserModel();
    if ($currentUser->isAdminUser()) {
        return false;
    }
    //Related list views that end up using the parent module's permissions -> Related list dont go trough here. This is breaking Local and long distance dispatch
    elseif (($module == 'TariffSections' && $_REQUEST['view'] !='List')
        || $module == 'MoveRoles'
        || $module == 'OrdersMilestone') {
        return false;
    }
    /*else if ($currentUser->get('agent_ids') != '' && !$currentUser->isVanLineUser()) {
        $accessible = explode(' |##| ', $currentUser->get('agent_ids'));
    } elseif ($currentUser->isVanLineUser()) {
        $accessibleAgents   = $currentUser->getVanlineUserAccessibleAgents();
        $accessibleVanlines = $currentUser->getAccessibleVanlinesForUser();
        $accessible         = $currentUser->AssocArrayMerge($accessibleAgents, $accessibleVanlines);
        //$accessible         = array_flip($accessible);
        //hahaha! the idea of flip was to get the keys but it crashed the same name ones.
        $accessible         = array_keys($accessible);
    }*/

        //OT4509 Make read only the records owned by Vanline to child agents
        // If the field uitype is 1020 => This module need to be share between agents and vanlines.
      $fieldInstance = Vtiger_Cache::get('agentid_field_instance', $module);

      if( !$fieldInstance ){
            $fieldInstance = Vtiger_Field_Model::getInstance('agentid', Vtiger_Module_Model::getInstance($module));
            Vtiger_Cache::set('agentid_field_instance', $module, $fieldInstance);
      }


	$accessible = getPermittedAccessible();
    if (is_array($accessible)) {
        if ($module == 'Accounts' || $module == 'Contracts') {
            $accessible = array_merge($accessible, array_keys($currentUser->getAccessibleVanlinesForUser()));
        }
		//file_put_contents('logs/devLog.log', "\n ownerCondition accessible: ".print_r($accessible, true), FILE_APPEND);
        if ($module == 'TariffSections' && $_REQUEST['view'] !='List') {
            return false;
        }

        $ownerCondition = Vtiger_Cache::get('vtiger-multi-agent', 'listviewehere-'.$module.'-'.$currentUser->id);
        if (empty($ownerCondition)) {
            if ($module == 'Users') {
                $ownerCondition = 'vtiger_users.agent_ids IN ('.implode(',', $accessible).')';
                Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$module.'-'.$currentUser->id, $ownerCondition);
            }  elseif ($module == 'OrdersTask' && ( $_REQUEST['view'] == 'NewLocalDispatch' || $_REQUEST['view'] == 'NewLocalDispatchActuals' )) {
                //OT17439 - replace the agentmanagerid by agentid in tasks. We need to build the $accesibleAgents arrays using the agentsid instead of the
                // agentmanagerid
                $accesibleAgents = Vtiger_Cache::get('vtiger-multi-agent', 'pa_orderstasks-'.$currentUser->id);
                if (empty($accesibleAgents)) {
                    $db              = PearDatabase::getInstance();
                    $result          =
                        $db->pquery("SELECT agentsid FROM vtiger_agents INNER JOIN vtiger_crmentity ON vtiger_agents.agentsid = vtiger_crmentity.crmid WHERE agentmanager_id IN (".
                                    implode(',', $accessible).
                                    ")");
                    $accesibleAgents = [];
                    if ($result && $db->num_rows($result) > 0) {
                        while ($row = $db->fetch_row($result)) {
                            $accesibleAgents[] = $row['agentsid'];
                        }
                        Vtiger_Cache::set('vtiger-multi-agent', 'pa_orderstasks-'.$currentUser->id, $accesibleAgents);
                    }

                    if(count($accesibleAgents) == 0) {
                        throw new Exception("Unable to locate participating agents. Please ensure that Agent Manager<br />records for this user have associated Agent Roster records linked.");
                    }
                }
                //OT16753 - In local dispatch show ONLY the task where the user agent is the participanting agent.
                $ownerCondition = '';
                getListViewParticipantCondition($module, $accesibleAgents, $ownerCondition);
            } elseif($fieldInstance->uitype == '1020' && !$currentUser->isVanlineUser()){
                //OT4509 Update reference lookup restriction based on owner
                $needBoth = true;
                $accesibleAgents = $currentUser->getAccessibleOwnersForUser($module, $needBoth, true);
                unset($accesibleAgents['agents']);
                unset($accesibleAgents['vanlines']);
                $accessible = array_keys($accesibleAgents);


                $ownerCondition   = ' vtiger_crmentity.agentid IN ('.implode(',', $accessible).')';

            }elseif($module == 'ReportRun' && $ownerAgent){
                $db = PearDatabase::getInstance();
                $ownerSql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=? LIMIT 1";
                $res = $db->pquery($ownerSql, [$ownerAgent]);

                if($res->fields['setype'] == 'VanlineManager'){
                    $ownerSql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id=?";
                    $res2 = $db->pquery($ownerSql, [$ownerAgent]);
                    $ownerAgentArr = [];
                    while($row =& $res2->fetchRow()) {
                        $ownerAgentArr[] = $row['agentmanagerid'];
                    }
                }elseif($res->fields['setype'] == 'AgentManager'){
                    $ownerAgentArr = [$ownerAgent];
                }
                $agentsArr = array_intersect($ownerAgentArr, $accessible);
                $ownerCondition = '(vtiger_crmentity.agentid IN ('.implode(',', $agentsArr).') OR (vtiger_crmentity.agentid IS NULL AND vtiger_crmentity.smownerid = '.$currentUser->id.') OR vtiger_crmentity.smownerid = '.$currentUser->id;
                //participant conditions
                getListViewParticipantCondition($module, $accessible, $ownerCondition);
                //close the agentid/smowner/participant condition parens
                $ownerCondition .= ')';
                //salesperson conditions
                getListViewSalesPersonCondition($module, $currentUser, $ownerCondition);
                //module specific conditions
                getListViewModuleCondition($module, $ownerCondition);

                // $ownerCondition   = '(vtiger_crmentity.agentid IN ('.implode(',', $accessible).'))';
                Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$module.'-'.$currentUser->id, $ownerCondition);
            }elseif(!($_REQUEST['action'] == 'Feed' && $_REQUEST['module'] == 'Calendar')){
				//agentid and smownerid conditions
                $accessibleGroups = $currentUser->getUserGroups($currentUser->id);
                //Adding crmentity ownerid allows users to see records in list view that they don't have access to edit.
                $ownerCondition   = '(vtiger_crmentity.agentid IN ('.implode(',', $accessible).')';
                if ($module == 'ReportRun') {
                    //So for reports, they can relate things that have no agentid set, because some things don't (ex; Emails, Calendar, ...)
                    $ownerCondition .= ' OR vtiger_crmentity.agentid is NULL';
                }
                $ownerCondition .= ' OR vtiger_crmentity.smownerid IN ('.$currentUser->id;
                if ($accessibleGroups) {
                    $ownerCondition .= ','.implode(',', $accessibleGroups);
                }
				//close the IN for smownerid conditions
				$ownerCondition .= ')';
				//participant conditions
				getListViewParticipantCondition($module, $accessible, $ownerCondition);
				//close the agentid/smowner/participant condition parens
				$ownerCondition .= ')';
				//salesperson conditions
				getListViewSalesPersonCondition($module, $currentUser, $ownerCondition);
				//module specific conditions
				getListViewModuleCondition($module, $ownerCondition);

               // $ownerCondition   = '(vtiger_crmentity.agentid IN ('.implode(',', $accessible).'))';
                Vtiger_Cache::set('vtiger-multi-agent', 'listviewehere-'.$module.'-'.$currentUser->id, $ownerCondition);
            }
        }
        //file_put_contents('logs/devLog.log', "\n ownerCondition: $ownerCondition \n", FILE_APPEND);
        return $ownerCondition;
    }
}

function getListViewSalesPersonCondition($module, $currentUser, &$whereClause)
{
	$currentUser = Users_Record_Model::getCurrentUserModel();
    if ($module == 'Opportunities' || $module == 'Leads' || $module == 'Orders') {
		//it's a module with the salesperson field
        if (getRoleDepth($currentUser->get('roleid')) == 7) {
			//and the current user is a salesperson; add salesperson conditions
            // if they change their mind and salespersons should see null/zero vals, uncomment below line, and comment one below that
            if (getenv('INSTANCE_NAME') != 'sirva') {
			$whereClause .= ' AND (sales_person = ' . $currentUser->id . ' OR sales_person IS NULL OR sales_person = 0)';
            } else {
                $whereClause .= ' AND (sales_person = ' . $currentUser->id . ')';
            }
		}
	}
}

function getListViewParticipantCondition($module, $accessible, &$whereClause)
{
	$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
	if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
		//participating agents module is active
		if($module == 'Potentials' || $module == 'Opportunities' || $module == 'Orders'){
		    $moduleInstance = Vtiger_Module::getInstance($module);
            $tableName = $moduleInstance->basetable;
            $tableKey = $moduleInstance->basetableid;
			//and it's a module with participants
            $whereClause .= ' OR EXISTS (SELECT 1 FROM vtiger_participatingagents WHERE '
            . 'vtiger_participatingagents.rel_crmid=' . $tableName . '.' . $tableKey . ' AND '
            . '(vtiger_participatingagents.agentmanager_id IN (' . implode(',', $accessible) . ') '
            . "AND vtiger_participatingagents.view_level != 'no_access' "
            . "AND (vtiger_participatingagents.status = 'Accepted'";
			//if theres no requests module or if it's inactive accept 'Pending' as a status as well as 'Accepted'
			$requestsModule = Vtiger_Module_Model::getInstance('OASurveyRequests');
			if(!$requestsModule || !$requestsModule->isActive()){
				$whereClause .= " OR vtiger_participatingagents.status = 'Pending'";
			}
			//close the parens
			$whereClause .= ")) AND vtiger_participatingagents.deleted = 0 LIMIT 1)";
		}  elseif ( $module == 'OrdersTask' && ($_REQUEST['view'] == 'NewLocalDispatch' || $_REQUEST['view'] == 'NewLocalDispatchActuals') ) {

		   $whereClause .= " vtiger_orderstask.participating_agent IN (" . implode(',', $accessible) . ") ";
		}
	}
}

function getListViewModuleCondition($module, &$whereClause)
{
    switch ($module) {
		case 'Leads':
			$whereClause .= ' AND converted = 0';
			break;
		case 'Surveys':
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$subordinateUsers = getSubordinateUsers();
			$subordinateUsers[] = $currentUser->getId();
			$usersList = '(' . implode(',', $subordinateUsers) . ')';
            // This didn't work properly, since it appears outside of the owner/agent/participant condition list,
            // and so it would override them
			//$whereClause .= ' OR (`vtiger_crmentity`.smownerid IN ' . $usersList . ')';
            // instead insert it before the last closing parens
            $pos = strrpos($whereClause, ')');
            $rpl = ' OR (`vtiger_crmentity`.smownerid IN ' . $usersList . ')';
            $whereClause = substr_replace($whereClause, $rpl, $pos, 0);
			//file_put_contents('logs/devLog.log', "\n In listViewModuleCondition Surveys \n where clause: $whereClause", FILE_APPEND);
			break;
        case 'OPList':
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $accessibleVanlines = $currentUser->getAccessibleVanlinesForUser();
            if ($accessibleVanlines) {
                $accessibleVanlineList = '('.implode(',', array_keys($accessibleVanlines)).')';
                $pos                   = strrpos($whereClause, ')');
                $rpl                   = ' OR (`vtiger_crmentity`.agentid IN '.$accessibleVanlineList.')';
                $whereClause           = substr_replace($whereClause, $rpl, $pos, 0);
            }
            break;
		default:
			//do nothing
	}
}

function getParticipantFromClause($module, &$fromClause)
{
	$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
	if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
		//participating agents module is active
        if ($module == 'Potentials' || $module == 'Opportunities' || $module == 'Orders') {
			//and it's a module with participants
			$fromClause .= " LEFT JOIN `vtiger_participatingagents` ON `vtiger_participatingagents`.rel_crmid = `vtiger_crmentity`.crmid
					LEFT JOIN `vtiger_agents` ON `vtiger_agents`.agentsid = `vtiger_participatingagents`.agents_id
					LEFT JOIN `vtiger_agentmanager` ON `vtiger_agentmanager`.agentmanagerid = `vtiger_agents`.agentmanager_id ";
		}
	}
}

function filterUserAccessibleGroups($groupsArray)
{
    $currentUser = Users_Record_Model::getCurrentUserModel();
    /*if ($currentUser->get('agent_ids') != '' && !$currentUser->isVanLineUser()) {
        $accessibleAgents = explode(' |##| ', $currentUser->get('agent_ids'));
    } elseif ($currentUser->isVanLineUser()) {
        $accessibleAgents = $currentUser->getVanlineUserAccessibleAgents();
    }*/
	$accessibleAgents = getPermittedAccessible();
    if (is_array($accessibleAgents) && !$currentUser->isAdminUser()) {
        $nonAccesibleGroups = Vtiger_Cache::get('vtiger-multi-agent', 'non-accessiblegroups-'.$currentUser->id);
        if (empty($nonAccesibleGroups)) {
            $db        = PearDatabase::getInstance();
            $agentList = '('.implode(',', $accessibleAgents).')';
            $result    = $db->pquery("SELECT groupid FROM vtiger_users2group
                                        INNER JOIN vtiger_users ON vtiger_users2group.userid = vtiger_users.id
                                        WHERE agent_ids NOT IN $agentList");
            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $nonAccesibleGroups[] = $row['groupid'];
                }
                Vtiger_Cache::set('vtiger-multi-agent', 'non-accessiblegroups-'.$currentUser->id, $nonAccesibleGroups);
            }
        }
        if (!empty($nonAccesibleGroups) && is_array($nonAccesibleGroups)) {
            foreach ($groupsArray as $groupId => $groupName) {
                if (in_array($groupId, $nonAccesibleGroups)) {
                    unset($groupsArray[$groupId]);
                }
            }
        }
    }

    return $groupsArray;
}

function getCurrentUserMembers()
{
    //file_put_contents('logs/devLog.log', "\n hit getCurrentUserMembers()\n", FILE_APPEND);
    $userRecord = Users_Record_Model::getCurrentUserModel();
    if ($userRecord->isAdminUser()) {
        $accessible = array_keys(Users_Record_Model::getAll(false));
        return $accessible;
    } /*else if ($userRecord->get('agent_ids') != '' && !$userRecord->isVanLineUser()) {
        $accessible = explode(' |##| ', $userRecord->get('agent_ids'));
    } elseif ($userRecord->isVanLineUser()) {
        $accessibleAgents   = $userRecord->getVanlineUserAccessibleAgents();
        $accessibleVanlines = $userRecord->getAccessibleVanlinesForUser();
        $accessible         = $userRecord->AssocArrayMerge($accessibleAgents, $accessibleVanlines);
        //@NOTE if accessible isn't what you expected it's this, I extrapolated the fix.
        //$accessible = array_flip($accessible);
        $accessible         = array_keys($accessible);
    }*/
	$accessible = getPermittedAccessible();
    $members = getMembersByRecord($accessible);

    //file_put_contents('logs/devLog.log', "\n in getCurrentUserMembers: ".print_r($members, true), FILE_APPEND);
    return $members;
}

function getMembersByUser($userId)
{
    $userRecord = Users_Record_Model::getInstanceById($userId, 'Users');
    if ($userRecord->isAdminUser()) {
        $accessible = array_keys(Users_Record_Model::getAll(false));
        return $accessible;
    } elseif ($userRecord->get('agent_ids') != '' && !$userRecord->isVanLineUser()) {
        $accessible = explode(' |##| ', $userRecord->get('agent_ids'));
    } elseif ($userRecord->isVanLineUser() || $userRecord->isAdminUser()) {
        $accessibleAgents   = $userRecord->getVanlineUserAccessibleAgents();
        $accessibleVanlines = $userRecord->getAccessibleVanlinesForUser();
        $accessible         = $userRecord->AssocArrayMerge($accessibleAgents, $accessibleVanlines);
        $accessible         = array_flip($accessible);
    }
    return $accessible;
}

function getMembersByRecord($recordIds)
{
    //gets users with accessible agent set
    $currentUser      = Users_Record_Model::getCurrentUserModel();
    $db               = PearDatabase::getInstance();
    //Removed currentUserDepth results limiter for this field. Uncomment to restore.
    // $currentUserDepth = getRoleDepth($currentUser->getRole());
    $ownerList        = '('.implode(',', $recordIds).')';
    $query            = "SELECT id FROM vtiger_users
	INNER JOIN vtiger_user2role ON `vtiger_users`.id = `vtiger_user2role`.userid
	INNER JOIN vtiger_role ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE (agent_ids IN $ownerList";
    foreach ($recordIds as $agentId) {
        $query .= " OR agent_ids LIKE '% $agentId' OR agent_ids LIKE '% $agentId %' OR agent_ids LIKE '$agentId %'";
    }
    $query .= ")";
    //remove or comment above line and uncomment below line to restore currentUserDepth limiter
    //$query .= ") AND `vtiger_role`.depth >= $currentUserDepth";
    //file_put_contents('logs/devLog.log', "\n QUERY: $query", FILE_APPEND);
    $result = $db->pquery($query);
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetchByAssoc($result)) {
            $accessibleUsers[] = $row['id'];
        }
    }

    //file_put_contents('logs/devLog.log', "\n MEMBERS: ".print_r($accessibleUsers, true), FILE_APPEND);
    return $accessibleUsers;
}

function getReferenceFieldRelations($record_id, $module)
{
    //get records related by UI type 10 fields
    $db = PearDatabase::getInstance();
    $relTableRow = $db->pquery("SELECT tablename, entityidfield from `vtiger_entityname` WHERE modulename = ?", [$module])->fetchRow();
    $relTableName = $relTableRow['tablename'];
    $relIdColumn = $relTableRow['entityidfield'];
    $relFields = [];
    $relFieldResult = $db->pquery("SELECT `vtiger_fieldmodulerel`.fieldid, `vtiger_field`.columnname FROM `vtiger_fieldmodulerel` INNER JOIN `vtiger_field` ON `vtiger_fieldmodulerel`.fieldid = `vtiger_field`.fieldid WHERE `vtiger_fieldmodulerel`.module = ? AND `vtiger_field`.`presence` IN (0,2)", [$module]);
    while ($row =& $relFieldResult->fetchRow()) {
        if ($row['columnname'] != 'crmid' && !in_array($row['columnname'], $relFields)) {
            $relFields[] = $row['columnname'];
        }
    }
    // No relation fields, no related records
    if (count($relFields) == 0) {
        return [];
    }
    $relRecords = [];
    $firstRel = array_pop($relFields);
    $relRecordSql = "SELECT DISTINCT " . $firstRel;
    foreach ($relFields as $relFieldColumn) {
        $relRecordSql .= ", $relFieldColumn ";
    }
    $relFields[] = $firstRel;
    $relRecordSql .= " FROM $relTableName WHERE $relIdColumn = ?";
    //file_put_contents('logs/devLog.log', "\n DP: REL RECORD SQL: $relRecordSql \n RECORDID: $record_id", FILE_APPEND);
    $relRecordResult = $db->pquery($relRecordSql, [$record_id]);
    if ($relRecordResult) {
        $row = $relRecordResult->fetchRow();
        foreach ($relFields as $relFieldColumn) {
            if ($row[$relFieldColumn]) {
                $relRecords[] = $row[$relFieldColumn];
            }
        }
    }
    //file_put_contents('logs/devLog.log', "\n RelRecords : ".print_r($relRecords, true), FILE_APPEND);
    return $relRecords;
}

function getCRMEntityRelations($record_id, $module)
{
    //get records related by entityrel fields
    $relatedRecords = [];
    $db = PearDatabase::getInstance();
    $relatedSql = '';
    if ($module == 'Documents') {
        $relatedSql .= 'SELECT crmid FROM `vtiger_senotesrel` WHERE `vtiger_senotesrel`.notesid = ?';
    } else {
        $relatedSql .= 'SELECT `vtiger_crmentityrel`.crmid FROM `vtiger_crmentityrel`  WHERE `vtiger_crmentityrel`.relcrmid = ? AND `vtiger_crmentityrel`.relmodule = ?';
    }
    $aux_result = $db->pquery($relatedSql, [$record_id, $module]);
    if ($aux_result && $db->num_rows($aux_result) > 0) {
        while ($row = $db->fetchByAssoc($aux_result)) {
            $relatedRecords[] = $row['crmid'];
        }
    }
    return $relatedRecords;
 }

function isParticipatingAgentAccesible($accesibleAgents, $record_id, $actionname, $module)
{
	//file_put_contents('logs/devLog.log', "\n isPermitted: $module $record_id $actionname", FILE_APPEND);
    $db        = PearDatabase::getInstance();
    $agentList = '('.implode(',', $accesibleAgents).')';
    $sql = "SELECT `vtiger_participatingagents`.view_level, `vtiger_participatingagents`.status FROM vtiger_participatingagents
                    INNER JOIN vtiger_crmentity as scrm ON scrm.crmid = vtiger_participatingagents.rel_crmid
                    INNER JOIN vtiger_agents ON vtiger_participatingagents.agents_id = vtiger_agents.agentsid
                    INNER JOIN vtiger_agentmanager  ON vtiger_agents.agentmanager_id = vtiger_agentmanager.agentmanagerid
                    WHERE vtiger_agentmanager.agentmanagerid IN $agentList AND vtiger_participatingagents.deleted = 0 AND (rel_crmid = ?";
	//allow related record access
    try {
        //source from crmentityrel
        $crmRelRecords = getCRMEntityRelations($record_id, $module);
        //source from relation fields (UI Type 10)
        $fieldRelRecords = getReferenceFieldRelations($record_id, $module);
        //merge id arrays
        $relRecords = array_merge($crmRelRecords, $fieldRelRecords);
    } catch (Exception $e) {
        $relRecords = [];
        //file_put_contents('logs/devLog.log', "\n Exception assembling related records for isParticipatingAgentAccesible", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n Exception : ".print_r($e, true), FILE_APPEND);
    }
    foreach ($relRecords as $relRecord) {
        //@TODO: find a better solution, because this could be non-unique rel_crmid here.
        //@NOTE: Seen on prod, $relRecord can be |##| for now just checking and exploding
        if (preg_match('/|##|/',$relRecord)) {
            $x = explode(' |##| ', $relRecord);
            foreach ($x as $item) {
                $sql .= " OR rel_crmid = ".$item;
            }
        } else {
            $sql .= " OR rel_crmid = ".$relRecord;
        }
    }

	//now that related records have been added, finish assembling query
    $sql .= ") AND (vtiger_participatingagents.status = 'Accepted' ";

    $requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
    if (!$requestsModel || !$requestsModel->isActive()) {
        $sql .= " OR vtiger_participatingagents.status = 'Pending'";
    } else {
		$sql .= " OR (vtiger_participatingagents.status = 'Pending' AND vtiger_participatingagents.view_level != 'no_access')";
	}

    $sql .= ')';

    if ($module == 'ExtraStops' && $actionname == 'EditView') {
        file_put_contents('logs/devLog.log', "\n DP: participant SQL: $sql \n RECORDID: $record_id", FILE_APPEND);
    }

    $result    = $db->pquery($sql, [$record_id]);
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetchByAssoc($result)) {
			//no estimates access for no rates
            if ($module == 'Estimates' && $row['view_level'] == 'no_rates') {
				continue;
			}
            switch ($actionname) {
                case 'EditView':
                    if ($row['view_level'] == 'full' || $row['view_level'] == 'no_rates') {
						//if requests module is active and status is pending deny edit access
                        if ($row['status'] != 'Pending' || !$requestsModel->isActive()) {
							return true;
						}
                    }
                    break;
                case 'Delete':
                    return false;
                    break;
                default:
                    if ($row['view_level'] == 'full' || $row['view_level'] == 'no_rates' || $row['view_level'] == 'read_only') {
                        return true;
                    }
                    break;
            }
        }
    } else {
        return false;
    }
	return false;
}

function getParticipantAgentShareRecords($moduleName)
{
    $currentUser = Users_Record_Model::getCurrentUserModel();
    $accessibleAgents = getPermittedAccessible();
    $agentList = '('.implode(',', $accessibleAgents).')';
    $query     = " UNION (SELECT smownerid FROM vtiger_participating_agents
        INNER JOIN vtiger_crmentity as scrm ON scrm.crmid = vtiger_participating_agents.crmentity_id
        INNER JOIN vtiger_agents  ON vtiger_participating_agents.agent_id = vtiger_agents.agentsid
        INNER JOIN vtiger_agentmanager  ON vtiger_agents.agent_number = vtiger_agentmanager.agency_code
        WHERE vtiger_agentmanager.agentmanagerid IN $agentList AND scrm.setype='$moduleName')";

    return $query;
}

function getParticipantAgentWhere($moduleName)
{
    //file_put_contents('logs/devLog.log', "\n Stack Trace : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
    $currentUser = Users_Record_Model::getCurrentUserModel();
    if (!$currentUser->isAdminUser()) {
        $accessibleAgents = getPermittedAccessible();
        $agentList       = '('.implode(',', array_keys($accessibleAgents)).')';
        $query           = " OR potentialid IN (SELECT vtiger_participating_agents.crmentity_id FROM vtiger_participating_agents
                    INNER JOIN vtiger_crmentity as scrm ON scrm.crmid = vtiger_participating_agents.crmentity_id
                    INNER JOIN vtiger_agents  ON vtiger_participating_agents.agent_id = vtiger_agents.agentsid
                    INNER JOIN vtiger_agentmanager  ON vtiger_agents.agent_number = vtiger_agentmanager.agency_code
                    WHERE scrm.deleted=0 AND permission IN (0,1,2) AND vtiger_agentmanager.agentmanagerid IN $agentList AND scrm.setype='$moduleName')";

        return $query;
    }

    return false;
}

function getParticipantsForRecord($recordId)
{
	$db = PearDatabase::getInstance();
	$currentUser = Users_Record_Model::getCurrentUserModel();
    $module = false;
    if ($recordId) {
        try {
            $module = Vtiger_Record_Model::getInstanceById($recordId)->getModule()->getName();
        } catch(Exception $e)
        {}
    }
	$participants = [];
	$sql = "SELECT DISTINCT `vtiger_agentmanager`.agentmanagerid FROM vtiger_participatingagents
			INNER JOIN vtiger_crmentity as scrm ON scrm.crmid = vtiger_participatingagents.rel_crmid
			INNER JOIN vtiger_agents ON vtiger_participatingagents.agents_id = vtiger_agents.agentsid
			INNER JOIN vtiger_agentmanager  ON vtiger_agents.agentmanager_id = vtiger_agentmanager.agentmanagerid
			WHERE rel_crmid = ? AND vtiger_participatingagents.deleted = 0";
	$relatedSql = 'SELECT `vtiger_crmentityrel`.crmid FROM `vtiger_crmentityrel` ';
    if ($module == 'Documents') {
		$relatedSql .= 'LEFT JOIN `vtiger_senotesrel` ON `vtiger_crmentityrel`.relcrmid = `vtiger_senotesrel`.crmid
						WHERE `vtiger_senotesrel`.notesid = ?';
    } else {
		$relatedSql .= 'WHERE `vtiger_crmentityrel`.relcrmid = ? AND `vtiger_crmentityrel`.relmodule = ?';
	}
	$aux_result = $db->pquery($relatedSql, [$recordId, $module]);
	if ($aux_result && $db->num_rows($aux_result) > 0) {
		while ($row = $db->fetchByAssoc($aux_result)) {
			//file_put_contents('logs/devLog.log', "\n ISPARTACCS CRMID: ".$row['crmid'], FILE_APPEND);
			$sql .= " OR rel_crmid = " . $row['crmid'];
		}
	}
	$sql .= " AND view_level != 'no_access'";
	$requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
	if (!$requestsModel || !$requestsModel->isActive()) {
		$sql .= " AND (`vtiger_participatingagents`.status = 'Pending' OR `vtiger_participatingagents`.status = 'Accepted')";
    } else {
		$sql .= " AND `vtiger_participatingagents`.status = 'Accepted'";
	}
	$result = $db->pquery($sql, [$recordId]);
	$row = $result->fetchRow();
    while ($row != null) {
		$participants[] = $row['agentmanagerid'];
		$row = $result->fetchRow();
	}
	return $participants;
}

function participantRelationAccessible($module, $relatedModule, $record_id)
{
	//returns true or false depending on if the participant should have access to the link in the related list;
	//convert agent type number to something more readable
	//TODO: agent type should really be a picklist with database values
	//$agentTypeArr = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'];
	//define allowances for different agent types / view-levels
	$readOnlyAccessible = [
		'Opportunities' => [
			'Estimates',
			'Cubesheets',
			'Surveys',
			'Contacts',
			'Documents',
			'Calendar',
		],
	];
	$noRatesAccessible = [
		'Opportunities' => [
			'Cubesheets',
			'Surveys',
			'Contacts',
			'OPList',
			'Contacts',
			'Documents',
			'Calendar',
		],
	];
	/*$current_user     = Users_Record_Model::getCurrentUserModel();
	$userAccessible = getPermittedAccessible();
	$recordAgentOwner = getRecordAgentOwner($record_id);
	$recordAssignedTo = getRecordOwnerId($record_id);
	$recordParticipants = getParticipantsForRecord($record_id);
	$userParticipants = array_intersect($userAccessible, $recordParticipants);*/
    if (isParticipantForRecord($record_id)) {
		//it's a participant that doesn't have owner privileges
		$participantInfo = getParticipantInfoForRecord($record_id);
		$participantTypes = $participantInfo['agent_types'];
		$participantViewLevels = $participantInfo['view_levels'];
		//handle agent types
        if (in_array('Booking Agent', $participantTypes) || in_array('full', $participantViewLevels)) {
			//it's the booking agent or has full access, always return true
			return true;
		}
		//handle view levels
        if (in_array('no_rates', $participantViewLevels) && in_array($relatedModule, $noRatesAccessible[$module])) {
			return true;
		}
        if (in_array('read_only', $participantViewLevels) && in_array($relatedModule, $readOnlyAccessible[$module])) {
			return true;
		}
		//related module allowances are not set, return false to restrict everything as a default
		return false;
    } else {
		//it's someone with owner privileges: always return true
		return true;
	}
}

function getParticipantInfoForRecord($recordId)
{
    if (empty($recordId)) {
		return false;
	}
	$userParticipants = array_intersect(getPermittedAccessible(), getParticipantsForRecord($recordId));
	$db = PearDatabase::getInstance();
	$participantViewLevels = [];
	$participantTypes = [];
    if ($recordId) {
		$module = Vtiger_Record_Model::getInstanceById($recordId)->getModule()->getName();
    } else {
		$module = false;
	}
	//$agentTypeArr = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'];
	//file_put_contents('logs/devLog.log', "\n userParticipants: ".print_r($userParticipants, true), FILE_APPEND);
    foreach ($userParticipants as $participant) {
		$sql = "SELECT view_level, agent_type, agentmanager_id FROM `vtiger_participatingagents`
				WHERE agentmanager_id = ? AND vtiger_participatingagents.deleted = 0 AND (rel_crmid = ? ";
		$relatedSql = 'SELECT `vtiger_crmentityrel`.crmid FROM `vtiger_crmentityrel` ';
        if ($module == 'Documents') {
			$relatedSql .= 'LEFT JOIN `vtiger_senotesrel` ON `vtiger_crmentityrel`.relcrmid = `vtiger_senotesrel`.crmid
							WHERE `vtiger_senotesrel`.notesid = ?';
        } else {
			$relatedSql .= 'WHERE `vtiger_crmentityrel`.relcrmid = ? AND `vtiger_crmentityrel`.relmodule = ?';
		}
		$aux_result = $db->pquery($relatedSql, [$recordId, $module]);
		if ($aux_result && $db->num_rows($aux_result) > 0) {
			while ($row = $db->fetchByAssoc($aux_result)) {
				$sql .= " OR rel_crmid = " . $row['crmid'];
			}
		}
		$sql .= " ) AND view_level != 'no_access' AND (status = 'Accepted' ";
		$requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
		//alter condition if requests module is deactive, can't accept requests that you don't get
		if (!$requestsModel || !$requestsModel->isActive()) {
			$sql .= " OR status = 'Pending'";
		}
		$sql .= ")";
		//file_put_contents('logs/devLog.log', "\n PARTICIPANT INFO SQL: $sql", FILE_APPEND);
		$result = $db->pquery($sql, [$participant, $recordId]);
		//assemble all view levels and agent types applicable to the current user
        while ($row =& $result->fetchRow()) {
			//file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
            if (!in_array($row['view_level'], $participantViewLevels)) {
				$participantViewLevels[] = $row['view_level'];
			}
            if (!in_array($row['agent_type'], $participantTypes)) {
				$participantTypes[] = $row['agent_type'];
			}
		}
	}
	return ["agent_types" => $participantTypes, "view_levels" => $participantViewLevels];
}

function isParticipantForRecord($recordId)
{
	//checks to see if user is in a participating agent for a record AND that the user does NOT have owner privileges
	//returns true/false if both conditions are met
	$current_user     = Users_Record_Model::getCurrentUserModel();
	$userAccessible = getPermittedAccessible();
	$recordAgentOwner = getRecordAgentOwner($recordId);
	$recordAssignedTo = getRecordOwnerId($recordId);
	$recordParticipants = getParticipantsForRecord($recordId);
    if (!in_array($recordAgentOwner, $userAccessible) && !$current_user->isAdminUser() && !in_array($current_user->getId(), $recordAssignedTo) && !empty(array_intersect($userAccessible, $recordParticipants)) && $recordId) {
		return true;
    } else {
		return false;
	}
}
