<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Potentials/Potentials.php,v 1.65 2005/04/28 08:08:27 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Emails/mail.php');

class Opportunities extends CRMEntity
{
    public $log;
    public $db;

    public $module_name="Opportunities";
    public $table_name = "vtiger_potential";
    public $table_index= 'potentialid';

    public $tab_name = array('vtiger_crmentity','vtiger_potential','vtiger_potentialscf');
    public $tab_name_index = array('vtiger_crmentity'=>'crmid','vtiger_potential'=>'potentialid','vtiger_potentialscf'=>'potentialid');
    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_potentialscf', 'potentialid');

    public $column_fields = array();

    public $sortby_fields = array('potentialname','amount','closingdate','smownerid','accountname');

    // This is the list of vtiger_fields that are in the lists.
    public $list_fields = array(
            'Potential'=>array('potential'=>'potentialname'),
            'Organization Name'=>array('potential'=>'related_to'),
            'Contact Name'=>array('potential'=>'contact_id'),
            'Sales Stage'=>array('potential'=>'sales_stage'),
            'Amount'=>array('potential'=>'amount'),
            'Expected Close Date'=>array('potential'=>'closingdate'),
            'Assigned To'=>array('crmentity','smownerid')
            );

    public $list_fields_name = array(
            'Potential'=>'potentialname',
            'Organization Name'=>'related_to',
            'Contact Name'=>'contact_id',
            'Sales Stage'=>'sales_stage',
            'Amount'=>'amount',
            'Expected Close Date'=>'closingdate',
            'Assigned To'=>'assigned_user_id');

    public $list_link_field= 'potentialname';

    public $search_fields = array(
            'Potential'=>array('potential'=>'potentialname'),
            'Related To'=>array('account'=>'accountname'),
            'Expected Close Date'=>array('potential'=>'closedate'),
            'Contact Name'=>array(
                                    'contactdetails'=>'firstname',
                                    'contactdetails'=>'lastname'
                                ),
            );

    public $search_fields_name = array(
            'Potential'=>'potentialname',
            'Related To'=>'accountname',
            'Expected Close Date'=>'closingdate',
            'Contact Name'=>'firstname',
            );

    public $required_fields =  array();

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('assigned_user_id', 'createdtime', 'modifiedtime', 'potentialname');

    //Added these variables which are used as default order by and sortorder in ListView
    public $default_order_by = 'potentialname';
    public $default_sort_order = 'ASC';

    // For Alphabetical search
    public $def_basicsearch_col = 'potentialname';

