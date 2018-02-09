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

class VanlineContacts extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vanlinecontacts';
    public $table_index= 'vanlinecontactsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vanlinecontactscf', 'vanlinecontactsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vanlinecontacts', 'vtiger_vanlinecontactscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vanlinecontacts' => 'vanlinecontactsid',
        'vtiger_vanlinecontactscf'=>'vanlinecontactsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

        'Last Name' => array('vanlinecontacts', 'vcontacts_lname'),
        'Department'=>array('vanlinecontacts', 'vcontacts_dept'),
        'Vanline Name'=> array('vanlinecontacts', 'vcontacts_vanlines'),
        'Office Phone' => array('vanlinecontacts', 'vcontacts_p3'),
        'Mobile Phone' => array('vanlinecontacts', 'vcontacts_p2'),
        'Primary Email' => array('vanlinecontacts', 'vcontacts_email1'),
        'First Name' => array('vanlinecontacts', 'vcontacts_fname'),
        //'Address 1' => Array('vanlinecontacts', 'vcontacts_address1')

        //'Assigned To' => Array('crmentity','smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Last Name' => 'vcontacts_lname',
        'Department' => 'vcontacts_dept',
        'Vanline Name' => 'vcontacts_vanlines',
        'Office Phone' => 'vcontacts_p3',
        'Mobile Phone' => 'vcontacts_p2',
        'Primary Email' => 'vcontacts_email1',
        'First Name' => 'vcontacts_fname',
        //'Address 1' => 'vcontacts_address1',

        //'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'vcontacts_fname';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'First Name' => array('vanlinecontacts', 'vcontacts_fname'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'First Name' => 'vcontacts_fname',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('vcontacts_fname');

    // For Alphabetical search
    public $def_basicsearch_col = 'vcontacts_fname';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'vcontacts_fname';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('vcontacts_fname','assigned_user_id');

    public $default_order_by = 'vcontacts_fname';
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
