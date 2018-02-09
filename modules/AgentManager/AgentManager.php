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
class AgentManager extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_agentmanager';
    public $table_index= 'agentmanagerid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_agentmanagercf', 'agentmanagerid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_agentmanager', 'vtiger_agentmanagercf'); //, 'vtiger_attachments');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_agentmanager' => 'agentmanagerid',
        'vtiger_agentmanagercf'=>'agentmanagerid',
        //'vtiger_attachments'=>'attachmentsid'
    );

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'LBL_AGENTMANAGER_NO' => Array('agentmanager', 'agent_no'),
        'LBL_AGENTMANAGER_NO' => array('agentmanager', 'agency_no'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        //'LBL_AGENTMANAGER_NO' => 'agent_no',
        'LBL_AGENTMANAGER_NO' => 'agency_no',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    //var $list_link_field = 'agent_no';
    public $list_link_field = 'agency_no';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'LBL_AGENTMANAGER_NO' => Array('agentmanager', 'agent_no'),
        'LBL_AGENTMANAGER_NO' => array('agentmanager', 'agency_no'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_AGENTMANAGER_NO' => 'agency_no',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    //var $popup_fields = Array ('agent_no');
    public $popup_fields = array('agency_no');

    // For Alphabetical search
    //var $def_basicsearch_col = 'agent_no';
    public $def_basicsearch_col = 'agency_no';

    // Column value to use on detail view record text display
    //var $def_detailview_recname = 'agent_no';
    public $def_detailview_recname = 'agency_no';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    //var $mandatory_fields = Array('agent_no','assigned_user_id');
    public $mandatory_fields = array('agency_no','assigned_user_id');

    //var $default_order_by = 'agent_no';
    public $default_order_by = 'agency_no';
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
            $returnset = '&return_module=AgentManager&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module=AgentManager&return_action=CallRelatedList&return_id='.$id;
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $id = mysqli_real_escape_string($id);
        $query = "SELECT vtiger_users.*, vtiger_agentmanager.agency_name as agentname FROM `vtiger_users`
		JOIN `vtiger_agentmanager` ON `vtiger_users`.agency_code=`vtiger_agentmanager`.agency_code WHERE `vtiger_agentmanager`.agentmanagerid='" . $id . "'";
        $log->debug("Exiting get_users method ...");
        return GetRelatedList('AgentManager', 'Users', $focus, $query, $button, $returnset);
    }

    public function unlinkRelationship($id, $return_module, $return_id)
    {
        file_put_contents('logs/Unlink.log', date('Y-m-d H:i:s - ').$return_module."\n", FILE_APPEND);
        global $log;
        if (empty($return_module) || empty($return_id)) {
            return;
        }

        if ($return_module == 'Accounts') {
            $this->trash($this->module_name, $id);
        } elseif ($return_module == 'Campaigns') {
            $sql = 'UPDATE vtiger_potential SET campaignid = ? WHERE potentialid = ?';
            $this->db->pquery($sql, array(null, $id));
        } elseif ($return_module == 'Products') {
            $sql = 'DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } elseif ($return_module == 'Contacts') {
            $sql = 'DELETE FROM vtiger_contpotentialrel WHERE potentialid=? AND contactid=?';
            $this->db->pquery($sql, array($id, $return_id));

            //If contact related to potential through edit of record,that entry will be present in
            //vtiger_potential contact_id column,which should be set to zero
            $sql = 'UPDATE vtiger_potential SET contact_id = ? WHERE potentialid=? AND contact_id=?';
            $this->db->pquery($sql, array(0, $id, $return_id));

            // Potential directly linked with Contact (not through Account - vtiger_contpotentialrel)
            $directRelCheck = $this->db->pquery('SELECT related_to FROM vtiger_potential WHERE potentialid=? AND contact_id=?', array($id, $return_id));
            if ($this->db->num_rows($directRelCheck)) {
                $this->trash($this->module_name, $id);
            }
        } else {
            $sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
            $params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
            $this->db->pquery($sql, $params);
        }
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

        $db = PearDatabase::getInstance();

        //make sure there is $_FILES!
        if (is_array($_FILES)) {
            //transform the $_FILES into a more useful form.
            $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
            $_FILES = $result['imagename'];
            foreach ($_FILES as $fileindex => $files) {
                if ($files['name'] != '' && $files['size'] > 0) {
                    $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                    $columns['imagename']   = $this->saveLogo($module, $files);
                }
            }
        }

        //old securities
        /*if(empty($recordId)) {

            $AgencyAdministratorId = $columns['assigned_user_id'];

            //file_put_contents('logs/devLog.log', "\n AGENT ADMIN ID: ".$AgencyAdministratorId, FILE_APPEND);

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
            $agency_name = $columns['agency_name'];
            $rolename = $agency_name;
            $parentrolesArray = array($roleIdAgent2);
            $directparentrolenum = count($parentrolesArray);
            $directparentrolenum -= 2;
            $directparentrole =  $parentrolesArray[$directparentrolenum];
            $parentroles = $AgencyAdministratorParentRoles."::".implode("::", $parentrolesArray);
            $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
            $result = $db->pquery($sql, array('Agent 2 Profile'));
            $row = $result->fetchRow();
            $profileid=$row[0];
            $this::addNewRole($roleIdAgent2, $rolename, $parentroles, $depth, 1, $directparentrole, $profileid);

            //crate agent administrator role
            $roleIdNumber = $db->getUniqueId('vtiger_role');
            $roleIdAgentAdmin = 'H'.$roleIdNumber;
            $depth = $roleStartDepth+2;
            $agency_name = $columns['agency_name'];
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
            $groupname = $columns['agency_name'];
            $sql = 'INSERT INTO `vtiger_groups`(groupid, groupname, description) VALUES (?,?,?)';
            $db->pquery($sql, array($groupId, $groupname, $description));
            $this::addRoleToGroup2rs($roleIdSalesManager, $groupId);
            $this::addRoleToGroup2role($roleIdAgentAdmin, $groupId);
            //end create group

            //add parent vanline group (if it exists) to new agent group
            $sql = "SELECT `vtiger_groups`.groupid FROM `vtiger_groups` JOIN `vtiger_vanlinemanager` ON vanline_name=groupname WHERE is_parent=1";
            $result = $db->pquery($sql, array());
            if($result){
                $insertSql = "INSERT INTO `vtiger_group2grouprel` VALUES (?,?)";
                while($row =& $result->fetchRow()) {
                    $db->pquery($insertSql, array($groupId, $row['groupid']));
                }
            }
            //end add parent vanline group

            //add agency parent agency admin to user2agency

            $sql = "SELECT id FROM `vtiger_crmentity_seq`";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            $expectedId = $row[0];

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

            if(!empty($vanlineUserId)) {
                $sql = "INSERT INTO `vtiger_users2group` VALUES (?,?)";
                file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
                file_put_contents('logs/devLog.log', "\n params : ".print_r([$groupId, $vanlineUserId], true), FILE_APPEND);
                $db->pquery($sql, [$groupId, $vanlineUserId]);
            }
            //create default agency settings

            //$sql = 'INSERT INTO `vtiger_agencysettings` VALUES (?,?,?,?,?,?,?,?,?,?)';
            //$db->pquery($sql, [$expectedId, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
        }

        else{
            //update group name to match agency name
            $sql = "SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid = ?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();

            $oldAgencyName = $row[0];

            $agent = $columns['agency_name'];

            $sql = "UPDATE `vtiger_groups` SET groupname = ? WHERE groupname = ?";
            $db->pquery($sql, array($agent, $oldAgencyName));

        }*/

        if (isset($expectedId)) {
            $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` ORDER BY agentmanagerid DESC LIMIT 1";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            $recordId = $row[0];

            if ($expectedId != $recordId) {
                return;
            }

            $vanlineId = $columns['vanline_id'];

            $sql = "SELECT tariffid FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND apply_to_all_agents=1";
            $result = $db->pquery($sql, array($vanlineId));

            while ($row =& $result->fetchRow()) {
                $sql = "INSERT INTO `vtiger_tariff2agent` (agentid, tariffid) VALUES (?,?)";
                $result = $db->pquery($sql, array($recordId, $row[0]));
            }
        }
        //automatically set agentid to self
        $sql = "UPDATE `vtiger_crmentity` set agentid = ? WHERE crmid = ?";
        $db->pquery($sql, array($recordId, $recordId));
        //AddressSegments save
        $CapacityCalendarCounterModel= Vtiger_Module_Model::getInstance('CapacityCalendarCounter');
        if ($CapacityCalendarCounterModel && $CapacityCalendarCounterModel->isActive()) {
            //one issue:  [module] => Leads
            $CapacityCalendarCounterModel->saveCapacityCalendarCounter($_REQUEST, $this->id);
        }
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
//        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
//        if(!$moduleInstance || !$moduleInstance->isActive()){
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
//            $params['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $current_user->getId());
//            vtws_create($moduleName, $params, $current_user);
//        }
//    }

    private function saveLogo($module, $file_details)
    {
        //@TODO remove this global variable.
        global $upload_badext;
        $db = PearDatabase::getInstance();

        //validate the file is an image
        if (validateImageFile($file_details)) {
            $date_var = date('Y-m-d H:i:s');
            $current_user = Users_Record_Model::getCurrentUserModel();

            $binFile = sanitizeUploadFileName($file_details['name'], $upload_badext);

            //Resize image to fit
            $tempImage = new \Imagick(realpath($file_details['tmp_name']));

            $tempImage->resizeImage(300, 200, Imagick::FILTER_UNDEFINED, 0.9, true);

            $tempImage->writeImage($file_details['tmp_name']);

            $filename     = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters

            $current_id   = $db->getUniqueID("vtiger_crmentity");
            //get the path to where the file is to be stored.
            $upload_file_path = decideFilePath();
            //upload the file in server
            if (move_uploaded_file($file_details['tmp_name'], $upload_file_path.$current_id."_".$binFile)) {
                $sql1 = "insert into vtiger_crmentity (
                                                        crmid,
                                                        smcreatorid,
                                                        smownerid,
                                                        modifiedby,
                                                        setype,
                                                        description,
                                                        createdtime,
                                                        modifiedtime,
                                                        agentid
                                                        ) values (?,?,?,?,?,?,?,?,?)";
                $params1 =
                     [
                         $current_id,
                         $current_user->id,
                         $this->column_fields['assigned_user_id'],
                         $current_user->id,
                         $module." Attachment",
                         'Agency Logo',
                         $this->db->formatString("vtiger_crmentity", "createdtime", $date_var),
                         $this->db->formatDate($date_var, true),
                         $this->id
                     ];
                $db->pquery($sql1, $params1);

                $sql2 = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)";
                $db->pquery($sql2,
                                  [$current_id, $filename, 'Agency Logo', $file_details['type'], $upload_file_path]);

                $delquery = 'delete from vtiger_salesmanattachmentsrel where smid = ?';
                $this->db->pquery($delquery, [$this->id]);

                //moved this into this block, so if there's no id, don't try it.
                $sql3 = 'insert into vtiger_salesmanattachmentsrel values(?,?)';
                $this->db->pquery($sql3, [$this->id, $current_id]);

                //update the imagename in the record table
                $db->pquery("UPDATE `vtiger_agentmanager` set `imagename`=? where `agentmanagerid`=?", [$filename, $this->id]);

                return $filename;
            } else {
                //upload failed
            }
        }
        return;
    }

    /*
     * wrong place goes in the record.php.
    function deleteImage() {
        $db = PearDatabase::getInstance();
        $sql1 = 'SELECT attachmentsid FROM vtiger_salesmanattachmentsrel WHERE smid = ?';
        $res1 = $db->pquery($sql1, array($this->id));
        if ($db->num_rows($res1) > 0) {
            $attachmentId = $db->query_result($res1, 0, 'attachmentsid');

            $sql2 = "DELETE FROM vtiger_crmentity WHERE crmid=? AND setype='AgentManager Attachment'";
            $db->pquery($sql2, array($attachmentId));

            $sql3 = 'DELETE FROM vtiger_salesmanattachmentsrel WHERE smid=? AND attachmentsid=?';
            $db->pquery($sql3, array($this->id, $attachmentId));

            $sql2 = "UPDATE vtiger_agentmanager SET imagename='' WHERE agentmanagerid=?";
            $db->pquery($sql2, array($this->id));

            $sql4 = 'DELETE FROM vtiger_attachments WHERE attachmentsid=?';
            $db->pquery($sql4, array($attachmentId));
        }
    }
    */

    public function addNewRole($id, $rolename, $parentroles, $depth, $allowassignedrecordsto, $directparentid, $profileid)
    {
        //old securities
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        /* $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO vtiger_role(roleid, rolename, parentrole, depth, allowassignedrecordsto) VALUES (?,?,?,?,?)';
        $db->pquery($sql, array($id, $rolename, $parentroles, $depth, $allowassignedrecordsto));
        $picklist2RoleSQL = "INSERT INTO vtiger_role2picklist SELECT '".$id."',picklistvalueid,picklistid,sortid
        FROM vtiger_role2picklist WHERE roleid = ?";
        $db->pquery($picklist2RoleSQL, array($directparentid));
        $sql = 'INSERT INTO vtiger_role2profile(roleid, profileid) VALUES (?,?)';
        $params = array($id, $profileid);
        $db->pquery($sql, $params); */
    }

    public function addRoleToGroup2rs($memberId, $groupId)
    {
        //old securities
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        /* $db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2rs`(roleandsubid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId)); */
    }

    public function addRoleToGroup2role($memberId, $groupId)
    {
        //old securities
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        /*$db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2role`(roleid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId));*/
    }

    public function save_related_module($module, $crmid, $with_module, $with_crmids)
    {
        $db = PearDatabase::getInstance();
        if (!is_array($with_crmids)) {
            $with_crmids = array($with_crmids);
        }
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == 'Users') {
                //$db->pquery("UPDATE `vtiger_users` SET agency_code=? WHERE id=?", array($with_crmid, $crmid));
                $sql = "SELECT userid, agency_code FROM `vtiger_user2agency` WHERE userid=? AND agency_code=?";
                $result = $db->pquery($sql, array($with_crmid, $crmid));
                $row = $result->fetchRow();
                if ($row == null) {
                    $db->pquery("INSERT INTO `vtiger_user2agency` VALUES (?,?)", array($with_crmid, $crmid));
                }
            } elseif ($with_module == 'Coordinators') {
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }
}
