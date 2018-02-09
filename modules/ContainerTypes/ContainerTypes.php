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

class ContainerTypes extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_containertypes';
    public $table_index= 'containertypesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_containertypescf', 'containertypesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_containertypes', 'vtiger_containertypescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_containertypes' => 'containertypesid',
        'vtiger_containertypescf'=>'containertypesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_CONTAINERTYPES_NAME' => array('containertypes', 'containertypes_name'),
        'LBL_CONTAINERTYPES_ASSIGNED_TO' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_CONTAINERTYPES_NAME' => 'containertypes_name',
        'LBL_CONTAINERTYPES_ASSIGNED_TO' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'containertypes_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_CONTAINERTYPES_NAME' => array('containertypes', 'containertypes_name'),
        'LBL_CONTAINERTYPES_ASSIGNED_TO' => array('vtiger_crmentity','assigned_user_id'),
        //'Owner' => Array('vtiger_crmentity', '')
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_CONTAINERTYPES_NAME' => 'containertypes_name',
        'LBL_CONTAINERTYPES_ASSIGNED_TO' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('containertypes_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'containertypes_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'containertypes_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('containertypes_name','assigned_user_id');
    //var $mandatory_fields = Array('emprole_desc');

    public $default_order_by = 'containertypes_name';
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
