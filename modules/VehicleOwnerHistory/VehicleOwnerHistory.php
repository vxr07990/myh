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

class VehicleOwnerHistory extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vehicleownerhistory';
    public $table_index = 'vehicleownerhistoryid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vehicleownerhistorycf', 'vehicleownerhistoryid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vehicleownerhistory', 'vtiger_vehicleownerhistorycf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vehicleownerhistory' => 'vehicleownerhistoryid',
        'vtiger_vehicleownerhistorycf' => 'vehicleownerhistoryid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OWNER_HISTORY_NO' => array('vehicleownerhistory', 'ownerhistory_number'),
        'LBL_OWNER_HISTORY_VEHICLE_NO' => array('vehicleownerhistory', 'ownerhistory_vehicle'),
        'LBL_SPONSOR_AGENT_NO' => array('vehicleownerhistory', 'vehicle_sponsor_agent'),
        'LBL_TITLE_OWNER_AGENT_NO' => array('vehicleownerhistory', 'vehicle_titleowner_agent'),
        'LBL_SPONSOR_TYPE' => array('vehicleownerhistory', 'sponsor_type'),
        'LBL_TITLE_OWNER_TYPE' => array('vehicleownerhistory', 'titleowner_type'),
        'LBL_PURCHASE_DATE' => array('vehicleownerhistory', 'purchase_date'),
        'LBL_TERM_DATE' => array('vehicleownerhistory', 'term_date'),
        'LBL_EARLY_TERM_DATE' => array('vehicleownerhistory', 'early_term_date'),
        'LBL_OWNER_HISTORY_ADDRESS' => array('vehicleownerhistory','ownerhistory_address'),
        'LBL_LEINHOLDER' => array('vehicleownerhistory','ownerhistory_leinholder'),
        'LBL_OWNER_HISTORY_PHONE' => array('vehicleownerhistory','ownerhistory_phone'),
        'LBL_OWNER_HISTORY_EMAIL' => array('vehicleownerhistory','ownerhistory_email'),
        'LBL_PURCHASE_PRICE' => array('vehicleownerhistory','ownerhistory_purchaseprice'),
        'LBL_MILEAGE' => array('vehicleownerhistory','ownerhistory_mileage'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_OWNER_HISTORY_NO' => 'ownerhistory_number',
        'LBL_OWNER_HISTORY_VEHICLE_NO' => 'ownerhistory_vehicle',
        'LBL_SPONSOR_AGENT_NO' => 'vehicle_sponsor_agent',
        'LBL_TITLE_OWNER_AGENT_NO' => 'vehicle_titleowner_agent',
        'LBL_SPONSOR_TYPE' => 'sponsor_type',
        'LBL_TITLE_OWNER_TYPE' => 'titleowner_type',
        'LBL_PURCHASE_DATE' => 'purchase_date',
        'LBL_TERM_DATE' => 'term_date',
        'LBL_EARLY_TERM_DATE' => 'early_term_date',
        'LBL_OWNER_HISTORY_ADDRESS' => 'ownerhistory_address',
        'LBL_LEINHOLDER' => 'ownerhistory_leinholder',
        'LBL_OWNER_HISTORY_PHONE' => 'ownerhistory_phone',
        'LBL_OWNER_HISTORY_EMAIL' => 'ownerhistory_email',
        'LBL_PURCHASE_PRICE' => 'ownerhistory_purchaseprice',
        'LBL_MILEAGE' => 'ownerhistory_mileage',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'ownerhistory_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OWNER_HISTORY_NO' => array('vehicleownerhistory', 'ownerhistory_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_OWNER_HISTORY_NO' => 'ownerhistory_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('ownerhistory_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'ownerhistory_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'ownerhistory_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('ownerhistory_number', 'assigned_user_id');
    public $default_order_by = 'ownerhistory_number';
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
