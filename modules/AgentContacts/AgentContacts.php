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

class AgentContacts extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_agentcontacts';
    public $table_index= 'agentcontactsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_agentcontactscf', 'agentcontactsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_agentcontacts', 'vtiger_agentcontactscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_agentcontacts' => 'agentcontactsid',
        'vtiger_agentcontactscf'=>'agentcontactsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Last Name' => array('agentcontacts', 'acontacts_lname'),
        'Department' => array('agentcontacts', 'acontacts_dept'),
        'Agent Name' => array('agentcontacts', 'acontacts_agents'),
        'Office Phone' => array('agentcontacts', 'acontacts_p1'),
        'Mobile Phone' => array('agentcontacts', 'acontacts_p2'),
        'Primary Email' => array('agentcontacts', 'acontacts_email1'),
        'First Name' => array('agentcontacts', 'acontacts_fname'),
        //'Assigned To' => Array('crmentity','smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
            'Last Name' => 'acontacts_lname',
        'Department' => 'acontacts_dept',
        'Agent Name' => 'acontacts_agents',
        'Office Phone' => 'acontacts_p1',
        'Mobile Phone' => 'acontacts_p2',
        'Primary Email' => 'acontacts_email1',
        'First Name' => 'acontacts_fname',
        //'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'acontacts_fname';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'First Name' => array('agentcontacts', 'acontacts_fname'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'First Name' => 'acontacts_fname',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('acontacts_fname');

    // For Alphabetical search
    public $def_basicsearch_col = 'acontacts_fname';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'acontacts_fname';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('acontacts_fname','assigned_user_id');

    public $default_order_by = 'acontacts_fname';
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
}
