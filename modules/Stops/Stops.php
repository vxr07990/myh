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

class Stops extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_stops';
    public $table_index= 'stopsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_stopscf', 'stopsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_stops', 'vtiger_stopscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_stops' => 'stopsid',
        'vtiger_stopscf'=>'stopsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Stop Sequence' => array('stops ', 'stop_sequence'),
        'Stop Type' => array('stops', 'stop_type'),
        'Stop Date From' => array('stops', 'stop_datefrom'),
        'Stop Date To' => array('stops', 'stop_dateto'),
        'Stop Description' => array('stops', 'stop_description')
        //'Accident Time' => Array('accidents', 'accidents_time')
        //'Assigned To' => Array('accidents','smownerid'),
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Stop Sequence' => 'stop_sequence',
        'Stop Type' => 'stop_type',
        'Stop Date From' => 'stop_datefrom',
        'Stop Date To' => 'stop_dateto',
        'Stop Description' => 'stop_description',
        //'Accident Time' => 'accidents_time',
        //'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'stop_sequence';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'Stops' => Array('stops', 'stops_name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        //'Stops' => 'stops_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('stop_sequence');

    // For Alphabetical search
    public $def_basicsearch_col = 'stop_sequence';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'stop_sequence';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('stop_sequence');

    public $default_order_by = 'stop_sequence';
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
