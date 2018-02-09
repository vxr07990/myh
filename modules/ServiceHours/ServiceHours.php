<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

include_once 'modules/Vtiger/CRMEntity.php';

class ServiceHours extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_servicehours';
    public $table_index = 'servicehoursid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_servicehourscf', 'servicehoursid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_servicehours', 'vtiger_servicehourscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_servicehours' => 'servicehoursid',
        'vtiger_servicehourscf' => 'servicehoursid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_SERVICEHOURS_ID' => array('servicehours', 'servhours_id'),
        'LBL_ACTUAL_DATE' => array('servicehours', 'actual_start_date'),
        'LBL_TOTAL_WORKED_HOURS' => array('servicehours', 'total_hours'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_SERVICEHOURS_ID' => 'servhours_id',
        'LBL_ACTUAL_DATE' =>  'actual_start_date',
        'LBL_TOTAL_WORKED_HOURS' =>'total_hours',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'servhours_id';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_SERVICEHOURS_ID' => array('servicehours', 'servhours_id'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_SERVICEHOURS_ID' => 'servhours_id',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('servhours_id');
    // For Alphabetical search
    public $def_basicsearch_col = 'servhours_id';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'servhours_id';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('servhours_id', 'assigned_user_id');
    public $default_order_by = 'actual_start_date';
    public $default_sort_order = 'DESC';

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
