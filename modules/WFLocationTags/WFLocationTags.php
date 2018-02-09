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

class WFLocationTags extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_wflocationtags';
    public $table_index= 'wflocationtagsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_wflocationtagscf', 'wflocationtagsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_wflocationtags', 'vtiger_wflocationtagscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_wflocationtags' => 'wflocationtagsid',
        'vtiger_wflocationtagscf'=>'wflocationtagsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_WFLOCATIONTAGS_ID' => array('wflocationtags', 'wflocationtagsid'),
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_WFLOCATIONTAGS_ID' => 'wflocationtagsid',
    );

    // Make the field link to detail view
    public $list_link_field = 'wflocationtagsid';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_WFLOCATIONTAGS_ID' => array('wflocationtags', 'wflocationtagsid'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_WFLOCATIONTAGS_ID' => 'wflocationtagsid',
    );

    // For Popup window record selection
    public $popup_fields = array('wflocationtagsid');

    // For Alphabetical search
    public $def_basicsearch_col = 'wflocationtagsid';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'wflocationtagsid';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('wflocationtagsid');

    public $default_order_by = 'wflocationtagsid';
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
