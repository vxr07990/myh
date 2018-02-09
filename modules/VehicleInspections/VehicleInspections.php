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

class VehicleInspections extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vehicleinspections';
    public $table_index = 'vehicleinspectionsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vehicleinspectionscf', 'vehicleinspectionsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vehicleinspections', 'vtiger_vehicleinspectionscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vehicleinspections' => 'vehicleinspectionsid',
        'vtiger_vehicleinspectionscf' => 'vehicleinspectionsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_INSPECTIONS_NO' => array('vehicleinspections', 'inspection_number'),
        'LBL_INSPECTIONS_DUE' => array('vehicleinspections', 'inspection_duedate'),
        'LBL_INSPECTIONS_TYPE' => array('vehicleinspections', 'inspection_type'),
        'LBL_INSPECTIONS_ODOMETER' => array('vehicleinspections', 'inspection_odometer'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_INSPECTIONS_NO' => 'inspection_number',
        'LBL_INSPECTIONS_DUE' => 'inspection_duedate',
        'LBL_INSPECTIONS_TYPE' => 'inspection_type',
        'LBL_INSPECTIONS_ODOMETER' => 'inspection_odometer',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'inspection_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_INSPECTIONS_NO' => array('vehicleinspections', 'inspection_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_INSPECTIONS_NO' => 'inspection_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('inspection_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'inspection_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'inspection_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('inspection_number', 'assigned_user_id');
    public $default_order_by = 'inspection_number';
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