    //var $groupTable = Array('vtiger_potentialgrouprelation','potentialid');
    public function Opportunities()
    {
        $this->log = LoggerManager::getLogger('potential');
        $this->db = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Opportunities');
    }
    public function save_module()
    {
        //this needs to exist for saveentity to work correctly
        //Address List save
        $addressListModule= Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->saveAddressList($_REQUEST, $this->id);
        }
    }

    /** Function to create list query
    * @param reference variable - where condition is passed when the query is executed
    * Returns Query.
    */
    public function create_list_query($order_by, $where)
    {
        global $log,$current_user;
        require ('include/utils/LoadUserPrivileges.php');
        require ('include/utils/LoadUserSharingPrivileges.php');
        $tab_id = getTabid("Opportunities");
        $log->debug("Entering create_list_query(".$order_by.",". $where.") method ...");
        // Determine if the vtiger_account name is present in the where clause.
        $account_required = preg_match("/accounts\.name/", $where);

        if ($account_required) {
            $query = "SELECT vtiger_potential.potentialid,  vtiger_potential.potentialname, vtiger_potential.dateclosed FROM vtiger_potential, vtiger_account ";
            $where_auto = "account.accountid = vtiger_potential.related_to AND vtiger_crmentity.deleted=0 ";
        } else {
            $query = 'SELECT vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_crmentity.smcreatorid, vtiger_potential.closingdate FROM vtiger_potential inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid LEFT JOIN vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid ';
            $where_auto = ' AND vtiger_crmentity.deleted=0';
        }

        $query .= $this->getNonAdminAccessControlQuery('Opportunities', $current_user);
        if ($where != "") {
            $query .= " where $where ".$where_auto;
        } else {
            $query .= " where ".$where_auto;
        }
        if ($order_by != "") {
            $query .= " ORDER BY $order_by";
        }

        $log->debug("Exiting create_list_query method ...");
        return $query;
    }

    /** Function to export the Opportunities records in CSV Format
    * @param reference variable - order by is passed when the query is executed
    * @param reference variable - where condition is passed when the query is executed
    * Returns Export Potentials Query.
    */
    public function create_export_query($where)
    {
        global $log;
        global $current_user;
        $log->debug("Entering create_export_query(". $where.") method ...");

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery("Opportunities", "detail_view");
        $fields_list = getFieldsListFromQuery($sql);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				FROM vtiger_potential
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_account on vtiger_potential.related_to=vtiger_account.accountid
				LEFT JOIN vtiger_contactdetails on vtiger_potential.contact_id=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_potentialscf on vtiger_potentialscf.potentialid=vtiger_potential.potentialid
                LEFT JOIN vtiger_groups
        	        ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_campaign
					ON vtiger_campaign.campaignid = vtiger_potential.campaignid";

        $query .= $this->getNonAdminAccessControlQuery('Opportunities', $current_user);
        $where_auto = "  vtiger_crmentity.deleted = 0 ";

        if ($where != "") {
            $query .= "  WHERE ($where) AND ".$where_auto;
        } else {
            $query .= "  WHERE ".$where_auto;
        }

        $log->debug("Exiting create_export_query method ...");
        return $query;
    }



    /** Returns a list of the associated contacts
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_contacts(".$id.") method ...");
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

        $accountid = $this->column_fields['related_to'];
        $search_string = "&fromPotential=true&acc_id=$accountid";

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab$search_string','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = 'select case when (vtiger_users.user_name not like "") then '.$userNameSql.' else vtiger_groups.groupname end as user_name,
					vtiger_contactdetails.accountid,vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_contactdetails.contactid,
					vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.title, vtiger_contactdetails.department,
					vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
					vtiger_crmentity.modifiedtime , vtiger_account.accountname from vtiger_potential
					left join vtiger_contpotentialrel on vtiger_contpotentialrel.potentialid = vtiger_potential.potentialid
					inner join vtiger_contactdetails on ((vtiger_contactdetails.contactid = vtiger_contpotentialrel.contactid) or (vtiger_contactdetails.contactid = vtiger_potential.contact_id))
					INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					INNER JOIN vtiger_customerdetails ON vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
					INNER JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
					left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
					where vtiger_potential.potentialid = '.$id.' and vtiger_crmentity.deleted=0';

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_contacts method ...");

        return $return_value;
    }

    public function get_potentials($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_potentials(".$id.") method ...");
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

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_account.accountname, vtiger_crmentity.*, vtiger_potential.*, vtiger_potentialscf.*, vtiger_potential.potentialname FROM vtiger_potential
					INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid
                                        INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
                                        INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)
                                        LEFT JOIN vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
                                        LEFT JOIN vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					LEFT JOIN vtiger_account on vtiger_account.accountid=vtiger_potential.related_to
                                        LEFT JOIN vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_potential.contact_id
					WHERE vtiger_crmentity.deleted=0 AND (vtiger_crmentityrel.crmid = $id OR vtiger_crmentityrel.relcrmid = $id)
                                        AND vtiger_crmentity.crmid != $id";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_surveys method ...");
        return $return_value;
    }


    /** Returns a list of the associated calls
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

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_activity.activityid as 'tmp_activity_id',vtiger_activity.*,vtiger_seactivityrel.crmid as parent_id, vtiger_contactdetails.lastname,vtiger_contactdetails.firstname,
					vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
					case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_recurringevents.recurringtype from vtiger_activity
					inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
					left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid = vtiger_activity.activityid
					left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
					inner join vtiger_potential on vtiger_potential.potentialid=vtiger_seactivityrel.crmid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid
					where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0
					and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred'))
					or (vtiger_activity.activitytype NOT in ('Emails','Task') and  vtiger_activity.eventstatus not in ('','Held'))) ";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_activities method ...");
        return $return_value;
    }

     /**
     * Function to get Contact related Products
     * @param  integer   $id  - contactid
     * returns related Products record in array format
     */
    public function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_products(".$id.") method ...");
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

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $query = "SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode,
				vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price,
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid
				FROM vtiger_products
				INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype = 'Opportunities'
				INNER JOIN vtiger_productcf
				ON vtiger_products.productid = vtiger_productcf.productid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_seproductsrel.crmid
				LEFT JOIN vtiger_users
					ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.potentialid = $id";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_products method ...");
        return $return_value;
    }

    /**	Function used to get the Sales Stage history of the Potential
     *	@param $id - potentialid
     *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
     */
    public function get_stage_history($id)
    {
        global $log;
        $log->debug("Entering get_stage_history(".$id.") method ...");

        global $adb;
        global $mod_strings;
        global $app_strings;

        $query = 'select vtiger_potstagehistory.*, vtiger_potential.potentialname from vtiger_potstagehistory inner join vtiger_potential on vtiger_potential.potentialid = vtiger_potstagehistory.potentialid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid where vtiger_crmentity.deleted = 0 and vtiger_potential.potentialid = ?';
        $result=$adb->pquery($query, array($id));
        $noofrows = $adb->num_rows($result);

        $header[] = $app_strings['LBL_AMOUNT'];
        $header[] = $app_strings['LBL_SALES_STAGE'];
        $header[] = $app_strings['LBL_PROBABILITY'];
        $header[] = $app_strings['LBL_CLOSE_DATE'];
        $header[] = $app_strings['LBL_LAST_MODIFIED'];

        //Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
        //Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.
        global $current_user;

        //If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
        $amount_access = (getFieldVisibilityPermission('Opportunities', $current_user->id, 'amount') != '0')? 1 : 0;
        $probability_access = (getFieldVisibilityPermission('Opportunities', $current_user->id, 'probability') != '0')? 1 : 0;
        $picklistarray = getAccessPickListValues('Opportunities');

        $potential_stage_array = $picklistarray['sales_stage'];
        //- ==> picklist field is not permitted in profile
        //Not Accessible - picklist is permitted in profile but picklist value is not permitted
        $error_msg = 'Not Accessible';

        while ($row = $adb->fetch_array($result)) {
            $entries = array();

            $entries[] = ($amount_access != 1)? $row['amount'] : 0;
            $entries[] = (in_array($row['stage'], $potential_stage_array))? $row['stage']: $error_msg;
            $entries[] = ($probability_access != 1) ? $row['probability'] : 0;
            $entries[] = DateTimeField::convertToUserFormat($row['closedate']);
            $date = new DateTimeField($row['lastmodified']);
            $entries[] = $date->getDisplayDate();

            $entries_list[] = $entries;
        }

        $return_data = array('header'=>$header,'entries'=>$entries_list);

        $log->debug("Exiting get_stage_history method ...");

        return $return_data;
    }

    /** Returns a list of the associated emails
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;

        $oppRecordModel = Vtiger_Record_Model::getInstanceById($id, 'Opportunities');
        $id = $oppRecordModel->get('contact_id');

        $log->debug("Entering get_emails(".$id.") method ...");
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

        $button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT crm.label AS subject, rel.relcrmid AS parent_id,
                    DATE(crm.createdtime) AS date_start, TIME(crm.createdtime) as time_start,
                    crm.*
                    FROM `vtiger_crmentityrel` AS rel
                    JOIN `vtiger_emaildetails` AS email ON email.emailid = rel.crmid
                    JOIN `vtiger_crmentity` AS crm ON crm.crmid = rel.crmid
                    WHERE rel.relcrmid = ".$this->id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_emails method ...");
        return $return_value;
    }

    /**
    * Function to get Potential related Task & Event which have activity type Held, Completed or Deferred.
    * @param  integer   $id
    * returns related Task or Event record in array format
    */
    public function get_history($id)
    {
        global $log;
        $log->debug("Entering get_history(".$id.") method ...");
        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
		vtiger_activity.eventstatus, vtiger_activity.activitytype,vtiger_activity.date_start,
		vtiger_activity.due_date, vtiger_activity.time_start,vtiger_activity.time_end,
		vtiger_crmentity.modifiedtime, vtiger_crmentity.createdtime,
		vtiger_crmentity.description,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where (vtiger_activity.activitytype != 'Emails')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
        //Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

        $log->debug("Exiting get_history method ...");
        return getHistory('Opportunities', $query, $id);
    }


      /**
      * Function to get Potential related Quotes
      * @param  integer   $id  - potentialid
      * returns related Quotes record in array format
      */
    public function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_quotes(".$id.") method ...");
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

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_account.accountname, vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname from vtiger_quotes
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid
					left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_quotescf ON vtiger_quotescf.quoteid = vtiger_quotes.quoteid
					LEFT JOIN vtiger_quotesbillads ON vtiger_quotesbillads.quotebilladdressid = vtiger_quotes.quoteid
					LEFT JOIN vtiger_quotesshipads ON vtiger_quotesshipads.quoteshipaddressid = vtiger_quotes.quoteid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					LEFT join vtiger_account on vtiger_account.accountid=vtiger_quotes.accountid
					where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid=".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_quotes method ...");
        return $return_value;
    }

    /**
     * Function to get Potential related Quotes
     * @param  integer   $id  - potentialid
     * returns related Quotes record in array format
     */
    public function get_surveys($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_surveys(".$id.") method ...");
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

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_account.accountname, vtiger_crmentity.*, vtiger_surveys.*, vtiger_surveyscf.*, vtiger_potential.potentialname from vtiger_surveys
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_surveys.surveysid
					left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_surveys.potential_id
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_surveyscf ON vtiger_surveyscf.surveysid = vtiger_surveys.surveysid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					LEFT join vtiger_account on vtiger_account.accountid=vtiger_surveys.account_id
					where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid=".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_surveys method ...");
        return $return_value;
    }

    /**
     * Function to get Potential related SalesOrder
     * @param  integer   $id  - potentialid
     * returns related SalesOrder record in array format
     */
    public function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_salesorder(".$id.") method ...");
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

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename
			, vtiger_account.accountname, vtiger_potential.potentialname,case when
			(vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname
			end as user_name from vtiger_salesorder
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
			left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid
			left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid
			left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_salesorder.potentialid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
            LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
            LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.start_period = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			 where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid = ".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_salesorder method ...");
        return $return_value;
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

        $rel_table_arr = array("Activities"=>"vtiger_seactivityrel","Contacts"=>"vtiger_contpotentialrel","Products"=>"vtiger_seproductsrel",
                        "Attachments"=>"vtiger_seattachmentsrel","Quotes"=>"vtiger_quotes","SalesOrder"=>"vtiger_salesorder",
                        "Documents"=>"vtiger_senotesrel");

        $tbl_field_arr = array("vtiger_seactivityrel"=>"activityid","vtiger_contpotentialrel"=>"contactid","vtiger_seproductsrel"=>"productid",
                        "vtiger_seattachmentsrel"=>"attachmentsid","vtiger_quotes"=>"quoteid","vtiger_salesorder"=>"salesorderid",
                        "vtiger_senotesrel"=>"notesid");

        $entity_tbl_field_arr = array("vtiger_seactivityrel"=>"crmid","vtiger_contpotentialrel"=>"potentialid","vtiger_seproductsrel"=>"crmid",
                        "vtiger_seattachmentsrel"=>"crmid","vtiger_quotes"=>"potentialid","vtiger_salesorder"=>"potentialid",
                        "vtiger_senotesrel"=>"crmid");

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

    /*
     * Function to get the secondary query part of a report
     * @param - $module primary module name
     * @param - $secmodule secondary module name
     * returns the query string formed on fetching the related data for report for secondary module
     */
    public function generateReportsSecQuery($module, $secmodule, $queryplanner)
    {
        $matrix = $queryplanner->newDependencyMatrix();
        $matrix->setDependency('vtiger_crmentityPotentials', array('vtiger_groupsPotentials', 'vtiger_usersPotentials', 'vtiger_lastModifiedByPotentials'));
        $matrix->setDependency('vtiger_potential', array('vtiger_crmentityPotentials', 'vtiger_accountPotentials',
                                            'vtiger_contactdetailsPotentials', 'vtiger_campaignPotentials', 'vtiger_potentialscf'));


        if (!$queryplanner->requireTable("vtiger_potential", $matrix)) {
            return '';
        }

        $query = $this->getRelationQuery($module, $secmodule, "vtiger_potential", "potentialid", $queryplanner);

        if ($queryplanner->requireTable("vtiger_crmentityPotentials", $matrix)) {
            $query .= " left join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid and vtiger_crmentityPotentials.deleted=0";
        }
        if ($queryplanner->requireTable("vtiger_accountPotentials")) {
            $query .= " left join vtiger_account as vtiger_accountPotentials on vtiger_potential.related_to = vtiger_accountPotentials.accountid";
        }
        if ($queryplanner->requireTable("vtiger_contactdetailsPotentials")) {
            $query .= " left join vtiger_contactdetails as vtiger_contactdetailsPotentials on vtiger_potential.contact_id = vtiger_contactdetailsPotentials.contactid";
        }
        if ($queryplanner->requireTable("vtiger_potentialscf")) {
            $query .= " left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid";
        }
        if ($queryplanner->requireTable("vtiger_groupsPotentials")) {
            $query .= " left join vtiger_groups vtiger_groupsPotentials on vtiger_groupsPotentials.groupid = vtiger_crmentityPotentials.smownerid";
        }
        if ($queryplanner->requireTable("vtiger_usersPotentials")) {
            $query .= " left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid";
        }
        if ($queryplanner->requireTable("vtiger_campaignPotentials")) {
            $query .= " left join vtiger_campaign as vtiger_campaignPotentials on vtiger_potential.campaignid = vtiger_campaignPotentials.campaignid";
        }
        if ($queryplanner->requireTable("vtiger_lastModifiedByPotentials")) {
            $query .= " left join vtiger_users as vtiger_lastModifiedByPotentials on vtiger_lastModifiedByPotentials.id = vtiger_crmentityPotentials.modifiedby ";
        }
        if ($queryplanner->requireTable("vtiger_createdbyPotentials")) {
            $query .= " left join vtiger_users as vtiger_createdbyPotentials on vtiger_createdbyPotentials.id = vtiger_crmentityPotentials.smcreatorid ";
        }
        return $query;
    }

    /*
     * Function to get the relation tables for related modules
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    public function setRelationTables($secmodule)
    {
        $rel_tables = array(
            "Calendar" => array("vtiger_seactivityrel"=>array("crmid", "activityid"), "vtiger_potential"=>"potentialid"),
            "Products" => array("vtiger_seproductsrel"=>array("crmid", "productid"), "vtiger_potential"=>"potentialid"),
            "Quotes" => array("vtiger_quotes"=>array("potentialid", "quoteid"), "vtiger_potential"=>"potentialid"),
            "SalesOrder" => array("vtiger_salesorder"=>array("potentialid", "salesorderid"), "vtiger_potential"=>"potentialid"),
            "Documents" => array("vtiger_senotesrel"=>array("crmid", "notesid"), "vtiger_potential"=>"potentialid"),
            "Accounts" => array("vtiger_potential"=>array("potentialid", "related_to")),
            "Contacts" => array("vtiger_potential"=>array("potentialid", "contact_id")),
        );
        return $rel_tables[$secmodule];
    }

    // Function to unlink all the dependent entities of the given Entity by Id
    public function unlinkDependencies($module, $id)
    {
        global $log;
        /*//Backup Activity-Potentials Relation
        $act_q = "select activityid from vtiger_seactivityrel where crmid = ?";
        $act_res = $this->db->pquery($act_q, array($id));
        if ($this->db->num_rows($act_res) > 0) {
            for($k=0;$k < $this->db->num_rows($act_res);$k++)
            {
                $act_id = $this->db->query_result($act_res,$k,"activityid");
                $params = array($id, RB_RECORD_DELETED, 'vtiger_seactivityrel', 'crmid', 'activityid', $act_id);
                $this->db->pquery("insert into vtiger_relatedlists_rb values (?,?,?,?,?,?)", $params);
            }
        }
        $sql = 'delete from vtiger_seactivityrel where crmid = ?';
        $this->db->pquery($sql, array($id));*/

        parent::unlinkDependencies($module, $id);
    }

    // Function to unlink an entity with given Id from another entity
    public function unlinkRelationship($id, $return_module, $return_id)
    {
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

    public function save_related_module($module, $crmid, $with_module, $with_crmids)
    {
        $adb = PearDatabase::getInstance();

        if (!is_array($with_crmids)) {
            $with_crmids = array($with_crmids);
        }
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == 'Contacts') { //When we select contact from potential related list
                $sql = "insert into vtiger_contpotentialrel values (?,?)";
                $adb->pquery($sql, array($with_crmid, $crmid));
            } elseif ($with_module == 'Products') {
                //when we select product from potential related list
                $sql = "insert into vtiger_seproductsrel values (?,?,?)";
                $adb->pquery($sql, array($crmid, $with_crmid, 'Opportunities'));
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }
    public function saveentity($module, $fileid = '')
    {
        //if ($_REQUEST['repeat'] === true) {
            //return;
        //}

        //does things twice, this stops it.
        //$_REQUEST['repeat'] = true;
        $newRecord = false;

         //* this is possibly all wrong.
        $db        = PearDatabase::getInstance();
        $fieldList = array_merge($_REQUEST, $this->column_fields);

        //@NOTE: $_REQUEST could have untrue things like module = Leads.  There may be others. OH MY word this is done TWICE
        $fieldList['module'] = $module;
        //file_put_contents('logs/devLog.log', "\n FieldList : ".print_r($fieldList, true), FILE_APPEND);

        //Pull current sales person if record already exists
        $id = $fieldList['record'];
        if(empty($id) && isset($_REQUEST['element'])) {
            $elementObj = json_decode($_REQUEST['element'], true);
            $idArray = explode('x',$elementObj['id']);
            $id = $idArray[sizeof($idArray)-1];
        }elseif(empty($id) && isset($_REQUEST['recordId'])){
            $id = $_REQUEST['recordId'];
        }
        if (getenv('INSTANCE_NAME') == 'sirva' && !empty($id)) {
            $sql = "SELECT register_sts, sales_stage, sales_person FROM `vtiger_potential` WHERE potentialid=?";
            $result = $db->pquery($sql, [$id]);
            if ($db->num_rows($result) > 0) {
                $row = $result->fetchRow();
                $salesPerson = $row['sales_person'];
                if ($salesPerson != $fieldList['sales_person']) {
                    $sendEmail = true;
                }
                if($row['register_sts'] == 1) {
                    $allowed = ['Closed Won', 'Closed Lost'];
                    // $this->column_fields seems to be the used elsewhere. So I'm rolling with that.
                    if(in_array($this->column_fields['sales_stage'],$allowed) === false && in_array($this->column_fields['sales_stage'], $allowed) !== false) {
                        // I don't know which one is being used, so just set all of them.
                        $fieldList['sales_stage'] = $_REQUEST['sales_stage'] = $this->column_fields['sales_stage'] = $row['sales_stage'];
                    }
                }
            }

            //The following is moved from the save action, as it should always happen on any save
            //Need to update the lead desposition for leads for LMP
            $sql = "SELECT converted_from FROM `vtiger_potential` WHERE `potentialid` = ?";
            $result = $db->pquery($sql, [$id]);
            $leadID = $result->fetchRow()[0];
            if ($leadID != '') {
                $lead = Vtiger_Record_Model::getInstanceById($leadID, 'Leads');
                file_put_contents('logs/devLog.log', "\n LMP lead ID : ".print_r($lead->get('lmp_lead_id'), true), FILE_APPEND);
                if ($lead->get('lmp_lead_id') != '') {
                    $sql = 'SELECT agency_code, `vtiger_vanlinemanager`.vanline_id
                            FROM `vtiger_agentmanager`
                            JOIN `vtiger_vanlinemanager`
                            ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
                            JOIN `vtiger_crmentity`
                            ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.agentid
                            WHERE `vtiger_crmentity`.agentid = ?';
                    $agentVanline = $db->pquery($sql, [$lead->get('agentid')])->fetchRow();

                    $sql = 'SELECT `modcommentsid`, `commentcontent`, `user_name`
                            FROM `vtiger_modcomments`
                            LEFT JOIN `vtiger_users` ON `vtiger_users`.`id` = `vtiger_modcomments`.`userid`
                            WHERE `related_to` = ?';
                    file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
                    $comments = [];
                    $commentsResults = $db->pquery($sql, [$lead->get('id')]);
                    file_put_contents('logs/devLog.log', "\n Record : ".print_r($lead->get('id'), true), FILE_APPEND);
                    while ($row =& $commentsResults->fetchRow()) {
                        $user_name = $row['user_name'];
                        if (empty($user_name)) {
                            $sql2 = 'SELECT `vtiger_users`.user_name FROM `vtiger_users`
                                    JOIN `vtiger_crmentity` ON `vtiger_users`.id = `vtiger_crmentity`.smownerid
                                    WHERE `vtiger_crmentity`.crmid = ?';
                            $result2 = $db->pquery($sql2, [$row['modcommentsid']]);
                            $row2 = $result2->fetchRow();
                            $user_name = $row2['user_name'];
                        }
                        $comments[] = [
                            "NoteID" => $row['modcommentsid'],
                            "NoteDetail" => $row['commentcontent'],
                            "CreatedBy" => $user_name,
                        ];
                    }
                    switch ($lead->get('primary_phone_type')) {
                        case 'Home':
                            $phone_type = 'H';
                            break;
                        case 'Work':
                            $phone_type = 'W';
                            break;
                        case 'Cell':
                            $phone_type = 'C';
                            break;
                        default:
                            $phone_type = '';
                       }
                    if ($lead->get('preferred_pldate')) {
                        $preferred_pldate = DateTime::createFromFormat('Y-m-d', DateTimeField::convertToDBFormat($lead->get('preferred_pldate')))->format('m/d/Y');
                    } else {
                        $preferred_pldate = "";
                    }
                    if ($lead->get('preferred_pddate')) {
                        $preferred_pddate = DateTime::createFromFormat('Y-m-d', DateTimeField::convertToDBFormat($lead->get('preferred_pddate')))->format('m/d/Y');
                    } else {
                        $preferred_pddate = "";
                    }

                    $disposition = $fieldList['sales_stage'];

                    switch ($disposition) {
                        case 'Attempted Contact':
                        case 'Pending':
                        case 'Inactive':
                        case 'Ready to Book':
                        case 'Qualification':
                        case 'Perception Analysis':
                        case 'Value Proposition':
                            $disposition = 'Pending';
                            break;

                        case 'Booked':
                        case 'Closed Won':
                            $disposition = 'Booked';
                            break;

                        case 'Lost':
                        case 'Closed Lost':
                            $disposition = 'Not booked';
                            break;

                        case 'Duplicate':
                        case 'Proposal or Price Quote':
                            $disposition = 'Duplicate';
                            break;

                        case 'Needs Analysis':
                            $disposition = 'Survey Initiated';
                            break;

                        default:
                            $disposition = 'New';
                            break;
                    }

                    //Sirva needs periods on the apartment ones, but not on any others
                    $dwellingType = $lead->get('dwelling_type');

                    if(substr($dwellingType, -3) == 'Apt'){
                        $dwellingType .= '.';
                    }

                    $LMPMessage = [
                        "AgentCode"                   => $agentVanline['agency_code'],
                        "LeadId"                      => $lead->get('lmp_lead_id'),
                        "PrimaryContactFirstNme"      => $lead->get('firstname'),
                        "PrimaryContactLastNme"       => $lead->get('lastname'),
                        "PrimaryContactEmail"         => $lead->get('email'),
                        "PrimaryContactFax"           => $lead->get('origin_fax'),
                        "PrimaryContactHomePhone"     => $lead->get('primary_phone_type') == 'Home' ? $lead->get('phone') : '',
                        "PrimaryContactCellPhone"     => $lead->get('mobile'),
                        "PrimaryContactWorkPhone"     => $lead->get('primary_phone_type') == 'Work' ? $lead->get('phone') : '',
                        "PrimaryContactWorkPhoneExt"  => $lead->get('primary_phone_ext'),
                        "PrimaryContactPhoneType"     => $phone_type,// . ' Phone',
                        "PrimaryContactPerferredTime" => $lead->get('prefer_time'),
                        "PrimaryContactLanguage"      => $lead->get('languages'),
                        "OriginAddressLine1"          => $lead->get('origin_address1'),
                        "OriginAddressLine2"          => $lead->get('origin_address2'),
                        "OriginZip"                   => $lead->get('origin_zip'),
                        "OriginCity"                  => $lead->get('origin_city'),
                        "OriginStateCode"             => $lead->get('origin_state'),
                        "OriginCountryCode"           => $lead->get('origin_country') == 'United States' ? 'US' : 'CA',
                        "DestinationAddressLine1"     => $lead->get('destination_address1'),
                        "DestinationAddressLine2"     => $lead->get('destination_address2'),
                    // if the world made sense this is how this would be done
                        "DestinationZip"              => $fieldList['destination_zip'],
                        "DestinationCity"             => $fieldList['destination_city'],
                        "DestinationStateCode"        => $fieldList['destination_state'],
                    // but it doesn't so this is the way Sirva needs the data
                        //"DestinationZip"              => $lead->get('destination_city'),//$request->get('destination_zip'),
                        //"DestinationCity"             => $lead->get('destination_state'),//$request->get('destination_city'),
                        //"DestinationStateCode"        => $lead->get('destination_zip'),//$request->get('destination_state'),
                    // end super wrongly named field mapping at SIRVA's request.
                        "DestinationCountryCode"      => $lead->get('destination_country') == 'United States' ? 'US' : 'CA',
                        "MoveDate"                    => $preferred_pldate,
                        "ExpectedDeliverDate"         => $preferred_pddate,
                        "Disposition"                 => $disposition,
                        "NotBookedReason"             => $fieldList['disposition_lost_reasons'],
                        "OrderNumber"                 => $fieldList['register_sts_number'],
                        "DwellingTypeName"            => $dwellingType,
                        "FurnishLevel"                => $lead->get('furnish_level'),
                        "SpecialItems"                => $lead->get('special_terms'),
                        "Comment"                     => "",
                        "Brand"                       => $agentVanline['vanline_id'] == '9' ? 'NAVL' : 'AVL', //9 = NAVL || 1 = AVL
                        "KitchenTableClose"           => "",
                        "MovingAVehicle"              => $lead->get('moving_vehicle') == 'on' ? 'Y' : 'N',
                        "FlexibleOnDays"              => $lead->get('flexible_on_days')== 'on' ? 'Y' : 'N',
                        "CCDisposition"               => $lead->get('cc_disposition'),
                        "EmployerAssisting"           => $lead->get('enabled') == 'on' ? 'Y' : 'N',
                        "EmployerName"                => $lead->get('contact_name'),
                        "FeedbackAgentCode"           => $agentVanline['agency_code'],
                        "FeedbackType"                => "",
                        "Feedback"                    => "",
                        "Notes"                       => $comments,
                    ];
                    $jsonMessage = json_encode($LMPMessage, JSON_PRETTY_PRINT);
                    file_put_contents('logs/devLog.log', "\n Message JSON : ".print_r($jsonMessage, true), FILE_APPEND);
                    $curlAuth = $this->curlPOST('grant_type=client_credentials', getenv('SIRVA_SITE') . '/oauth2/AccessRequest');
                    file_put_contents('logs/devLog.log', "\n authResponse : ".print_r($curlAuth, true), FILE_APPEND);
                    $curlResponse = $this->curlPOST($jsonMessage, getenv('SIRVA_SITE').'/LMP/m7/UpdateLeadDetails', json_decode($curlAuth)->access_token, true);
                    file_put_contents('logs/devLog.log', "\n LMP response : ".print_r($curlResponse, true), FILE_APPEND);
                }
            }
        }

        //If we are saving this for the first time and have a sales person, or if the salesperson changes, we need to update the assigned date
        if ($sendEmail || (empty($fieldList['record']) && $fieldList['sales_person'] != '')) {
            $this->column_fields['assigned_date'] = date('Y-m-d');
            //file_put_contents('logs/devLogd.log', "\n TES TOAST.". print_r($module, true), FILE_APPEND);
        }
        parent::saveentity($module, $fileid);
        $fieldList = array_merge($this->column_fields, $_REQUEST);
        //@NOTE: $_REQUEST could have untrue things like module = Leads.  There may be others. OH MY word this is done TWICE
        $fieldList['module'] = $module;

        if ($sendEmail) {
            //file_put_contents('logs/devLog.log', "\n Non-matching sales person found.", FILE_APPEND);
            $sql = "SELECT email1 FROM `vtiger_users` WHERE id=?";
            $res = $db->pquery($sql, [$salesPerson]);

            $userEmail = $res->fields['email1'];

            $sql = "SELECT firstname, lastname, email FROM `vtiger_contactdetails` WHERE contactid=?";
            $res = $db->pquery($sql, [$fieldList['contact_id']]);
            $customerFirst = $res->fields['firstname'];
            $customerLast  = $res->fields['lastname'];
            $customerEmail = $res->fields['email'];

            $sql = "SELECT converted_from FROM `vtiger_potential` WHERE potentialid=? AND converted_from IS NOT NULL";
            $res = $db->pquery($sql, [$fieldList['record']]);
            $row = $res->fetchRow();

            if ($row != null) {
                $convertedFrom = $row['converted_from'];
                //Pull information from Lead from which Opportunity was converted
                $sql = "SELECT lmp_lead_id, primary_phone_type FROM `vtiger_leaddetails` JOIN `vtiger_leadscf` ON `vtiger_leaddetails`.leadid=`vtiger_leadscf`.leadid WHERE `vtiger_leaddetails`.leadid=?";
                $res = $db->pquery($sql, [$convertedFrom]);
                $lmpId = $res->fields['lmp_lead_id'];
                $primaryPhoneType = $res->fields['primary_phone_type'];
                $originPhone1 = $fieldList['origin_phone1'];
                $originPhone1Type = $fieldList['origin_phone1_type'];
                $originPhone2 = $fieldList['origin_phone2'];
                $originPhone2Type = $fieldList['origin_phone2_type'];
                if ($primaryPhoneType == $originPhone2Type) {
                    $primaryPhone = $originPhone2;
                } else {
                    $primaryPhone = $originPhone1;
                }
            } else {
                //Use Origin Phone 1 from Opportunity
                $lmpId = '';
                $primaryPhone = $fieldList['origin_phone1'];
            }


            global $vtiger_current_version;
            $softwareName = 'MoveCRM';
            $developerName = 'IGC Software';
            $developerSite = 'www.igcsoftware.com';
            $logo = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
            $website = 'www.igcsoftware.com';
            $supportTeam = 'MoveCRM Support Team';
            $supportEmail  = getenv('SUPPORT_EMAIL_ADDRESS');
            if(getenv('INSTANCE_NAME') == 'sirva')
            {
                $developerName = 'SIRVA';
            }
            //Fire email
            $subject = $softwareName.' Opportunity Reassignment';
            $message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">The following  lead referred to you has been reassigned or deleted.<br /> <br /> <br />Please manually delete from your QLAB Mobile Customer List as well as any related appointments you may have scheduled.
						<br /><br /> QLAB Lead ID: 46x'.$fieldList['record'].'
						<br />		 LMP Lead ID: '.$lmpId.'
						<br />		 Customer First Name: '.$customerFirst.'
						<br />		 Customer Last Name: '.$customerLast.'
						<br />		 Primary Phone Number: '.$primaryPhone.'
						<br />		 Origin City, State: '.$fieldList['origin_city'].', '.$fieldList['origin_state'].'
						<br />		 Move Type: '.$fieldList['move_type'].'
						<br />		 Customer Email: '.$customerEmail.'
						<div style="background-color:#204e81;padding:5px;vertical-align:middle">
						<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
						</div></div></div>';

            file_put_contents('logs/devLog.log', "\n Email Target: ".$userEmail."\n", FILE_APPEND);
            $mail_status = send_mail('Opportunities', $userEmail, $supportTeam, $supportEmail, $subject, $message, '', '', '', '', '', true);
            file_put_contents('logs/devLog.log', "\n Mail_status : ".print_r($mail_status, true), FILE_APPEND);
        }
        if (empty($fieldList['record'])) {
            $newRecord = true;
            unset($fieldList['register_sts']);
            unset($fieldList['sts_response']);
            unset($fieldList['register_sts_number']);
            if (!empty($fieldList['currentid'])) {
                $fieldList['record'] = $fieldList['currentid'];
            } else {
                //hope for the best!
                $fieldList['record'] = $this->id;
//				$sql                 = "SELECT id FROM `vtiger_crmentity_seq`";
//				$result              = $db->pquery($sql, []);
//				$row                 = $result->fetchRow();
//				$fieldList['record'] = $row[0]++;
//				$sql                 = "UPDATE `vtiger_crmentity_seq` SET id = ?";
//				$result              = $db->pquery($sql, [$fieldList['record']]);
//				//$row                 = $result->fetchRow();
            }
        }

        if (!$fieldList['record']) {
            //@TODO: we should really never get here, but if we do throw an error... it's probably not caught.
            throw new Exception(vtranslate('LBL_RECORD_NOT_FOUND'), -1);
        }

        // Saves the converted_from id so that we can reference back to the lead in syncwebservice
        if ($_REQUEST['view'] == 'SaveConvertLead' || $_REQUEST['mode'] == 'CreateLead') {
            //@TODO this does not seem to make sense but I can't find where currentid or record is set by syncweb or in authtest example... so leaving.
            $recordId = $_REQUEST['record'];
            //if the request was made through a syncwebservice request change the params
            if ($_REQUEST['mode'] == 'CreateLead') {
                $recordId = $_REQUEST['currentid'];
            }
            $sql = "UPDATE `vtiger_potential` SET converted_from = ? WHERE potentialid = ?";
            $result = $db->pquery($sql, array($recordId, $row['potentialid']));
        } else {
            $recordId = $fieldList['record'];
        }

        //Reassign surveys to the correct user if there are surveys assigned to the opp

        /******** TFS30012 - As per JROSS, disable this functionality ********/
        //if (getenv('INSTANCE_NAME') == 'sirva' && isset($_REQUEST['sales_person']) && $_REQUEST['sales_person'] != '' && $_REQUEST['sales_person'] != 0) {
        //    $sql    = "SELECT surveysid FROM `vtiger_surveys` WHERE potential_id = ? \n";
        //    $result = $db->pquery($sql, array($_REQUEST['record']));
        //    if ($result->numRows() > 0) {
        //        while ($row = $db->fetchByAssoc($result)) {
        //            $sql = "UPDATE `vtiger_crmentity` SET smownerid = ? WHERE crmid = ?";
        //            $db->pquery($sql, array($_REQUEST['sales_person'], $row['surveysid']));
        //        }
        //    }
        //}

        //participants save
//        if(getenv('INSTANCE_NAME') != 'graebel' && getenv('IGC_MOVEHQ') && $_REQUEST['isWebserviceConvertLead'] == 1) {
//            //Skip participating agents for non-GVL HQ instances during lead conversion
//        } else {
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                //one issue:  [module] => Leads
                $participatingAgentsModel::saveParticipants($fieldList, $recordId);
            }
//        }

        if (getenv('INSTANCE_NAME') == 'sirva' && $_REQUEST['isWebserviceConvertLead'] == 1) {
            //there is a version in save.php this is for the conversion only it seems
            //Related Opportunities update
            $recId    = $fieldList['record'];
            $recModel = Vtiger_Record_Model::getInstanceById($recId, 'Opportunities');
            $recModel->updateOppFields($recId);
            $sentToMobile = $fieldList['sent_to_mobile'];
            $modName      = $this->module_name;
            $surveyorId   = $fieldList['sales_person'];
            if (
                $surveyorId != NULL &&
                $surveyorId != 0 &&
                $sentToMobile == 0
            ) {
                //Survey Update Notification
                Surveys_Module_Model::SendSurveyUpdateNotification($recId, $surveyorId, $modName);
            }
        }

        //does this in Save.php, this one wouldn't have a $request to work with
        /*$vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ')."Preparing to call saveVehicles\n", FILE_APPEND);
            $vehicleLookupModel::saveVehicles($request);
        }*/

       //OLD STOPS $this->saveStops($fieldList, $recordId, $newRecord);
   }

    protected function getObjectTypeId($db, $modName)
    {
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";

        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id').'x';
    }

    public function saveStops($fieldList, $record, $newRecord = false)
    {
        /*$db = PearDatabase::getInstance();
        $totalStops = $fieldList['numStops'];
        for($i = 0; $i <= $totalStops; $i++){
            $description = $fieldList['stop_description_'.$i];
            $sequence = $fieldList['stop_sequence_'.$i];
            $sirvaStopType = $fieldList['sirva_stop_type_'.$i];
            $stopType = $fieldList['stop_type_'.$i];
            if($description && (($sequence && getenv('INSTANCE_NAME') != 'sirva') || (getenv('INSTANCE_NAME') == 'sirva' && $sirvaStopType)) && $stopType){
                $id = $fieldList['stop_id_'.$i];
                //file_put_contents('logs/devLog.log', "\n id: $id", FILE_APPEND);
                $weight = $fieldList['stop_weight_'.$i];
                //file_put_contents('logs/devLog.log', "\n id: $id", FILE_APPEND);
                $isPrimary = $fieldList['stop_isprimary_'.$i];
                $address1 = $fieldList['stop_address1_'.$i];
                $address2 = $fieldList['stop_address2_'.$i];
                $phone1 = $fieldList['stop_phone1_'.$i];
                $phone2 = $fieldList['stop_phone2_'.$i];
                $phoneType1 = $fieldList['stop_phonetype1_'.$i];
                $phoneType2 = $fieldList['stop_phonetype2_'.$i];
                $city = $fieldList['stop_city_'.$i];
                $type = $fieldList['stop_type_'.$i];
                $contact = $fieldList['stop_contact_'.$i];
                //file_put_contents('logs/devLog.log', "\n type: $type", FILE_APPEND);
                $state = $fieldList['stop_state_'.$i];
                //file_put_contents('logs/devLog.log', "\n contact: $contact", FILE_APPEND);
                $zip = $fieldList['stop_zip_'.$i];
                $country = $fieldList['stop_country_'.$i];
                $date = $fieldList['stop_date_'.$i];
                if(!$id || $id == 'none' || $newRecord){
                    //file_put_contents('logs/devLog.log', "\n STOP ".$i." NO ID!!!!! NEW SAVE", FILE_APPEND);
                    $sql = 'SELECT id FROM `vtiger_extrastops_seq`';
                    $result = $db->pquery($sql, array());
                    $row = $result->fetchRow();
                    $id = $row[0];
                    if(!$id){
                        $id = 1;
                        $sql = 'INSERT INTO `vtiger_extrastops_seq` (id) VALUES (2)';
                        $db->pquery($sql, array());
                    }
                    $sql = 'UPDATE `vtiger_extrastops_seq` SET id = ?';
                    $db->pquery($sql, array(($id+1)));
                    if(getenv('INSTANCE_NAME') == 'sirva'){
                        //sirva specific save for their custom stop type
                        $sql = 'INSERT INTO `vtiger_extrastops` (stopid, sirva_stop_type, stop_description, stop_weight, stop_isprimary, stop_address1, stop_address2, stop_phone1, stop_phone2, stop_phonetype1, stop_phonetype2, stop_city, stop_state, stop_zip, stop_country, stop_date, stop_opp, stop_contact, stop_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                        $db->pquery($sql, array($id, $sirvaStopType, $description, $weight, $isPrimary, $address1, $address2, $phone1, $phone2, $phoneType1, $phoneType2, $city, $state, $zip, $country, $date, $record, $contact, $type));
                    } else{
                        $sql = 'INSERT INTO `vtiger_extrastops` (stopid, stop_sequence, stop_description, stop_weight, stop_isprimary, stop_address1, stop_address2, stop_phone1, stop_phone2, stop_phonetype1, stop_phonetype2, stop_city, stop_state, stop_zip, stop_country, stop_date, stop_opp, stop_contact, stop_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                        $db->pquery($sql, array($id, $sequence, $description, $weight, $isPrimary, $address1, $address2, $phone1, $phone2, $phoneType1, $phoneType2, $city, $state, $zip, $country, $date, $record, $contact, $type));
                    }
                } else {
                    if(getenv('INSTANCE_NAME') == 'sirva'){
                        //sirva specific save for their custom stop type
                        $sql = 'UPDATE `vtiger_extrastops` SET sirva_Stop_type = ?, stop_description = ?, stop_weight = ?, stop_isprimary = ?, stop_address1 = ?, stop_address2 = ?, stop_phone1 = ?, stop_phone2 = ?, stop_phonetype1 = ?, stop_phonetype2 = ?, stop_city = ?, stop_state = ?, stop_zip = ?, stop_country = ?, stop_date = ?, stop_contact = ?, stop_type = ? WHERE stopid = ? AND stop_opp = ?';
                        $db->pquery($sql, array($sirvaStopType, $description, $weight, $isPrimary, $address1, $address2, $phone1, $phone2, $phoneType1, $phoneType2, $city, $state, $zip, $country, $date, $contact, $type, $id, $record));
                    } else{
                        //file_put_contents('logs/devLog.log', "\n STOP ".$i." ID PRESENT!!!!! UPDATING!", FILE_APPEND);
                        $sql = 'UPDATE `vtiger_extrastops` SET stop_sequence = ?, stop_description = ?, stop_weight = ?, stop_isprimary = ?, stop_address1 = ?, stop_address2 = ?, stop_phone1 = ?, stop_phone2 = ?, stop_phonetype1 = ?, stop_phonetype2 = ?, stop_city = ?, stop_state = ?, stop_zip = ?, stop_country = ?, stop_date = ?, stop_contact = ?, stop_type = ? WHERE stopid = ? AND stop_opp = ?';
                        $db->pquery($sql, array($sequence, $description, $weight, $isPrimary, $address1, $address2, $phone1, $phone2, $phoneType1, $phoneType2, $city, $state, $zip, $country, $date, $contact, $type, $id, $record));
                    }
               }
            }
        }*/
    }

    /**
     * Retrieve custom record information of the module
     * @param <Integer> $record - crmid of record
     */
    public function retrieve($record)
    {
        global $adb;
        $fieldList = [];

        //Participating Agents
        $sql = "SELECT * FROM `vtiger_participatingagents` WHERE `rel_crmid` =? AND deleted=0";
        $result = $adb->pquery($sql, [$record]);

        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['opp_participants' . $row['participatingagentsid']] = $row['agents_id'];
                $fieldList['agent_type'       . $row['participatingagentsid']] = $row['agent_type'];
                $fieldList['agent_permission'][$row['participatingagentsid']] = $row['view_level'];
            }
        }

        //Extra stops
        $sql = "SELECT * FROM `vtiger_extrastops` WHERE `extrastops_relcrmid` =?";
        $result = $adb->pquery($sql, [$record]);

        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                isset($fieldList['numStops']) ? $fieldList['numStops']++ : $fieldList['numStops'] = 1;
                $fieldList['extrastops_description_'.$fieldList['numStops']] = $row['extrastops_description'];
                $fieldList['sextrastops_sequence_'   .$fieldList['numStops']] = $row['extrastops_sequence'];
                $fieldList['extrastops_id_'         .$fieldList['numStops']] = $row['extrastopsid'];
                $fieldList['extrastops_weight_'     .$fieldList['numStops']] = $row['extrastops_weight'];
                $fieldList['extrastops_isprimary_'  .$fieldList['numStops']] = $row['extrastops_isprimary'];
                $fieldList['extrastops_address1_'   .$fieldList['numStops']] = $row['extrastops_address1'];
                $fieldList['extrastops_address2_'   .$fieldList['numStops']] = $row['extrastops_address2'];
                $fieldList['extrastops_phone1_'     .$fieldList['numStops']] = $row['extrastops_phone1'];
                $fieldList['extrastops_phone2_'     .$fieldList['numStops']] = $row['extrastops_phone2'];
                $fieldList['extrastops_phonetype1_' .$fieldList['numStops']] = $row['extrastops_phonetype1'];
                $fieldList['extrastops_phonetype2_' .$fieldList['numStops']] = $row['extrastops_phonetype2'];
                $fieldList['extrastops_city_'       .$fieldList['numStops']] = $row['extrastops_city'];
                $fieldList['extrastops_type_'       .$fieldList['numStops']] = $row['extrastops_type'];
                $fieldList['extrastops_contact_'    .$fieldList['numStops']] = $row['extrastops_contact'];
                $fieldList['extrastops_state_'      .$fieldList['numStops']] = $row['extrastops_state'];
                $fieldList['extrastops_zip_'        .$fieldList['numStops']] = $row['extrastops_zip'];
                $fieldList['extrastops_country_'    .$fieldList['numStops']] = $row['extrastops_country'];
                $fieldList['extrastops_date_'       .$fieldList['numStops']] = $row['extrastops_date'];
            }
        }


        return $fieldList;
    }
        /*
        //screw it this is so bad I can't even begin to make it work, I tried I really did
        file_put_contents('logs/devLog.log', "\n Data: ".print_r($request, true), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n:".print_r(function_exists('vtws_create'), true), FILE_APPEND);
        $db = PearDatabase::getInstance();
        //$totalParticipants = $request->get('numAgents');
        //$participantArray = array();
        $agent_types = $request->get('agent_type');
        //$oppParticipants = $request->get('opp_participants');
        $agent_persmissions = $request->get('agent_permission');
        $table_ids = $request->get('table_id');
        $agent_statuses = $request->get('agent_status');
        // file_put_contents('logs/devLog.log', "\n Data:".print_r(array('type'=>$agent_types, 'agents'=>$oppParticipants, 'permissions'=>$agent_persmissions, 'tableId'=>$table_ids, 'statues'=>$agent_statuses), true), FILE_APPEND);
        for($i = 1; $i<=count($table_ids); $i++){
            $participant = $request->get('opp_participants'.$i);
            $status = intval($agent_statuses[$i]);
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $row = 0;
            $db->startTransaction();
            $type = '';
            $sql = 'SELECT * FROM `vtiger_participating_agents` WHERE crmentity_id=? AND agent_id=? AND agent_type=?';
            $result = $db->pquery($sql, array($request->get('record'), $participant, $agent_types[$i]));
            $result = $result->fetchRow();

            //file_put_contents('logs/devLog.log', "\n:".print_r(array($request->get('record'), $oppParticipants[$i], $agent_types[$i]), true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n: Result ".print_r($result->fetchRow(), true), FILE_APPEND);
            if($table_ids[$i]=='none'){
                $sql = 'INSERT INTO `vtiger_participating_agents`(crmentity_id, agent_id, agent_type, permission, status, modified_by) VALUES (?,?,?,?,?,?)';
                $db->pquery($sql, array($request->get('record'), $participant, $agent_types[$i], $agent_persmissions[$i], $status, $currentUser->getId()));
                $result = $db->pquery("SELECT * FROM `vtiger_participating_agents` ORDER BY id DESC LIMIT 1", array());
                $row = $result->fetchRow();
                $table_ids[$i]=$row['id'];
                $type = 'insert';
                // file_put_contents('logs/devLog.log', "\nInsert Values: ".print_r( array($request->get('record'), $oppParticipants[$i], $agent_types[$i], $agent_persmissions[$i], $status), true), FILE_APPEND);
                // file_put_contents('logs/devLog.log', "\nInsert SQL: ".print_r('INSERT INTO `vtiger_participating_agents`(crmentity_id, agent_id, agent_type, permission, status) VALUES (?,?,?,?,?)', true), FILE_APPEND);
                // file_put_contents('logs/devLog.log', "\nInsert Reuturn: ".print_r($row, true), FILE_APPEND);
            }
            else{
                //getting the old group
                $result = $db->pquery("SELECT * FROM `vtiger_participating_agents` WHERE id = ?", array($table_ids[$i]));
                $row = $result->fetchRow();
                file_put_contents('logs/devLog.log', "\n old_row : ".print_r($row, true), FILE_APPEND);
                //updating
                $sql = "UPDATE `vtiger_participating_agents` SET agent_id = ?, agent_type = ?, permission = ?, status = ? WHERE id = ?";
                $sqlData = array($participant, $agent_types[$i], $agent_persmissions[$i], $status, $table_ids[$i]);
                file_put_contents('logs/devLog.log', "\n This is what's being done in the update :", FILE_APPEND);
                file_put_contents('logs/devLog.log', "\n sql : ".$sql, FILE_APPEND);
                file_put_contents('logs/devLog.log', "\n SqlData : ".print_r($sqlData, true), FILE_APPEND);
                $db->pquery($sql, $sqlData);

                $type = 'update';
            }
            $group_new =  $db->pquery("SELECT vtiger_groups.groupid FROM vtiger_agents, vtiger_groups WHERE
                                              vtiger_agents.agentname = vtiger_groups.groupname AND vtiger_agents.agentsid=?", array($participant));
            $group_old =  $db->pquery("SELECT vtiger_groups.groupid FROM vtiger_agents, vtiger_groups WHERE vtiger_agents.agentname = vtiger_groups.groupname AND vtiger_agents.agentsid=?", array($row['agent_id']));
            $group = array('old'=>$group_old->fetchRow()[0], 'new'=>$group_new->fetchRow()[0]);
            //if(!$result)$db->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);
            //$db->hasFailedTransaction(); --returns true/fasle
            $db->completeTransaction();
            $data = [];
            $agent_variety = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent'];
            //if this is an insert (meaning a newly added agent) or a change of agent
            if($type=='insert' || ($row['agent_id']!=$participant)){
                // file_put_contents('logs/devLog.log', "\nNotifying agent ".$oppParticipants[$i]." of the new request! >:)",FILE_APPEND);

                $data[] = array (
                    'inbox_message' => 'You have a '.$agent_variety[$agent_types[$i]].' Request for '.$request->get('potentialname').' from '.$currentUser->getDisplayName(),
                    'inbox_priority'=> 'MEDIUM',
                    'inbox_announce'  => '1',
                    'inbox_read'  => '0',
                    'inbox_for_crmentity' => $request->get('record'),
                    'assigned_user_id' => vtws_getWebserviceEntityId('Groups', $group['new']),
                    'inbox_from' => vtws_getWebserviceEntityId('Users', $currentUser->getId()),
                    'inbox_type' => 'Participating Agent Request',
                    'inbox_link' => $table_ids[$i]
                );

            }
            //if you have been removed from a case and this is a new removal
            if(($row['status']!=$status && $status==2) || ($row['agent_id']!=$participant)){
                // file_put_contents('logs/devLog.log', "\nNotifying agent ".$row['agent_id']." of the cancelation! :`(",FILE_APPEND);
                $data[] = array (
                    'inbox_message' => $currentUser->getDisplayName().' Has Canceled Your '.$agent_variety[$agent_types[$i]].' Request',
                    'inbox_priority'=> 'MEDIUM',
                    'inbox_announce'  => '1',
                    'inbox_read'  => '0',
                    'inbox_for_crmentity' => $request->get('record'),
                    'assigned_user_id' => vtws_getWebserviceEntityId('Groups', $group['old']),
                    'inbox_from' => vtws_getWebserviceEntityId('Users', $currentUser->getId()),
                    'inbox_type' => 'Participating Agent Request',
                    'inbox_link' => $table_ids[$i]
                );
            }


            if(!empty($data)){
                try{
                    foreach($data as $datum){
                        $adminToBe = new Users();
                        $current_user = $adminToBe->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                        $cool = vtws_create('Inbox', $datum, $current_user);
                        // file_put_contents('logs/devLog.log', "\nNotifying agent Original ".print_r($datum, true),FILE_APPEND);
                        // file_put_contents('logs/devLog.log', "\nNotifying agent Return ".print_r($cool, true),FILE_APPEND);
                    }
                } catch (WebServiceException $ex) {
                    file_put_contents('logs/devLog.log', "\n Error:".print_r($ex->getMessage(), true), FILE_APPEND);
                }
            }
        }
        */

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

    protected function getObjTypeId($modName)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }

    public function curlPOST($post_string, $webserviceURL, $key = '', $auth = false)
    {
        $ch = curl_init();

        if (!$auth) {
            $headers = [
                'Authorization: Basic ' . getenv('SIRVA_KEY'),
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            $headers = [
                'Authorization: Bearer ' . $key,
                'Host: ' . parse_url(getenv('SIRVA_SITE'))['host'],
                'Content-Type: application/json',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }
}
