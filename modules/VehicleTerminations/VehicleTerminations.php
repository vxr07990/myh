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

class VehicleTerminations extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vehicleterminations';
    public $table_index = 'vehicleterminationsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vehicleterminationscf', 'vehicleterminationsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vehicleterminations', 'vtiger_vehicleterminationscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vehicleterminations' => 'vehicleterminationsid',
        'vtiger_vehicleterminationscf' => 'vehicleterminationsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_TERMINATION_NO' => array('vehicleterminations', 'termination_number'),
        'LBL_TERMINATION_VEHICLE_NO' => array('vehicleterminations', 'termination_vehicle'),
        'LBL_TERMINATION_REASON' => array('vehicleterminations', 'termination_reason'),
        'LBL_TERMINATION_DATE' => array('vehicleterminations', 'termination_date'),
        'LBL_TERMINATION_DRIVER_NO' => array('vehicleterminations', 'termination_driver'),
        'LBL_TERMINATION_COMMENTS' => array('vehicleterminations', 'termination_comments'),
        'LBL_TERMINATION_PROBLEM_SOLVED' => array('vehicleterminations', 'termination_problem_solved'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_TERMINATION_NO' => 'termination_number',
        'LBL_TERMINATION_VEHICLE_NO' => 'termination_vehicle',
        'LBL_TERMINATION_REASON' => 'termination_reason',
        'LBL_TERMINATION_DATE' => 'termination_date',
        'LBL_TERMINATION_DRIVER_NO' => 'termination_driver',
        'LBL_TERMINATION_COMMENTS' => 'termination_comments',
        'LBL_TERMINATION_PROBLEM_SOLVED' => 'termination_problem_solved',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'termination_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_TERMINATION_NO' => array('vehicleterminations', 'termination_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_TERMINATION_NO' => 'termination_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('termination_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'termination_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'termination_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('termination_number', 'assigned_user_id');
    public $default_order_by = 'termination_number';
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
