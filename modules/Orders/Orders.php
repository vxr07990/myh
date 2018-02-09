<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('libraries/MoveCrm/GraebelAPI/customerHandler.php');
require_once('libraries/MoveCrm/GraebelAPI/orderHandler.php');
require_once('libraries/MoveCrm/GraebelAPI/invoiceHandler.php');
class Orders extends CRMEntity
{
    public $db, $log; // Used in class functions of CRMEntity

    public $table_name = 'vtiger_orders';
    public $table_index= 'ordersid';
    public $column_fields = array();

    /** Indicator if this is a custom module or standard module */
    public $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_orderscf', 'ordersid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_orders', 'vtiger_orderscf', 'vtiger_ordersbillads');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_orders'   => 'ordersid',
        'vtiger_orderscf' => 'ordersid',
        'vtiger_ordersbillads' => 'orderaddressid'
    );

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
    /* Format: Field Label => Array(tablename, columnname) */
    // tablename should not have prefix 'vtiger_'
        'Orders Number'=>array('orders', 'orders_no'),
        'Orders Name'=> array('orders', 'orders_contacts'),
        //'Start Date'=> Array('orders', 'startdate'),
        'Delivery From' => array('orders', 'orders_ddate'),
        'Delivery To' => array('orders', 'orders_dtdate'),
        'Status'=>array('orders', 'ordersstatus'),
        //'Type'=>Array('orders','orderstype'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
    /* Format: Field Label => fieldname */
        'Orders Number'=>'orders_no',
        'Orders Name'=> 'orders_contacts',
        //'Start Date'=> 'startdate',
        'Delivery From' =>  'orders_ddate',
        'Delivery To' =>  'orders_dtdate',
        'Status'=>'ordersstatus',
        //'Type'=>'orderstype',
        'Assigned To' => 'assigned_user_id'
    );

    // Make the field link to detail view from list view (Fieldname)
    public $list_link_field = 'orders_contacts';

    // For Popup listview and UI type support
    public $search_fields = array(
    /* Format: Field Label => Array(tablename, columnname) */
    // tablename should not have prefix 'vtiger_'
    'Orders Number'=>array('orders', 'orders_no'),
    'Orders Name'=> array('orders', 'orders_contacts'),
    'Start Date'=> array('orders', 'startdate'),
    'Status'=>array('orders','ordersstatus'),
    //'Type'=>Array('orders','orderstype'),
    );
    public $search_fields_name = array(
    /* Format: Field Label => fieldname */
    'Orders Name'=> 'orders_contacts',
    //'Start Date'=> 'startdate',
    'Status'=>'ordersstatus',
    //'Type'=>'orderstype',
    );

    // For Popup window record selection
    public $popup_fields = array('orders_contacts');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    public $sortby_fields = array();

    // For Alphabetical search
    public $def_basicsearch_col = 'orders_contacts';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'orders_contacts';

    // Required Information for enabling Import feature
    public $required_fields = array('orders_contacts'=>1);

    // Callback function list during Importing
    public $special_functions = array('set_import_assigned_user');

    public $default_order_by = 'orders_contacts';
    public $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('createdtime', 'modifiedtime', 'orders_contacts', 'assigned_user_id');

    public function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    public function save_module($module)
    {
        //Address List save
        $addressListModule= Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->saveAddressList($_REQUEST, $this->id);
        }
    }

    public function saveentity($module, $fileid = '')
    {
        $newSave = true;
        if ($_REQUEST['record']) {
            $newSave = false;
        }

        //OT1595 received_date is the created date for a new order.
        if (array_key_exists('received_date', $this->column_fields) && !$this->column_fields['received_date']) {
            $dateTime = new DateTimeField($this->column_fields['createdtime']);
            $this->column_fields['received_date'] = $_REQUEST['received_date'] = $dateTime->getDisplayDate();
        }

        $this->preSaveRecord = $this->getRecordModel($_REQUEST);
        $this->column_fields['origin_zone'] = $this->getZone($this->column_fields['origin_state'], $this->column_fields['origin_zip']);
        $this->column_fields['empty_zone'] = $this->getZone($this->column_fields['destination_state'], $this->column_fields['destination_zip']);
        parent::saveentity($module, $fileid);
        $orderId = $this->id;
        //@TODO: what?  how did you find this.
        //$moduleInstance = $this->getInstance($module);
        //$orderId = $moduleInstance->db->database->genID;
        if ($_REQUEST['relationOperation'] && $orderId) {
            $this->copyDocumentsToOrder($_REQUEST['sourceRecord'], $orderId);
            $this->copyPrimaryEstimateToOrder($_REQUEST['sourceRecord'], $orderId);
            $this->copyPrimarySurveyToOrder($_REQUEST['sourceRecord'], $orderId);
        }

        if ($_REQUEST['relationOperation'] && $_REQUEST['sourceModule'] == 'Opportunities'){
            $db = &PearDatabase::getInstance();
            $db->pquery('UPDATE `vtiger_potential` SET opportunitystatus=? WHERE potentialid=?',
                        ['Closed Won', $_REQUEST['sourceRecord']]);
        }
        $fieldList = array_merge($_REQUEST, $this->column_fields);

        if($fieldList['orders_trip']) {
            try {
                $tripRecordModel = Vtiger_Record_Model::getInstanceById($fieldList['orders_trip'], 'Trips');
                $tripRecordModel->recalculateTripsFields();
            } catch (Exception $e)
            {}
        }

        if(getenv('INSTANCE_NAME') == 'graebel') {
            $db = &PearDatabase::getInstance();
            $db->pquery('UPDATE vtiger_orderstask SET orderstask_account=? WHERE ordersid=?',
                        [$fieldList['orders_account'], $this->id]);
        }

        //file_put_contents('logs/devLog.log', "\n DP ORDER NO FieldList : ".print_r($fieldList, true), FILE_APPEND);
        //$this->verifyOrderNumber($fieldList['record'], $fieldList['orders_no']);

        //put this here instead of a workflow so it's reliable.
        if (strtolower(getenv('INSTANCE_NAME')) == 'graebel' && getenv('GVL_API_ON')) {
            $apiResponse = $this->handleGVLAPI($newSave, $fieldList);
            //apiResponse should be a stdClass object
            if (is_array($apiResponse)) {
                foreach ($apiResponse as $responseType => $responseObject) {
                    switch ($responseType) {
                        case 'customerResponse':
                            break;
                        case 'orderResponse':
                            break;
                        default:
                            break;
                    }
                    //do something here to test the response is success.
                }
            } else {
                //fail message invalid response from internal function
            }
        }
        //OT16258 - save participating agent blocks not saving for orders from webservice post.
        //@TODO: this seems to work, but this will need some review later.
        //when it's a webservice call it needs to save the participants
        //participants save
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $participatingAgentsModel::saveParticipants($fieldList, $orderId, $newSave);
        }

        if($newSave && $fieldList['order_id']){
            $orderModel = Orders_Record_Model::getInstanceById($fieldList['order_id']);
            //$orderModel = Orders_Record_Model::getInstanceById($orderId);
            $guestRecord = $orderModel->getGuestModuleRecords('MoveRoles');
            $guestModuleModel = Vtiger_Module_Model::getInstance('MoveRoles');
            if ($guestModuleModel && $guestModuleModel->isActive()) {
                $linkColumn = $guestModuleModel->getLinkColumn('Orders');
                foreach($guestRecord as $singleRecord){
                    //$singleRecord->set($linkColumn, $fieldList['record']);
                    $singleRecord->set($linkColumn, $orderId);
                    $singleRecord->save();
                }
            }
        }

        //it seems like we can't count on element ONLY coming from webservice and so it might double save these.
        //probably not an issue beyond speed and efficiency.
        // guests are now saved in saveentity
        if (false && $_REQUEST['element']) {
            //@TODO: find what variable, function is used to tell when it's initial and not later.
            //if (!CRMEntity::isBulkSaveMode()) {
            if (!$_REQUEST['SaveExtrasDone']) {
                //When it's a Webservice call it needs to save the guest blocks
                //by setting the things we need we can then call that directly.
                $request = new Vtiger_Request(json_decode($_REQUEST['element'], true), json_decode($_REQUEST['element'], true));
                $request->set('record', $fieldList['record']);
                $request->set('module', $module);
                $orderSaveAction = new Orders_Save_Action();
                $orderSaveAction->saveGuests($request);
                //@TODO: find the thing that must exist to handle this.
                $_REQUEST['SaveExtrasDone'] = true;
            }
        }
        $this->postSaveRecord = $this->getRecordModel($fieldList);
        $this->callSyncs();
    }

    protected function getRecordModel($requestArray) {
        $fieldList = array_merge($requestArray, $this->column_fields);
        if (empty($fieldList['record'])) {
            if (!empty($fieldList['currentid'])) {
                $fieldList['record'] = $fieldList['currentid'];
            } else {
                $fieldList['record'] = $this->id;
            }
        }
        if ($fieldList['record']) {
            try {
                return Vtiger_Record_Model::getInstanceById($fieldList['record'], 'Orders');
            } catch (Exception $ex) {
                //LOG ERROR
            }
        }
        return false;
    }

    public function retrieve($record)
    {
        $fieldList = [];
        $db = PearDatabase::getInstance();
        $agentQuery = "SELECT * FROM `vtiger_participatingagents` WHERE rel_crmid=? AND deleted=0";
        $res = $db->pquery($agentQuery, [$record]);
        $index = 0;
        while ($row = $res->fetchRow()) {
            $index++;
            $fieldList['participantId_'.$index] = $row['participatingagentsid'];
            $fieldList['agent_permission_'.$index] = $row['view_level'];
            $fieldList['agents_id_'.$index] = $row['agents_id'];
            $fieldList['agent_type_'.$index] = $row['agent_type'];
        }
        $fieldList['numAgents'] = $index;

        return $fieldList;
    }

    public function verifyOrderNumber($orderId, $orderNumber)
    {
        //file_put_contents('logs/devLog.log', "\n DP OID: $orderId", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n DP ONUM: $orderNumber", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $sql = "SELECT ordersid FROM `vtiger_orders` WHERE orders_no = ?";
        $result = $db->pquery($sql, [$orderNumber]);
        $orderNumbers = [];
        while ($row =& $result->fetchRow()) {
            $orderNumbers[] = $row;
        }
        //file_put_contents('logs/devLog.log', "\n DP COUNT O#: " . count($orderNumbers), FILE_APPEND);
        if (count($orderNumbers) > 1) {
            $newOrderNumber = Orders_Edit_View::getOrderNo();
            $sql = "UPDATE `vtiger_orders` SET orders_no = ? WHERE ordersid = ?";
            $db->pquery($sql, [$newOrderNumber, $orderId]);
            $db->pquery("UPDATE `vtiger_modentity_num` SET cur_id = cur_id+1 WHERE semodule=?", ['Orders']);
        }
    }

    /**
     * Return query to use based on given modulename, fieldname
     * Useful to handle specific case handling for Popup
     */
    public function getQueryByModuleField($module, $fieldname, $srcrecord)
    {
        // $srcrecord could be empty
    }

    /**
     * Get list view query (send more WHERE clause condition if required)
     */
    public function getListQuery($module, $usewhere='')
    {
        $query = "SELECT vtiger_crmentity.*, $this->table_name.*";

        // Keep track of tables joined to avoid duplicates
        $joinedTables = array();

        // Select Custom Field Table Columns if present
        if (!empty($this->customFieldTable)) {
            $query .= ", " . $this->customFieldTable[0] . ".* ";
        }

        $query .= " FROM $this->table_name";

        $query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        $joinedTables[] = $this->table_name;
        $joinedTables[] = 'vtiger_crmentity';

        // Consider custom table join as well.
        if (!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
            $joinedTables[] = $this->customFieldTable[0];
        }
        $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $joinedTables[] = 'vtiger_users';
        $joinedTables[] = 'vtiger_groups';

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for ($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other =  CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            if (!in_array($other->table_name, $joinedTables)) {
                $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
                $joinedTables[] = $other->table_name;
            }
        }

        global $current_user;
        $query .= $this->getNonAdminAccessControlQuery($module, $current_user);
        $query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
        return $query;
    }

    /**
     * Apply security restriction (sharing privilege) query part for List view.
     */
    public function getListViewSecurityParameter($module)
    {
        global $current_user;
        require ('include/utils/LoadUserPrivileges.php');
        require ('include/utils/LoadUserSharingPrivileges.php');

        $sec_query = '';
        $tabid = getTabid($module);

        if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
            && $defaultOrgSharingPermission[$tabid] == 3) {
            $sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR vtiger_crmentity.smownerid IN
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR
						(";

                    // Build the query based on the group association of current user.
                    if (sizeof($current_user_groups) > 0) {
                        $sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
                    }
            $sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
            $sec_query .= ")
				)";
        }
        return $sec_query;
    }

    /**
     * Create query to export the records.
     */
    public function create_export_query($where)
    {
        global $current_user;

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery('Orders', "detail_view");

        $fields_list = getFieldsListFromQuery($sql);

        $query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

        if (!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
        }

        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for ($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other = CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
        }

        $query .= $this->getNonAdminAccessControlQuery($thismodule, $current_user);
        $where_auto = " vtiger_crmentity.deleted=0";

        if ($where != '') {
            $query .= " WHERE ($where) AND $where_auto";
        } else {
            $query .= " WHERE $where_auto";
        }

        return $query;
    }

    /**
     * Transform the value while exporting
     */
    public function transform_export_value($key, $value)
    {
        return parent::transform_export_value($key, $value);
    }

    /**
     * Function which will give the basic query to find duplicates
     */
    public function getDuplicatesQuery($module, $table_cols, $field_values, $ui_type_arr, $select_cols='')
    {
        $select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

        // Select Custom Field Table Columns if present
        if (isset($this->customFieldTable)) {
            $query .= ", " . $this->customFieldTable[0] . ".* ";
        }

        $from_clause = " FROM $this->table_name";

        $from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if (isset($this->customFieldTable)) {
            $from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
        }
        $from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $where_clause = "	WHERE vtiger_crmentity.deleted = 0";
        $where_clause .= $this->getListViewSecurityParameter($module);

        if (isset($select_cols) && trim($select_cols) != '') {
            $sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
                " INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
            // Consider custom table join as well.
            if (isset($this->customFieldTable)) {
                $sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
            }
            $sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
        } else {
            $sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
        }


        $query = $select_clause . $from_clause .
                    " LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
                    " INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values, $ui_type_arr, $module) .
                    $where_clause .
                    " ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

        return $query;
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    public function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == 'module.postinstall') {
            global $adb;

            include_once('vtlib/Vtiger/Module.php');
            $moduleInstance = Vtiger_Module::getInstance($modulename);
            $ordersResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('Orders'));
            $ordersTabid = $adb->query_result($ordersResult, 0, 'tabid');

            // Mark the module as Standard module
            $adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));

            // Add module to Customer portal
            if (getTabid('CustomerPortal') && $ordersTabid) {
                $checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_tabs WHERE tabid=?', array($ordersTabid));
                if ($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
                    $maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
                    $maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
                    $nextSequence = $maxSequence+1;
                    $adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($ordersTabid,1,$nextSequence)");
                    $adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($ordersTabid,'showrelatedinfo',1)");
                }
            }

            // Add Gnatt chart to the related list of the module
            $relation_id = $adb->getUniqueID('vtiger_relatedlists');
            $max_sequence = 0;
            $result = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=$ordersTabid");
            if ($adb->num_rows($result)) {
                $max_sequence = $adb->query_result($result, 0, 'maxsequence');
            }
            $sequence = $max_sequence+1;
            $adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
                        array($relation_id, $ordersTabid, 0, 'get_gantt_chart', $sequence, 'Charts', 0));

            // Add orders module to the related list of Accounts module
            $accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
            $accountsModuleInstance->setRelatedList($moduleInstance, 'Orders', array('ADD', 'SELECT'), 'get_dependents_list');

            // Add orders module to the related list of Accounts module
            $contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
            $contactsModuleInstance->setRelatedList($moduleInstance, 'Orders', array('ADD', 'SELECT'), 'get_dependents_list');

            // Add orders module to the related list of HelpDesk module
            $helpDeskModuleInstance = Vtiger_Module::getInstance('HelpDesk');
            $helpDeskModuleInstance->setRelatedList($moduleInstance, 'Orders', array('SELECT'), 'get_related_list');

            $modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
            if ($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
                include_once 'modules/ModComments/ModComments.php';
                if (class_exists('ModComments')) {
                    ModComments::addWidgetTo(array('Orders'));
                }
            }

            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
            if (!($adb->num_rows($result))) {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'ORDE', 1, 1, 1));
            }
        } elseif ($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } elseif ($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } elseif ($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($event_type == 'module.postupdate') {
            global $adb;

            $ordersResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('Orders'));
            $ordersTabid = $adb->query_result($ordersResult, 0, 'tabid');

            // Add Gnatt chart to the related list of the module
            $relation_id = $adb->getUniqueID('vtiger_relatedlists');
            $max_sequence = 0;
            $result = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=$ordersTabid");
            if ($adb->num_rows($result)) {
                $max_sequence = $adb->query_result($result, 0, 'maxsequence');
            }
            $sequence = $max_sequence+1;
            $adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
                        array($relation_id, $ordersTabid, 0, 'get_gantt_chart', $sequence, 'Charts', 0));

            // Add Comments widget to orders module
            $modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
            if ($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
                include_once 'modules/ModComments/ModComments.php';
                if (class_exists('ModComments')) {
                    ModComments::addWidgetTo(array('Orders'));
                }
            }

            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
            if (!($adb->num_rows($result))) {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'ORDE', 1, 1, 1));
            }
        }
    }

    public static function registerLinks()
    {
    }

    /**
     * Here we override the parent's method,
     * This is done because the related lists for this module use a custom query
     * that queries the child module's table (column of the uitype10 field)
     *
     * @see data/CRMEntity#save_related_module($module, $crmid, $with_module, $with_crmid)
     */
    //function save_related_module($module, $crmid, $with_module, $with_crmid) {    }

    /**
     * Here we override the parent's method
     * This is done because the related lists for this module use a custom query
     * that queries the child module's table (column of the uitype10 field)
     *
     * @see data/CRMEntity#delete_related_module($module, $crmid, $with_module, $with_crmid)
     */
    public function delete_related_module($module, $crmid, $with_module, $with_crmid)
    {
        if (!in_array($with_module, array('OrdersMilestone', 'OrdersTask', 'Stops'))) {
            parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
            return;
        }
        $destinationModule = vtlib_purify($_REQUEST['destination_module']);
        if (empty($destinationModule)) {
            $destinationModule = $with_module;
        }
        if (!is_array($with_crmid)) {
            $with_crmid = array($with_crmid);
        }
        foreach ($with_crmid as $relcrmid) {
            if ($with_module == 'Stops') {
                $sql = "SELECT stop_sequence, stops_isprimary FROM vtiger_stops WHERE stopsid=?";
                $result = $this->db->pquery($sql, array($relcrmid));
                $row = $result->fetchRow();
                $sequence = $row[0];
                $isprimary = $row[1];
                if ($isprimary) {
                    return;
                }

                $sql = "UPDATE vtiger_stops SET stop_order=? WHERE stopsid=?";
                //file_put_contents('logs/unlinkTest.log', date('Y-m-d H:i:s - ').$sql."\n", FILE_APPEND);
                $this->db->pquery($sql, array(null, $relcrmid));

                $sql = "UPDATE vtiger_stops SET stop_sequence=stop_sequence-1 WHERE stop_order=? AND stop_sequence>?";
                $this->db->pquery($sql, array($crmid, $sequence));
            } else {
                $child = CRMEntity::getInstance($destinationModule);
                $child->retrieve_entity_info($relcrmid, $destinationModule);
                $child->mode='edit';
                $child->column_fields['ordersid']='';
                $child->save($destinationModule, $relcrmid);
            }
        }
    }

    /**
     * Handle getting related list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

    /**
     * Handle getting dependents list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }


    public function get_gantt_chart($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        require_once("BURAK_Gantt.class.php");

        $headers = array();
        $headers[0] = getTranslatedString('LBL_PROGRESS_CHART');

        $entries = array();

        global $adb,$tmp_dir,$default_charset;
        $record = $id;
        $g = new BURAK_Gantt();
        // set grid type
        $g->setGrid(1);
        // set Gantt colors
        $g->setColor("group", "000000");
        $g->setColor("progress", "660000");

        $related_orderstasks = $adb->pquery("SELECT pt.* FROM vtiger_orderstask AS pt
												INNER JOIN vtiger_crmentity AS crment ON pt.orderstaskid=crment.crmid
												WHERE ordersid=? AND crment.deleted=0 AND pt.startdate IS NOT NULL AND pt.enddate IS NOT NULL",
                                        array($record)) or die("Please install the OrdersMilestone and OrdersTasks modules first.");

        while ($rec_related_orderstasks = $adb->fetchByAssoc($related_orderstasks)) {
            if ($rec_related_orderstasks['orderstaskprogress']=="--none--") {
                $percentage = 0;
            } else {
                $percentage = str_replace("%", "", $rec_related_orderstasks['orderstaskprogress']);
            }

            $rec_related_orderstasks['orderstaskname'] = iconv($default_charset, "ISO-8859-2//TRANSLIT", $rec_related_orderstasks['orderstaskname']);
            $g->addTask($rec_related_orderstasks['orderstaskid'], $rec_related_orderstasks['startdate'], $rec_related_orderstasks['enddate'], $percentage, $rec_related_orderstasks['orderstaskname']);
        }


        $related_ordersmilestones = $adb->pquery("SELECT pm.* FROM vtiger_ordersmilestone AS pm
													INNER JOIN vtiger_crmentity AS crment on pm.ordersmilestoneid=crment.crmid
													WHERE ordersid=? and crment.deleted=0",
                                            array($record)) or die("Please install the OrdersMilestone and OrdersTasks modules first.");

        while ($rec_related_ordersmilestones = $adb->fetchByAssoc($related_ordersmilestones)) {
            $rec_related_ordersmilestones['ordersmilestonename'] = iconv($default_charset, "ISO-8859-2//TRANSLIT", $rec_related_ordersmilestones['ordersmilestonename']);
            $g->addMilestone($rec_related_ordersmilestones['ordersmilestoneid'], $rec_related_ordersmilestones['ordersmilestonedate'], $rec_related_ordersmilestones['ordersmilestonename']);
        }

        $g->outputGantt($tmp_dir."diagram_".$record.".jpg", "100");

        $origin = $tmp_dir."diagram_".$record.".jpg";
        $destination = $tmp_dir."pic_diagram_".$record.".jpg";

        $imagesize = getimagesize($origin);
        $actualWidth = $imagesize[0];
        $actualHeight = $imagesize[1];

        $size = 1000;
        if ($actualWidth > $size) {
            $width = $size;
            $height = ($actualHeight * $size) / $actualWidth;
            copy($origin, $destination);
            $id_origin = imagecreatefromjpeg($destination);
            $id_destination = imagecreate($width, $height);
            imagecopyresized($id_destination, $id_origin, 0, 0, 0, 0, $width, $height, $actualWidth, $actualHeight);
            imagejpeg($id_destination, $destination);
            imagedestroy($id_origin);
            imagedestroy($id_destination);

            $image = $destination;
        } else {
            $image = $origin;
        }

        $fullGanttChartImageUrl = $tmp_dir."diagram_".$record.".jpg";
        $thumbGanttChartImageUrl = $image;
        $entries[0] = array("<a href='$fullGanttChartImageUrl' border='0' target='_blank'><img src='$thumbGanttChartImageUrl' border='0'></a>");

        return array('header'=> $headers, 'entries'=> $entries);
    }

    /** Function to unlink an entity with given Id from another entity */
    public function unlinkRelationship($id, $return_module, $return_id)
    {
        global $log, $currentModule;

        if ($return_module == 'Accounts') {
            $focus = CRMEntity::getInstance($return_module);
            $entityIds = $focus->getRelatedContactsIds($return_id);
            array_push($entityIds, $return_id);
            $entityIds = implode(',', $entityIds);
            $return_modules = "'Accounts','Contacts'";
        } else {
            $entityIds = $return_id;
            $return_modules = "'".$return_module."'";
        }

        $query = 'DELETE FROM vtiger_crmentityrel WHERE (relcrmid='.$id.' AND module IN ('.$return_modules.') AND crmid IN ('.$entityIds.')) OR (crmid='.$id.' AND relmodule IN ('.$return_modules.') AND relcrmid IN ('.$entityIds.'))';
        $this->db->pquery($query, array());

        $sql = 'SELECT tabid, tablename, columnname FROM vtiger_field WHERE fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE module=? AND relmodule IN ('.$return_modules.'))';
        $fieldRes = $this->db->pquery($sql, array($currentModule));
        $numOfFields = $this->db->num_rows($fieldRes);

        for ($i = 0; $i < $numOfFields; $i++) {
            $tabId = $this->db->query_result($fieldRes, $i, 'tabid');
            $tableName = $this->db->query_result($fieldRes, $i, 'tablename');
            $columnName = $this->db->query_result($fieldRes, $i, 'columnname');
            $relatedModule = vtlib_getModuleNameById($tabId);
            $focusObj = CRMEntity::getInstance($relatedModule);

            $updateQuery = "UPDATE $tableName SET $columnName=? WHERE $columnName IN ($entityIds) AND $focusObj->table_index=?";
            $updateParams = array(null, $id);
            $this->db->pquery($updateQuery, $updateParams);
        }
    }

    /**
     * Move the related records of the specified list of id's to the given record.
     * @param String This module name
     * @param Array List of Entity Id's from which related records need to be transfered
     * @param Integer Id of the the Record to which the related records are to be moved
     */
    public function transferRelatedRecords($module, $transferEntityIds, $entityId)
    {
        global $adb,$log;
        $log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

        $rel_table_arr = array("OrdersTask"=>"vtiger_orderstask",'OrdersMilestone'=>'vtiger_ordersmilestone',
                                "Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel");

        $tbl_field_arr = array("vtiger_orderstask"=>"orderstaskid",'vtiger_ordersmilestone'=>'ordersmilestoneid',
                                "vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid");

        $entity_tbl_field_arr = array("vtiger_orderstask"=>"ordersid",'vtiger_ordersmilestone'=>'ordersid',
                                    "vtiger_senotesrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid");

        foreach ($transferEntityIds as $transferId) {
            foreach ($rel_table_arr as $rel_module=>$rel_table) {
                $id_field = $tbl_field_arr[$rel_table];
                $entity_id_field = $entity_tbl_field_arr[$rel_table];
                // IN clause to avoid duplicate entries
                $sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
                        " and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
                        array($transferId, $entityId));
                $res_cnt = $adb->num_rows($sel_result);
                if ($res_cnt > 0) {
                    for ($i=0;$i<$res_cnt;$i++) {
                        $id_field_value = $adb->query_result($sel_result, $i, $id_field);
                        $adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
                            array($entityId, $transferId, $id_field_value));
                    }
                }
            }
        }
        parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
        $log->debug("Exiting transferRelatedRecords...");
    }

    public function copyDocumentsToOrder($opp_id, $order_id)
    {
        //Get all related documents to the opp
        $sql = 'SELECT notesid FROM `vtiger_senotesrel` WHERE crmid = ?';
        $result = $this->db->pquery($sql, [$opp_id]);
        while ($row = $result->fetchRow()) {
            //create a new relation from the documents to the new order
            $sql = 'INSERT INTO `vtiger_senotesrel` (crmid, notesid) VALUES (?, ?)';
            $this->db->pquery($sql, [$order_id, $row['notesid']]);
        }
    }

    public function copyPrimarySurveyToOrder($opp_id, $order_id)
    {
        $parentRecord = Vtiger_Record_Model::getInstanceById($opp_id, 'Opportunities');
        if($parentRecord) {
            $primarySurveyRecord = $parentRecord->getPrimarySurveyRecordModel();
            if($primarySurveyRecord) {
                $primarySurveyId = $primarySurveyRecord->getId();
            }
            if ($primarySurveyId) {
                $sql = "UPDATE `vtiger_cubesheets` SET cubesheets_orderid = ? WHERE cubesheetsid = ?";
                $this->db->pquery($sql, [$order_id, $primarySurveyId]);
            }
        }
    }

    public function copyPrimaryEstimateToOrder($opp_id, $order_id)
    {
        $sql = 'SELECT * FROM `vtiger_quotes` WHERE potentialid = ? AND is_primary = ? LIMIT 1';
        $result = $this->db->pquery($sql, [$opp_id, '1']);
        $row = $result->fetchRow();

        if ($row) {
//            $sql = 'UPDATE `vtiger_quotes` SET orders_id = ? WHERE quoteid = ? LIMIT 1';
//            $result = $this->db->pquery($sql, [$order_id, $row['quoteid']]);
//
//            $this->db->pquery('UPDATE vtiger_orders SET mileage=? WHERE ordersid=?',
//                              [$row['interstate_mileage'], $order_id]);
//
//            $sql = 'INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES (?, ?, ?, ?)';
//            $params = [$order_id, 'Orders', $row['quoteid'], 'Estimates'];
//            $result = $this->db->pquery($sql, $params);
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users_Record_Model::getCurrentUserModel()->getId());
            $wsid     = vtws_getWebserviceEntityId('Estimates', $row['quoteid']);
            $estimate = vtws_retrieve($wsid, $current_user);
            for ($i = 0; $i <= $estimate['detailLineItemCount']; $i++) {
                unset($estimate['detaillineitemid'.$i]);
            }
            foreach($estimate as $key => $value)
            {
                if(strpos($key, 'serviceProviderID') === 0)
                {
                    unset($estimate[$key]);
                }
            }
            unset($estimate['id']);
            unset($_REQUEST['id']);
            unset($estimate['record']);
            unset($_REQUEST['record']);

            //Drop opp relation and set order relation
            unset($estimate['potential_id']);
            $estimate['orders_id'] = vtws_getWebserviceEntityId('Orders', $order_id);
            $newEstimate = vtws_create('Estimates', $estimate, $current_user);
            if ($newEstimate['id']) {
                $newEstimateId = substr(strstr($newEstimate['id'], 'x'), 1);
                header('Location: index.php?module=Estimates&view=Detail&record='.$newEstimateId);
            } else {
                throw new Exception('Failed to update new Estimate.');
            }
        }
    }

    /** Returns a list of the associated tasks
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_activities(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/Activity.php");
        $other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        $button .= '<input type="hidden" name="activity_mode">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                if (getFieldVisibilityPermission('Calendar', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                               " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
                               " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
                }
                if (getFieldVisibilityPermission('Events', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                               " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
                               " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
                }
            }
        }

        //$entityIds = $this->getRelatedContactsIds();
        //$entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

    //@TODO REVIEW THIS QUERY
        $query = "SELECT vtiger_activity.*, vtiger_cntactivityrel.*, vtiger_seactivityrel.crmid AS parent_id, vtiger_contactdetails.lastname,
				vtiger_contactdetails.firstname, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				CASE 
				  WHEN (vtiger_users.user_name NOT LIKE '') 
				  THEN $userNameSql 
				  ELSE vtiger_groups.groupname 
				  END AS user_name,
				vtiger_recurringevents.recurringtype, 
				CASE 
				  WHEN vtiger_activity.status IS NULL 
				  THEN vtiger_activity.eventstatus 
				  ELSE vtiger_activity.status 
				  END AS status
				FROM vtiger_activity
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
				LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT OUTER JOIN vtiger_recurringevents ON vtiger_recurringevents.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0
				AND
				(
				  (
				    vtiger_activity.activitytype='Task'
				    AND vtiger_activity.status NOT IN ('Completed','Deferred')
				  ) OR (
                    vtiger_activity.activitytype NOT IN ('Emails','Task')
                    AND  vtiger_activity.eventstatus NOT IN ('','Held')
                  )
                )
				AND (vtiger_seactivityrel.crmid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_cntactivityrel.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }
        // There could be more than one contact for an activity.
        $query .= ' GROUP BY vtiger_activity.activityid';

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_activities method ...");
        return $return_value;
    }

    /** Returns a list of the associated tasks
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_cubesheets($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_cubesheets(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        $button .= '<input type="hidden" name="activity_mode">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                if (getFieldVisibilityPermission('Calendar', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                               " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
                               " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
                }
                if (getFieldVisibilityPermission('Events', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                               " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
                               " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
                }
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT vtiger_cubesheets.cubesheetsid, vtiger_cubesheets.cubesheet_name, vtiger_cubesheets.is_primary, vtiger_potential.potentialname,
				vtiger_contactdetails.lastname,	vtiger_contactdetails.firstname, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				FROM vtiger_cubesheets
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cubesheets.cubesheetsid
				LEFT JOIN vtiger_surveys ON vtiger_surveys.surveysid = vtiger_cubesheets.survey_appointment_id
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cubesheets.contact_id
				LEFT JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_cubesheets.potential_id
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0
				AND vtiger_cubesheets.cubesheets_orderid = $id";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_cubesheets method ...");
        return $return_value;
    }

    protected function callSyncs () {
        if (!$this->id) {
            return false;
        }

        if (getenv('INSTANCE_NAME') == 'mccollisters') {
            return $this->callMcCollisterSyncs();
        }
        return true;
    }

    protected function callMcCollisterSyncs () {
        if (!$this->doMcCollisterSync()) {
            return false;
        }
        $driverEmployeeId = $this->column_fields['driver_trip'];
        if (!$driverEmployeeId) {
            return false;
        }

        $driverEmployeeRecord = Employees_Record_Model::getInstanceById($driverEmployeeId);
        if (!$driverEmployeeRecord) {
            return false;
        }

        if (method_exists($driverEmployeeRecord, 'getLinkedUser')) {
            $driverUserRecord = $driverEmployeeRecord->getLinkedUser();
        }

        //ensure the record was pulled
        if (!$driverUserRecord) {
            return false;
        }

        //ensure it has an id and is therefor not a clean instance.
        if (!$driverUserRecord->getId()) {
            return false;
        }

        return $this->MobileMoverOrderUpdateNotification($driverUserRecord);
    }

    /**
     * Returns true or false on whether or not to do the sync for McCollisters based
     * off of whether or not there is a post save record (??), if required fields were
     * filled out, and if certain fields changed
     *
     * @return    Boolean
     */
    protected function doMcCollisterSync() {
        $fieldsMustBe = [
            'orders_otherstatus' => 'Confirmed',
            'driver_trip' => true
        ];
        $fieldsChanged = [
                'driver_trip',
                'orders_otherstatus'
        ];

        if (!$this->checkRecordModelsCanBeTested($this->preSaveRecord, $this->postSaveRecord)) {
            //false return means there is no post save record.
            return false;
        }

        if (is_array($fieldsMustBe)) {
            foreach ($fieldsMustBe as $field => $value) {
                //Make sure all the required fields are the required value
                if (!$this->testRecordFieldValue($this->postSaveRecord, $field, $value)) {

                    //return false from the function if any do NOT match the required value.
                    return false;
                }
            }
        }

        if (
            is_array($fieldsChanged) &&
            count($fieldsChanged) > 0
        ) {
            foreach ($fieldsChanged as $field) {
                //check all of the send notification if a field changed fields
                if ($this->compareRecordFieldChanged($this->preSaveRecord, $this->postSaveRecord, $field)) {
                    //return true from the function if any of these fields are changed.
                    return true;
                }
            }
            //return false here because required fields did not change and need to.
            return false;
        }

        //return true on default because we have:
        // 1) a post save record.
        // 2) the required value fields met the requirements or did not exist.
        // 3) the fields required to change did not exist
        return true;
    }

    /**
     * Returns true or false if fields changed. Returns true only if there is no
     * search field, there is no original record, and the fields do not match
     *
     * @param     Orders_Record_Model $originalRecordModel
     * @param     Orders_Record_Model $currentRecordModel
     * @param     String $field
     * @return    Boolean
     */
    protected function compareRecordFieldChanged($originalRecordModel, $currentRecordModel, $field) {
        if (!$currentRecordModel) {
            return false;
        }

        if (!method_exists($currentRecordModel, 'get')) {
            return false;
        }

        if (!$field) {
            //no search definition so just do it.
            return true;
        }

        $currentFieldValue = $currentRecordModel->get($field);

        if (!$originalRecordModel) {
            return true;
        }

        if (!method_exists($originalRecordModel, 'get')) {
            return true;
        }

        $originalFieldValue = $originalRecordModel->get($field);
        if ($originalFieldValue != $currentFieldValue) {
            //they don't match to return true.
            return true;
        }

        return false;
    }

    protected function checkRecordModelsCanBeTested($originalRecordModel, $currentRecordModel) {
        if (!$currentRecordModel) {
            //no current record?  then don't go
            return false;
        }

        //not really needed.
        if (!$originalRecordModel) {
            //no original record then do it!
            return true;
        }

        return true;
    }

    protected function testRecordFieldValue($recordModel, $field, $value) {
        if (!$recordModel) {
            //there is no record model to test.
            return false;
        }
        if (!method_exists($recordModel, 'get')) {
            //record model is not an expected object?
            return false;
        }

        $recordValue = $recordModel->get($field);

        //CHECK THAT THE VALUE IS JUST SET
        if ($value === true) {
            if (
                //!empty($recordValue) && //@TODO: This might be desired in some context.
                isset($recordValue)
            ) {
                return true;
            }
            return false;
        }

        if ($recordValue != $value) {
            //record's value and required value do not match
            return false;
        }

        //return true on default, because
        return true;
    }



    protected function MobileMoverOrderUpdateNotification($userRecord) {
        $method = 'MobileMoverUpdateNotification';

        if (!getenv('MM_ORDER_NOTIFY_ON')) {
            return false;
        }

        $wsdl = getenv('SURVEY_SYNC_URL');
        if (!$wsdl) {
            return false;
        }

        $params = $this->buildMMParams($userRecord);
        if (!$params) {
            return false;
        }

        //@NOTE: Add in extra things particlar to this type of notification
        $params['orderNumber'] = $this->column_fields['orders_no'];

        //Alter accessKey value to be the "NotificationToken" which is different from the accessKey in the default usage.
//        if ($pushToken = $this->getUserNotificationToken($userRecord)) {
//            //if it exists at least.
//            $params['accessKey'] = $pushToken;
//        }

        try {
            $result = $this->sendMMRequest($wsdl, $method, $params);
        } catch (Exception $ex) {
            //do nothing for now.
            return false;
        }

        return true;
    }

    protected function sendMMRequest($wsdl, $method, $params) {
        require_once('libraries/nusoap/nusoap.php');

        if (!$wsdl) {
            throw new Exception('WSDL is required');
        }

        if (!$method) {
            throw new Exception('method is required');
        }

        $soapResult = [];
        try {
            $soapClient = new soapclient2($wsdl, 'wsdl');
            $soapClient->setDefaultRpcParams(true);
            $soapProxy = $soapClient->getProxy();
            if (!method_exists($soapProxy, $method)) {
                throw new Exception('method ' . $method . ' does not exist.');
            }
            $soapResult = $soapProxy->$method($params);
        } catch (Exception $ex) {
            throw $ex;
        }

        return $soapResult;
    }

    protected function buildMMParams($userRecord) {
        $wsid = vtws_getWebserviceEntityId($this->moduleName,$this->id);

        if (!$wsid) {
            return false;
        }

        $params              = [];
        $params['username']  = $this->getUsername($userRecord);
        $params['accessKey'] = $this->getUserAccessKey($userRecord);
        $params['address']   = getenv('SITE_URL');
        $params['recordID']  = $wsid;

        return $params;
    }

    protected function getUserAccessKey($user) {
        if (!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        return $user->get('accesskey');
    }

    protected function getUsername($user) {
        if (!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        return $user->get('user_name');
    }

    protected function getUserNotificationToken($user) {
        if (!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        $token = $user->get('oi_push_notification_token');
        if (!$token) {
            $token = $user->get('push_notification_token');
        }
        return $token;
    }

    public function handleGVLAPI($newSave = false, $fieldList)
    {
        $customerResponse = false;
        $orderResponse = false;
        $failCustomer = false;
        $record = false;

        if ($fieldList['record']) {
            $record = $fieldList['record'];
        } elseif ($_REQUEST['record']) {
            $record = $_REQUEST['record'];
        } else {
            return false;
        }

        if ($newSave) {
            //We have to trigger the new save stuff.
            //send the Customer POST request.
            try {
                $customerAPI = new MoveCrm\GraebelAPI\customerHandler(['orderNumber' => $record]);
                $customerResponse = $customerAPI->createCustomer();
            } catch (Exception $ex) {
                $failCustomer = true;
                //@TODO: Unsure if we should fail?
            }
            if (!$failCustomer) {
                //send the Order POST request.
                try {
                    $orderAPI = new MoveCrm\GraebelAPI\orderHandler(['orderNumber' => $record]);
                    $orderResponse = $orderAPI->createOrder();
                } catch (Exception $ex) {
                    //@TODO: Unsure if we should fail?
                }
            }
        } else {
            //we have to trigger the existing order update stuff.
            //send the Order POST request.
            try {
                $orderAPI = new MoveCrm\GraebelAPI\orderHandler(['orderNumber' => $record]);
                $orderResponse = $orderAPI->updateOrder();
            } catch (Exception $ex) {
                //@TODO: Unsure if we should fail?
            }
        }

        $db = PearDatabase::getInstance();
        $sql = "SELECT quoteid AS actualid FROM `vtiger_quotes` JOIN `vtiger_crmentity` ON `vtiger_quotes`.quoteid=`vtiger_crmentity`.crmid WHERE orders_id=? AND is_primary=1 AND setype='Actuals' AND deleted=0";
        $result = $db->pquery($sql, [$record]);
        $row = $result->fetchRow();

        if (($fieldList['orders_sit'] == 'on' || $fieldList['orders_sit'] == 1) && $row != null) {
            //We have to trigger an invoice API call for SIT
            file_put_contents('logs/gvlAPI.log', "\n".date('Y-m-d H:i:s - ')."Preparing to trigger Invoice API\n", FILE_APPEND);
            try {
                $invoiceResponse = \MoveCrm\GraebelAPI\invoiceHandler::triggerInvoiceAPI(\MoveCrm\GraebelAPI\invoiceHandler::TRIGGER_SIT, ['orderNumber' => $record, 'actualNumber' => $row['actualid']]);
                file_put_contents('logs/gvlAPI.log', "\n".date('Y-m-d H:i:s - ')."invoiceResponse : ".print_r($invoiceResponse, true)."\n", FILE_APPEND);
            } catch (Exception $ex) {
                file_put_contents('logs/gvlAPI.log', "\n".date('Y-m-d H:i:s - ')."ex->getMessage() (".$ex->getMessage().")", FILE_APPEND);
                file_put_contents('logs/gvlAPI.log', "\n".date('Y-m-d H:i:s - ')."ex->getCode() (".$ex->getCode().")", FILE_APPEND);
            }
        }

        return [
            'customerResponse' => $customerResponse,
            'orderResponse'    => $orderResponse,
            'invoiceResponse'  => $invoiceResponse,
        ];
    }

    public function get_trips($id)
    {
        global $singlepane_view;

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $ordersModel = Vtiger_Record_Model::getInstanceById($id, 'Orders');
        $tripId = $ordersModel->get('orders_trip');

        $button = "";
        $query = "SELECT vtiger_trips.* FROM vtiger_trips INNER JOIN vtiger_crmentity  ON vtiger_trips.tripsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_trips.tripsid = ".$tripId;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        return $return_value;
    }

    function getZone($state, $zip){
        $zoneAdminModel = Vtiger_Module_Model::getInstance('ZoneAdmin');
        return $zoneAdminModel->getAddressZone($state,$zip);
    }
}
