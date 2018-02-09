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

class VendorsOutofService extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_vendorsoutofservice';
    public $table_index = 'vendorsoutofserviceid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_vendorsoutofservicecf', 'vendorsoutofserviceid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_vendorsoutofservice', 'vtiger_vendorsoutofservicecf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_vendorsoutofservice' => 'vendorsoutofserviceid',
        'vtiger_vendorsoutofservicecf' => 'vendorsoutofserviceid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OUTOFSERVICE_NO' => array('vendorsoutofservice', 'voos_number'),
        'LBL_OUTOFSERVICE_VENDOR' => array('vendorsoutofservice', 'voos_vendorid'),
        'LBL_OUTOFSERVICE_STATUS' => array('vendorsoutofservice', 'voos_status'),
        'LBL_OUTOFSERVICE_REASON' => array('vendorsoutofservice', 'voos_reason'),
        'LBL_OUTOFSERVICE_EFFECTIVE_DATE' => array('vendorsoutofservice', 'voos_effective_date'),
        'LBL_OUTOFSERVICE_REINSTATED_DATE' => array('vendorsoutofservice', 'voos_reinstated_date'),
        'LBL_OUTOFSERVICE_COMMENTS' => array('vendorsoutofservice', 'voos_comments'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_OUTOFSERVICE_NO' => 'voos_number',
        'LBL_OUTOFSERVICE_VENDOR' => 'voos_vendorid',
        'LBL_OUTOFSERVICE_STATUS' => 'voos_status',
        'LBL_OUTOFSERVICE_REASON' => 'voos_reason',
        'LBL_OUTOFSERVICE_EFFECTIVE_DATE' => 'voos_effective_date',
        'LBL_OUTOFSERVICE_REINSTATED_DATE' => 'voos_reinstated_date',
        'LBL_OUTOFSERVICE_COMMENTS' => 'voos_comments',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'voos_number';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OUTOFSERVICE_NO' => array('vendorsoutofservice', 'voos_number'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_INSPECTIONS_NO' => 'voos_number',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('voos_number');
    // For Alphabetical search
    public $def_basicsearch_col = 'voos_number';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'voos_number';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('voos_number', 'assigned_user_id');
    public $default_order_by = 'voos_number';
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
