<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';
require_once('include/Webservices/Create.php');
class VanlineManager extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vanlinemanager';
    public $table_index= 'vanlinemanagerid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vanlinemanagercf', 'vanlinemanagerid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vanlinemanager', 'vtiger_vanlinemanagercf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vanlinemanager' => 'vanlinemanagerid',
        'vtiger_vanlinemanagercf'=>'vanlinemanagerid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_VANLINEMANAGER_NO' => array('vanlinemanager', 'vanline_no'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_VANLINEMANAGER_NO' => 'vanline_no',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'vanline_no';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_VANLINEMANAGER_NO' => array('vanlinemanager', 'vanline_no'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_VANLINEMANAGER_NO' => 'vanline_no',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('vanline_no');

    // For Alphabetical search
    public $def_basicsearch_col = 'vanline_no';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'vanline_no';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('vanline_no','assigned_user_id');

    public $default_order_by = 'vanline_no';
    public $default_sort_order='ASC';

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    public function get_users($id)
    {
        global $log,$singlepane_view;
        $log->debug("Entering get_users(".$id.") method ...");
        require_once('modules/Users/Users.php');
        $focus = new Users();

        $button = '';

        if ($singlepane_view == 'true') {
            $returnset = '&return_module=VanlineManager&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module=VanlineManager&return_action=CallRelatedList&return_id='.$id;
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_users.*, vtiger_vanlinemanager.vanline_name as vanlinename FROM `vtiger_users`
		JOIN `vtiger_vanlinemanager` ON `vtiger_users`.vanline_id=`vtiger_vanlinemanager`.vanline_id WHERE `vtiger_vanlinemanager`.vanlinemanagerid=".$id;
        $log->debug("Exiting get_users method ...");
        return GetRelatedList('VanlineManager', 'Users', $focus, $query, $button, $returnset);
    }

    public function save_related_module($module, $crmid, $with_module, $with_crmids)
    {
        $db = PearDatabase::getInstance();
        if (!is_array($with_crmids)) {
            $with_crmids = array($with_crmids);
        }
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == 'AgentManager') { //When we select Agents from VanlineManager related list
                $sql = "UPDATE `vtiger_agentmanager` SET vanline_id=? WHERE agentmanagerid=?";
                $db->pquery($sql, array($crmid, $with_crmid));
            } elseif ($with_module == 'Users') {
                //$db->pquery("UPDATE `vtiger_users` SET agency_code=? WHERE id=?", array($with_crmid, $crmid));
                $sql = "SELECT userid, vanlineid FROM `vtiger_users2vanline` WHERE userid=? AND vanlineid=?";
                $result = $db->pquery($sql, array($with_crmid, $crmid));
                $row = $result->fetchRow();
                if ($row == null) {
                    $db->pquery("INSERT INTO `vtiger_users2vanline` VALUES (?,?)", array($with_crmid, $crmid));
                }
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }

    public function addNewRole($id, $rolename, $parentroles, $depth, $allowassignedrecordsto, $directparentid, $profileid)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();
        $sql = 'INSERT INTO vtiger_role(roleid, rolename, parentrole, depth, allowassignedrecordsto) VALUES (?,?,?,?,?)';
        $db->pquery($sql, array($id, $rolename, $parentroles, $depth, $allowassignedrecordsto));
        $picklist2RoleSQL = "INSERT INTO vtiger_role2picklist SELECT '".$id."',picklistvalueid,picklistid,sortid
        FROM vtiger_role2picklist WHERE roleid = ?";
        $db->pquery($picklist2RoleSQL, array($directparentid));
        $sql = 'INSERT INTO vtiger_role2profile(roleid, profileid) VALUES (?,?)';
        $params = array($id, $profileid);
        $db->pquery($sql, $params);*/
    }

    public function addRoleToGroup2role($memberId, $groupId)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /* $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2role`(roleid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId)); */
    }

    public function save_module($module)
    {
        $columns = array_merge($this->column_fields, $_REQUEST);

        $recordId = $this->id;
        if (empty($recordId)) {
            if ($columns['record']) {
                $recordId = $columns['record'];
            } elseif ($columns['currentid']) {
                $recordId = $columns['currentid'];
            }
        }
        $newRecord = $columns['newRecord'];
        file_put_contents('logs/ParentVanline.log', date('Y-m-d H:i:s - ').$recordId."\n", FILE_APPEND);

        $db = PearDatabase::getInstance();

        //automatically set agentid to self
        $sql = "UPDATE `vtiger_crmentity` set agentid = ? WHERE crmid = ?";
        $db->pquery($sql, array($recordId, $recordId));

        if (empty($newRecord)) {
            //Old Securities
            /*$isParent = $columns['is_parent'] == 'on';

            $roleStartDepth = 2;

            //create vanline role
            $roleIdNumber = $db->getUniqueId('vtiger_role');
            $roleIdVanline = 'H'.$roleIdNumber;
            $depth = $roleStartDepth;
            $vanline_name = $columns['vanline_name'];
            $rolename = $vanline_name;
            $parentrolesArray = array("H1", "H2", $roleIdVanline);
            $directparentrolenum = count($parentrolesArray);
            $directparentrolenum -= 2;
            $directparentrole =  $parentrolesArray[$directparentrolenum];
            $parentroles = implode("::", $parentrolesArray);
            $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
            $result = $db->pquery($sql, array('Vanline Profile'));
            $row = $result->fetchRow();
            $profileid=$row[0];
            $this::addNewRole($roleIdVanline, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);
            //create vanline user role
            $roleIdNumber = $db->getUniqueId('vtiger_role');
            $roleIdVanlineUser = 'H'.$roleIdNumber;
            $depth = $roleStartDepth+1;
            $rolename = $vanline_name.' Vanline User';
            $parentrolesArray = array("H1", "H2", $roleIdVanline, $roleIdVanlineUser);
            $directparentrolenum = count($parentrolesArray);
            $directparentrolenum -= 2;
            $directparentrole =  $parentrolesArray[$directparentrolenum];
            $parentroles = implode("::", $parentrolesArray);
            $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
            $result = ($isParent) ? $db->pquery($sql, array('Parent Vanline User')) : $db->pquery($sql, array('Vanline User Profile'));
            $row = $result->fetchRow();
            $profileid=$row[0];
            $this::addNewRole($roleIdVanlineUser, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);
            //create agent family administrator role
            if(!$isParent) {
                $roleIdNumber = $db->getUniqueId('vtiger_role');
                $roleIdAgencyAdministrator = 'H'.$roleIdNumber;
                $depth = $roleStartDepth+2;
                $rolename = $vanline_name.' Agent Family Administrator';
                $parentrolesArray = array("H1", "H2", $roleIdVanline, $roleIdVanlineUser, $roleIdAgencyAdministrator);
                $directparentrolenum = count($parentrolesArray);
                $directparentrolenum -= 2;
                $directparentrole =  $parentrolesArray[$directparentrolenum];
                $parentroles = implode("::", $parentrolesArray);
                $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
                $result = $db->pquery($sql, array('Agent Family Administrator Profile'));
                $row = $result->fetchRow();
                $profileid=$row[0];
                $this::addNewRole($roleIdAgencyAdministrator, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);
            }

            //create group
            $description = '';
            $groupId = $db->getUniqueId('vtiger_users');
            file_put_contents('logs/ParentVanline.log', date('Y-m-d H:i:s - ').$groupId."\n", FILE_APPEND);
            $groupname = $columns['vanline_name'];
            $sql = 'INSERT INTO `vtiger_groups`(groupid, groupname, description, grouptype) VALUES (?,?,?,?)';
            $db->pquery($sql, array($groupId, $groupname, $description, 1));
            $this::addRoleToGroup2role($roleIdVanlineUser, $groupId);
            if(!$isParent) {$this::addRoleToGroup2role($roleIdAgencyAdministrator, $groupId);}
            //end create group

            if($isParent) {
                //add parent vanline group to all existing groups
                file_put_contents('logs/ParentVanline.log', date('Y-m-d H:i:s - ').$groupId."\n", FILE_APPEND);
                $insertSql = "INSERT INTO `vtiger_group2grouprel` VALUES (?,?)";

                $sql = "SELECT groupid FROM `vtiger_groups` WHERE groupid != ?";
                $result = $db->pquery($sql, array($groupId));

                while($row =& $result->fetchRow()) {
                    //$this::addRoleToGroup2role($roleIdVanlineUser, $row['groupid']);
                    $db->pquery($insertSql, array($row['groupid'], $groupId));
                }
            } else {
                //Add parent vanline's group to new vanline's group
                $sql = "SELECT `vtiger_groups`.groupid FROM `vtiger_groups` JOIN `vtiger_vanlinemanager` ON vanline_name=groupname WHERE is_parent=1";
                $insertSql = "INSERT INTO `vtiger_group2grouprel` VALUES (?,?)";
                $result = $db->pquery($sql, array());
                while($row =& $result->fetchRow()) {
                    $db->pquery($insertSql, array($groupId, $row['groupid']));
                }
            } */
        } else {
            //update group name to match agency name
            /*$sql = "SELECT vanline_name FROM `vtiger_vanlinemanager` WHERE vanlinemanagerid = ?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();

            $oldVanlineName = $row[0];

            $agent = $columns['vanline_name'];

            $sql = "UPDATE `vtiger_groups` SET groupname = ? WHERE groupname = ?";
            $db->pquery($sql, array($agent, $oldVanlineName));*/
        }
    }

    /**
     * taken from data/CRMEntity.php
     * updated to so related list of leadsourcemanager has special behavior
     */
    public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $currentModule, $app_strings, $singlepane_view;
        $parenttab      = getParentTab();
        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other          = CRMEntity::getInstance($related_module);
        // Some standard module class doesn't have required variables
        // that are used in the query, they are defined in this generic API
        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = 'SINGLE_'.$related_module;
        $button           = '';
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".
                           getTranslatedString('LBL_SELECT').
                           " ".
                           getTranslatedString($related_module).
                           "' class='crmbutton small edit' ".
                           " type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"".
                           " value='".
                           getTranslatedString('LBL_SELECT').
                           " ".
                           getTranslatedString($related_module, $related_module).
                           "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
                           "<input title='".getTranslatedString('LBL_ADD_NEW')." ".getTranslatedString($singular_modname)."' class='crmbutton small create'".
                           " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'".
                           " value='".getTranslatedString('LBL_ADD_NEW')." ".getTranslatedString($singular_modname, $related_module)."'>&nbsp;";
            }
        }
        // To make the edit or del link actions to return back to same view.
        if ($singlepane_view == 'true') {
            $returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
        } else {
            $returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
        }
        $query       = "SELECT vtiger_crmentity.*, $other->table_name.*";
        $userNameSql = getSqlForNameInDisplayFormat(['first_name' => 'vtiger_users.first_name',
                                                     'last_name'  => 'vtiger_users.last_name'],
                                                    'Users');
        $query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";
        $more_relation = '';
        if (!empty($other->related_tables)) {
            foreach ($other->related_tables as $tname => $relmap) {
                $query .= ", $tname.*";
                // Setup the default JOIN conditions if not specified
                if (empty($relmap[1])) {
                    $relmap[1] = $other->table_name;
                }
                if (empty($relmap[2])) {
                    $relmap[2] = $relmap[0];
                }
                $more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
            }
        }
        $query .= " FROM $other->table_name";
        $query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
        $query .= " INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)";
        $query .= $more_relation;
        $query .= " LEFT  JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT  JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " WHERE vtiger_crmentity.deleted = 0";

        //if it's a lead source we don't want ot limit the records to just the "ownership"
        //@TODO: review this I'm not sure I'm going about this task in the right direction.
        if ($related_module == 'LeadSourceManager') {
            //limit by Vanline_id in the vtiger_leadsourcemanager table.
            $query .= " AND vtiger_leadsourcemanager.vanlinemanager_id = $id";
        } elseif ($related_module == 'AgentManager') {
            $query .= " AND vtiger_agentmanager.vanline_id = $id";
        } else {
            //are these the same?  that's sort of weird right?
            $query .= " AND (vtiger_crmentityrel.crmid = $id OR vtiger_crmentityrel.relcrmid = $id)";
        }

        $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
        if ($return_value == null) {
            $return_value = [];
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        return $return_value;
    }

