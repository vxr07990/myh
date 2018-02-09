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

class VehicleTransfers extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vehicletransfers';
    public $table_index = 'vehicletransfersid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vehicletransferscf', 'vehicletransfersid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vehicletransfers', 'vtiger_vehicletransferscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vehicletransfers' => 'vehicletransfersid',
        'vtiger_vehicletransferscf' => 'vehicletransfersid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_TRANSFERS_NO' => array('vehicletransfers', 'transfer_number'),
        'LBL_TRANSFERS_VEHICLE_NO' => array('vehicletransfers', 'transfer_vehicle'),
        'LBL_TRANSFERS_DRIVER_NO' => array('vehicletransfers', 'transfer_driver'),
        'LBL_TRANSFERS_OLD_AGENT_NO' => array('vehicletransfers', 'transfer_old_agent'),
        'LBL_TRANSFERS_NEW_AGENT_NO' => array('vehicletransfers', 'transfer_new_agent'),
        'LBL_TRANSFERS_DATE' => array('vehicletransfers', 'transfers_date'),
        'LBL_INSPECTIONS_INSP_NAME' => array('vehicletransfers', 'transfers_from_unit'),
        'LBL_INSPECTIONS_INSP_NAME' => array('vehicletransfers', 'transfers_to_unit'),
        'LBL_TRANSFERS_COMMENTS' => array('vehicletransfers', 'transfers_comments'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_TRANSFERS_NO' => 'transfer_number',
        'LBL_TRANSFERS_VEHICLE_NO' => 'transfer_vehicle',
        'LBL_TRANSFERS_DRIVER_NO' => 'transfer_driver',
        'LBL_TRANSFERS_OLD_AGENT_NO' => 'transfer_old_agent',
        'LBL_TRANSFERS_NEW_AGENT_NO' => 'transfer_new_agent',
        'LBL_TRANSFERS_DATE' => 'transfers_date',
        'LBL_INSPECTIONS_INSP_NAME' => 'transfers_from_unit',
        'LBL_INSPECTIONS_INSP_NAME' => 'transfers_to_unit',
        'LBL_TRANSFERS_COMMENTS' => 'transfers_comments',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'transfer_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_TRANSFERS_NO' => array('vehicletransfers', 'transfer_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_TRANSFERS_NO' => 'transfer_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('transfer_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'transfer_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'transfer_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('transfer_number', 'assigned_user_id');
    public $default_order_by = 'transfer_number';
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
