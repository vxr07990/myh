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

class VehicleMaintenance extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vehiclemaintenance';
    public $table_index = 'vehiclemaintenanceid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vehiclemaintenancecf', 'vehiclemaintenanceid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vehiclemaintenance', 'vtiger_vehiclemaintenancecf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vehiclemaintenance' => 'vehiclemaintenanceid',
        'vtiger_vehiclemaintenancecf' => 'vehiclemaintenanceid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_MAINTENANCE_NO' => array('vehiclemaintenance', 'maintenance_number'),
        'LBL_MAINTENANCE_DATE' => array('vehiclemaintenance', 'maintenance_date'),
        'LBL_MAINTENANCE_REASON' => array('vehiclemaintenance', 'maintenance_reason'),
        'LBL_MAINTENANCE_ODOMETER' => array('vehiclemaintenance', 'maintenance_odometer'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_MAINTENANCE_NO' => 'maintenance_number',
        'LBL_MAINTENANCE_DATE' => 'maintenance_date',
        'LBL_MAINTENANCE_REASON' => 'maintenance_reason',
        'LBL_MAINTENANCE_ODOMETER' => 'maintenance_odometer',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'maintenance_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_MAINTENANCE_NO' => array('vehiclemaintenance', 'maintenance_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_MAINTENANCE_NO' => 'maintenance_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('maintenance_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'maintenance_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'maintenance_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('maintenance_number', 'assigned_user_id');
    public $default_order_by = 'maintenance_number';
    public $default_sort_order = 'ASC';

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