//    public function saveentity($module, $fileid = '') {
//        $newSave = true;
//        if ($_REQUEST['record']) {
//            $newSave = false;
//        }
//        parent::saveentity($module, $fileid);
//        if($newSave) {
//            $agentId = $this->id;
//            $this->generateDefaultRecords('WFSlotConfiguration', $agentId);
//            $this->generateDefaultRecords('WFLocationTypes', $agentId);
//            $this->generateDefaultRecords('WFStatus', $agentId);
//        }
//    }
//
//    public function generateDefaultRecords($moduleName, $agentid, $wareHouseId = 0){
//        //TODO : Remove all references to wareHouseId when field removed
//        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
//        if(!$moduleInstance){
//            return;
//        }
//        $db = PearDatabase::getInstance();
//        $defaultsTable = 'vtiger_'.strtolower($moduleName).'_defaults';
//        $result = $db->pquery('SHOW TABLES LIKE ?', [$defaultsTable]);
//        if ($db->num_rows($result) == 0){
//            return;
//        }
//        $current_user = Users_Record_Model::getCurrentUserModel();
//        $result = $db->pquery('SELECT * FROM `'.$defaultsTable.'`', array());
//        while($row = $result->fetchrow()){
//            $params = [];
//            foreach($row as $columnName=>$columnValue){
//                if(is_string($columnName) && !preg_match('/id$/',$columnName)) {
//                    $params[$columnName] = $columnValue;
//                }
//            }
//            if($moduleName == 'WFLocationTypes'){
//                $params['is_default'] = 'on';
//                $params['warehouse'] = vtws_getWebserviceEntityId('WFWarehouses', $wareHouseId);
//            }
//            if($moduleName == 'WFStatus'){
//                $params['is_default'] = 'on';
//            }
//            $params['agentid'] = $agentid;
//            $params['assigned_user_id'] = vtws_getWebserviceEntityId('Users',$current_user->getId());
//            vtws_create($moduleName, $params, $current_user);
//        }
//    }

}
