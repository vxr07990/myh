<?php
/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

include_once 'modules/Vtiger/CRMEntity.php';

class Employees extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_employees';
    public $table_index= 'employeesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_employeescf', 'employeesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_employees', 'vtiger_employeescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_employees' => 'employeesid',
        'vtiger_employeescf'=>'employeesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Name' => array('employees', 'name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Name' => 'name',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Name' => array('employees', 'name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Name' => 'name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('name');

    // For Alphabetical search
    public $def_basicsearch_col = 'name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('name','assigned_user_id');

    public $default_order_by = 'name';
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

    public function save_module($module)
    {
        $this->insertIntoAttachment($this->id, $module);
        return; //Solving the issue of employees doing things to and with users for now.

        //Do not do this user/employees thing anymore
        //@TODO: maybe in the future we will want employee records update user record info like name/address.
        if(
            false ||
            getenv('INSTANCE_NAME') != 'graebel'
        ) {
            // Save User record
            if ($this->column_fields['move_hq_user'] == 'Yes') {
                $arrMappings = [
                    'first_name'         => 'name',
                    'last_name'          => 'employee_lastname',
                    'email1'             => 'employee_email',
                    'phone_mobile'       => 'employee_mphone',
                    'phone_home'         => 'employee_hphone',
                    'address_street'     => 'address1',
                    'address_city'       => 'city',
                    'address_state'      => 'state',
                    'address_postalcode' => 'zip',
                    'address_country'    => 'country',
                    'title'              => 'employees_title',
                    'imagename'          => 'imagename',
                    'status'             => 'employee_status',
                ];
                global $adb;
                $currentUserModel = Users_Record_Model::getCurrentUserModel();
                if ($_REQUEST['record'] != '') {
                    // Get User Id
                    $rsUserId = $adb->pquery("SELECT userid FROM vtiger_employees WHERE employeesid=?", [$this->id]);
                    if ($adb->query_result($rsUserId, 0, 'userid') != '') {
                        $userid          = $adb->query_result($rsUserId, 0, 'userid');
                        $userRecordModel = Users_Record_Model::getInstanceById($userid, 'Users');
                        $modelData       = $userRecordModel->getData();
                        $userRecordModel->set('id', $userid);
                        $sharedType = vtlib_purify($_REQUEST['sharedtype']);
                        if (!empty($sharedType)) {
                            $userRecordModel->set('calendarsharedtype', $sharedType);
                        }
                        $userRecordModel->set('mode', 'edit');
                    } else {
                        $userRecordModel = Vtiger_Record_Model::getCleanInstance('Users');
                        $modelData       = $userRecordModel->getData();
                        $userRecordModel->set('mode', '');
                    }
                } else {
                    $userRecordModel = Vtiger_Record_Model::getCleanInstance('Users');
                    $modelData       = $userRecordModel->getData();
                    $userRecordModel->set('mode', '');
                }
                foreach ($modelData as $fieldName => $value) {
                    if (in_array($fieldName, array_keys($arrMappings))) {
                        if (!isset($_REQUEST[$arrMappings[$fieldName]])) {
                            continue;
                        }
                        $fieldValue = vtlib_purify($_REQUEST[$arrMappings[$fieldName]]);
                    } else {
                        if (!isset($_REQUEST[$fieldName])) {
                            continue;
                        }
                        $fieldValue = vtlib_purify($_REQUEST[$fieldName]);
                    }
                    if ($fieldName === 'is_admin') {
                        if (!$currentUserModel->isAdminUser() && (!$fieldValue)) {
                            $fieldValue = 'off';
                        } elseif ($currentUserModel->isAdminUser() && ($fieldValue || $fieldValue === 'on')) {
                            $fieldValue = 'on';
                            $userRecordModel->set('is_owner', 1);
                        } else {
                            $fieldValue = 'off';
                            $userRecordModel->set('is_owner', 0);
                        }
                    }
                    if ($fieldName == 'agent_ids') {
                        if (isset($_REQUEST['agent_ids'])) {
                            $fieldValue                  = implode(' |##| ', $_REQUEST['agent_ids']);
                            $_REQUEST['agent_ids_order'] = $fieldValue;
                        }
                    }
                    if ($fieldValue !== NULL) {
                        if (!is_array($fieldValue)) {
                            $fieldValue = trim($fieldValue);
                        }
                        $userRecordModel->set($fieldName, $fieldValue);
                    }
                }
                $homePageComponents         = $userRecordModel->getHomePageComponents();
                $selectedHomePageComponents = $_REQUEST['homepage_components'];
                foreach ($homePageComponents as $key => $value) {
                    if (in_array($key, $selectedHomePageComponents)) {
                        $_REQUEST[$key] = $key;
                    } else {
                        $_REQUEST[$key] = '';
                    }
                }
                // Tag cloud save
                $tagCloud = $_REQUEST['tagcloudview'];
                if ($tagCloud == "on") {
                    $userRecordModel->set('tagcloud', 0);
                } else {
                    $userRecordModel->set('tagcloud', 1);
                }
                $userRecordModel->save();
                $userid = $userRecordModel->getId();
                $adb->pquery("UPDATE vtiger_employees SET userid=? WHERE employeesid=?", [$userid, $this->id]);
                //@TODO maybe need this still
                //@NOTE: Actually no, I don't have this field in that database table locally and there is nothing that adds it scripted.
                //$adb->pquery("UPDATE vtiger_users SET employeesid=? WHERE userid=?", [$this->id, $userid]);
            }
        }
    }

    public function saveentity($module, $fileid = '')
    {
            if (getenv('INSTANCE_NAME') == 'graebel') {
                if (($_REQUEST['contractor_prole'] || $_REQUEST['employee_prole']) &&
                    (preg_match('/Driver/', $_REQUEST['contractor_prole']) || preg_match('/Driver/', $_REQUEST['employee_prole']))
                ) {
                    $current_user = Users_Record_Model::getCurrentUserModel();
                    if (!\MoveCrm\InputUtils::CheckboxToBool($current_user->get('drivers_edit_permission'))) {
                        throw new AppException(vtranslate('LBL_PERMISSION_DENIED_DRIVERS'));
                    }
                }
            }

            if (
            (array_key_exists('move_hq_user', $_REQUEST) && !\MoveCrm\InputUtils::CheckboxToBool($_REQUEST['move_hq_user'])) ||
                !\MoveCrm\InputUtils::CheckboxToBool($this->column_fields['move_hq_user'])
            ) {
                $_REQUEST['userid'] = $this->column_fields['userid'] = '';
            }
            if (
                !$_REQUEST['userid'] &&
                !$this->column_fields['userid']
            ) {
                $_REQUEST['move_hq_user'] = $this->column_fields['move_hq_user'] = 'No';
            }
            parent::saveentity($module, $fileid);
    }

    public function insertIntoAttachment($id, $module)
    {
        global $log, $adb,$upload_badext;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");

        $file_saved = false;
        //This is to added to store the existing attachment id of the contact where we should delete this when we give new image
        $old_attachmentid = $adb->query_result($adb->pquery("select vtiger_crmentity.crmid from vtiger_seattachmentsrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid where  vtiger_seattachmentsrel.crmid=?", array($id)), 0, 'crmid');
        foreach ($_FILES as $fileindex => $files) {
            if ($files['name'] != '' && $files['size'] > 0) {
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $file_saved = $this->uploadAndSaveFile($id, $module, $files);
            }
        }

        $imageNameSql = 'SELECT `vtiger_attachments`.`name` FROM vtiger_seattachmentsrel INNER JOIN vtiger_attachments ON
								vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid LEFT JOIN vtiger_employees ON
								vtiger_employees.employeesid = vtiger_seattachmentsrel.crmid WHERE vtiger_seattachmentsrel.crmid = ?';
        $imageNameResult = $adb->pquery($imageNameSql, array($id));
        $imageName = decode_html($adb->query_result($imageNameResult, 0, "name"));

        //Inserting image information of record into base table
        $adb->pquery('UPDATE vtiger_employees SET imagename = ? WHERE employeesid = ?', array($imageName, $id));

        //This is to handle the delete image for contacts
        if ($module == 'Contacts' && $file_saved) {
            if ($old_attachmentid != '') {
                $setype = $adb->query_result($adb->pquery("select setype from vtiger_crmentity where crmid=?", array($old_attachmentid)), 0, 'setype');
                if ($setype == 'Contacts Image') {
                    $del_res1 = $adb->pquery("delete from vtiger_attachments where attachmentsid=?", array($old_attachmentid));
                    $del_res2 = $adb->pquery("delete from vtiger_seattachmentsrel where attachmentsid=?", array($old_attachmentid));
                }
            }
        }

        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }

    public function retrieve_entity_info($record, $module)
    {
        //if(getenv('INSTANCE_NAME') == 'graebel')
        //{
            return parent::retrieve_entity_info($record, $module);
        //}
        global $adb, $log, $app_strings;
        // INNER JOIN is desirable if all dependent table has entries for the record.
        // LEFT JOIN is desired if the dependent tables does not have entry.
        $join_type = 'LEFT JOIN';
        // Tables which has multiple rows for the same record
        // will be skipped in record retrieve - need to be taken care separately.
        $multirow_tables = null;
        if (isset($this->multirow_tables)) {
            $multirow_tables = $this->multirow_tables;
        } else {
            $multirow_tables = [
                'vtiger_campaignrelstatus',
                'vtiger_attachments',
                //'vtiger_inventoryproductrel',
                //'vtiger_cntactivityrel',
                'vtiger_email_track',
            ];
        }
        // Lookup module field cache
        if ($module == 'Calendar' || $module == 'Events') {
            getColumnFields('Calendar');
            $cachedEventsFields   = VTCacheUtils::lookupFieldInfo_Module('Events');
            $cachedCalendarFields = VTCacheUtils::lookupFieldInfo_Module('Calendar');
            $cachedModuleFields   = array_merge($cachedEventsFields, $cachedCalendarFields);
        } else {
            $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        }
        if ($cachedModuleFields === false) {
            // Pull fields and cache for further use
            $tabid = getTabid($module);
            $sql0  = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            // NOTE: Need to skip in-active fields which we will be done later.
            $result0 = $adb->pquery($sql0, [$tabid]);
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
                    // Update cache
                    VTCacheUtils::updateFieldInfo(
                        $tabid,
                        $resultrow['fieldname'],
                        $resultrow['fieldid'],
                        $resultrow['fieldlabel'],
                        $resultrow['columnname'],
                        $resultrow['tablename'],
                        $resultrow['uitype'],
                        $resultrow['typeofdata'],
                        $resultrow['presence']
                    );
                }
                // Get only active field information
                $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
            }
        }
        if ($cachedModuleFields) {
            $column_clause   = '';
            $from_clause     = '';
            $where_clause    = '';
            $limit_clause    = ' LIMIT 1'; // to eliminate multi-records due to table joins.
            $params          = [];
            $required_tables = $this->tab_name_index; // copies-on-write
            foreach ($cachedModuleFields as $fieldinfo) {
                if (in_array($fieldinfo['tablename'], $multirow_tables)) {
                    continue;
                }
                // Added to avoid picking shipping tax fields for Inventory modules, the shipping tax detail are stored in vtiger_inventoryshippingrel
                // table, but in vtiger_field table we have set tablename as vtiger_inventoryproductrel.
                if (($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false
                ) {
                    continue;
                }
                // Alias prefixed with tablename+fieldname to avoid duplicate column name across tables
                // fieldname are always assumed to be unique for a module
                $column_clause .= $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }
            $column_clause .= 'vtiger_crmentity.deleted';
            if (isset($required_tables['vtiger_crmentity'])) {
                $from_clause = ' vtiger_crmentity';
                unset($required_tables['vtiger_crmentity']);
                foreach ($required_tables as $tablename => $tableindex) {
                    if (in_array($tablename, $multirow_tables)) {
                        // Avoid multirow table joins.
                        continue;
                    }
                    $from_clause .= sprintf(' %s %s ON %s.%s=%s.%s',
                        $join_type,
                        $tablename,
                        $tablename,
                        $tableindex,
                        'vtiger_crmentity',
                        'crmid');
                }
            }

            $from_clause .= " LEFT JOIN vtiger_user2role ON vtiger_user2role.userid = vtiger_employees.userid";
            $where_clause .= ' vtiger_crmentity.crmid=?';
            $params[] = $record;
            $sql      = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);
//			echo $adb->convert2Sql($sql, $params)."<br><br>";
            $result = $adb->pquery($sql, $params);
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!empty($resultrow['deleted'])) {
                    throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                }
                foreach ($cachedModuleFields as $fieldinfo) {
                    $fieldvalue = '';
                    $fieldkey   = $this->createColumnAliasForField($fieldinfo);
                    //Note : value is retrieved with a tablename+fieldname as we are using alias while building query
                    if (isset($resultrow[$fieldkey])) {
                        $fieldvalue = $resultrow[$fieldkey];
                    }
                    $this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
                }
            }
        }
        $this->column_fields['record_id']     = $record;
        $this->column_fields['record_module'] = $module;
    }
}
