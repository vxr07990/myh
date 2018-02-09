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
class WFAccounts extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_wfaccounts';
    public $table_index= 'wfaccountsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_wfaccountscf', 'wfaccountsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_wfaccounts', 'vtiger_wfaccountscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_wfaccounts' => 'wfaccountsid',
        'vtiger_wfaccountscf'=>'wfaccountsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Customer Name' => array('wfaccounts', 'wfaccount_name'),
        'Customer Type' => array('wfaccounts', 'wfaccount_type')
    );
    public $list_fields_name = array(
        'Customer Name' => 'wfaccount_name',
        'Customer Type' => 'wfaccount_type',
    );

    // Make the field link to detail view
    public $list_link_field = 'wfaccount_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Customer Name' => array('wfaccounts', 'wfaccount_name'),
        'Customer Type' => array('wfaccounts', 'wfaccount_type')
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Customer Name' => 'wfaccount_name',
        'Customer Type' => 'wfaccount_type',
    );

    // For Popup window record selection
    public $popup_fields = array('wfaccount_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'wfaccount_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'wfaccount_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('wfaccount_name');

    public $default_order_by = 'wfaccount_name';
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
    function save_module($module)
    {
        $this->insertIntoAttachment($this->id,$module);
    }
    function insertIntoAttachment($id,$module)
    {
        global $log, $adb,$upload_badext;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");

        $file_saved = false;
        //This is to added to store the existing attachment id of the contact where we should delete this when we give new image
        $old_attachmentid = $adb->query_result($adb->pquery("select vtiger_crmentity.crmid from vtiger_seattachmentsrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid where  vtiger_seattachmentsrel.crmid=?", array($id)),0,'crmid');
        $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        $_FILES = $result['logo'];
        foreach($_FILES as $fileindex => $files)
        {
            if($files['name'] != '' && $files['size'] > 0)
            {
                $files['expected_attachment_type'] = 'Image';
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $file_saved = $this->uploadAndSaveFile($id,$module,$files);
            }
        }

        $imageNameSql = 'SELECT name FROM vtiger_seattachmentsrel
                          INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
                          LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seattachmentsrel.crmid
                          WHERE vtiger_seattachmentsrel.crmid = ?';
        $imageNameResult = $adb->pquery($imageNameSql,array($id));
        $imageName = decode_html($adb->query_result($imageNameResult, 0, "name"));
        //Inserting image information of record into base table
        $adb->pquery('UPDATE vtiger_wfaccounts SET logo = ? WHERE wfaccountsid = ?',array($imageName,$id));
        //This is to handle the delete image for contacts
        if($file_saved)
        {
            if($old_attachmentid != '')
            {
                $setype = $adb->query_result($adb->pquery("select setype from vtiger_crmentity where crmid=?", array($old_attachmentid)),0,'setype');
                if($setype == 'WFAccounts Attachment')
                {
                    $del_res1 = $adb->pquery("delete from vtiger_attachments where attachmentsid=?", array($old_attachmentid));
                    $del_res2 = $adb->pquery("delete from vtiger_seattachmentsrel where attachmentsid=?", array($old_attachmentid));
                }
            }
        }

        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }

    function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {


        global $app_strings, $singlepane_view, $current_user;

        $parenttab = getParentTab();

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);

        // Some standard module class doesn't have required variables
        // that are used in the query, they are defined in this generic API
        $currentModule = 'WFAccounts';
        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);

        $singular_modname = 'SINGLE_' . $related_module;

        $button = '';

        // To make the edit or del link actions to return back to same view.
        if ($singlepane_view == 'true')
            $returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
        else
            $returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

        $return_value = null;
        $dependentFieldSql = $this->db->pquery("SELECT tabid, fieldname, columnname FROM vtiger_field WHERE uitype='10' AND" .
            " fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=? AND module=?)", array($currentModule, $related_module));
        $numOfFields = $this->db->num_rows($dependentFieldSql);

        if ($numOfFields > 0) {
            $dependentColumn = $this->db->query_result($dependentFieldSql, 0, 'columnname');
            $dependentField = $this->db->query_result($dependentFieldSql, 0, 'fieldname');

            $button .= '<input type="hidden" name="' . $dependentColumn . '" id="' . $dependentColumn . '" value="' . $id . '">';
            $button .= '<input type="hidden" name="' . $dependentColumn . '_type" id="' . $dependentColumn . '_type" value="' . $currentModule . '">';
            if ($actions) {
                if (is_string($actions))
                    $actions = explode(',', strtoupper($actions));
                if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes'
                    && getFieldVisibilityPermission($related_module, $current_user->id, $dependentField, 'readwrite') == '0') {
                    $button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                        " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
                }
            }

            $query = "SELECT vtiger_crmentity.*, $other->table_name.*";

            $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
                'last_name' => 'vtiger_users.last_name'), 'Users');
            $query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";

            $more_relation = '';
            if (!empty($other->related_tables)) {
                foreach ($other->related_tables as $tname => $relmap) {
                    $query .= ", $tname.*";

                    // Setup the default JOIN conditions if not specified
                    if (empty($relmap[1]))
                        $relmap[1] = $other->table_name;
                    if (empty($relmap[2]))
                        $relmap[2] = $relmap[0];
                    $more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
                }
            }

            $query .= " FROM $other->table_name";
            $query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
            $query .= " INNER  JOIN $this->table_name   ON $this->table_name.$this->table_index = $other->table_name.$dependentColumn";
            $query .= $more_relation;
            $query .= " LEFT  JOIN vtiger_users        ON vtiger_users.id = vtiger_crmentity.smownerid";
            $query .= " LEFT  JOIN vtiger_groups       ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

            $query .= " WHERE vtiger_crmentity.deleted = 0 AND $this->table_name.$this->table_index = $id";

            $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
        }
        if ($return_value == null)
            $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        return $return_value;
    }

   public function saveentity($module, $fileid = '') {
       parent::saveentity($module, $fileid);
       if(!$this->checkHasConfiguration($this->id)) {
				 	$record = Vtiger_Record_Model::getInstanceById($this->id,'WFAccounts');
					if($record) {
						$this->generateConfiguration($record);
					}
       }
   }

	 private function checkHasConfiguration($id) {
		 $db = PearDatabase::getInstance();
		 $result = $db->getOne("SELECT count(*) FROM `vtiger_wfconfiguration` WHERE `wfaccount` = $id");
		 if($result == 0) {
			return false;
		 } else {
			return true;
		 }
	 }

	 private function generateConfiguration($record) {
		$configuration = Vtiger_Record_Model::getCleanInstance('WFConfiguration');
		$configuration->set('wfaccount',$record->getId());
		$configuration->set('assigned_user_id',$record->get('assigned_user_id'));
		$configuration->set('agentid',$record->get('agentid'));
		$configuration->set('smcreatorid',$record->get('created_by'));
		$configuration->set('articlenumber_group',1);
		$configuration->set('description_group',1);
		$configuration->save();
	 }
}
