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

class ItemCodesMapping extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_itemcodesmapping';
    public $table_index= 'itemcodesmappingid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_itemcodesmappingcf', 'itemcodesmappingid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_itemcodesmapping', 'vtiger_itemcodesmappingcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_itemcodesmapping' => 'itemcodesmappingid',
        'vtiger_itemcodesmappingcf'=>'itemcodesmappingid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_BUSINESSLINE' => array('itemcodesmapping', 'itcmapping_businessline'),
        'LBL_BILLING_TYPE' => array('itemcodesmapping', 'itcmapping_billingtype'),
        'LBL_AUTHORITY' => array('itemcodesmapping', 'itcmapping_authority'),
        'LBL_GLCODE' => array('itemcodesmapping', 'itcmapping_glcode'),
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_BUSINESSLINE' => 'itcmapping_businessline',
        'LBL_BILLING_TYPE' => 'itcmapping_billingtype',
        'LBL_AUTHORITY' => 'itcmapping_authority',
        'LBL_GLCODE' => 'itcmapping_glcode',
    );

    // Make the field link to detail view
    public $list_link_field = 'itcmapping_businessline';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_BUSINESSLINE' => array('itemcodesmapping', 'itcmapping_businessline'),
        'LBL_BILLING_TYPE' => array('itemcodesmapping', 'itcmapping_billingtype'),
        'LBL_AUTHORITY' => array('itemcodesmapping', 'itcmapping_authority'),
        'LBL_GLCODE' => array('itemcodesmapping', 'itcmapping_glcode'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_BUSINESSLINE' => 'itcmapping_businessline',
        'LBL_BILLING_TYPE' => 'itcmapping_billingtype',
        'LBL_AUTHORITY' => 'itcmapping_authority',
        'LBL_GLCODE' => 'itcmapping_glcode',
    );

    // For Popup window record selection
    public $popup_fields = array('itcmapping_businessline');

    // For Alphabetical search
    public $def_basicsearch_col = 'itcmapping_businessline';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'itcmapping_businessline';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('itcmapping_businessline','itcmapping_billingtype','itcmapping_authority','itcmapping_glcode');

    public $default_order_by = 'itcmapping_businessline';
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
