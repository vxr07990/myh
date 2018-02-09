<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//#this is for form not really functionaliy
//because we aren't displaying this

include_once 'modules/Vtiger/CRMEntity.php';

class SMSResponder extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_smsresponder';
    public $table_index= 'smsresponderid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_smsrespondercf', 'smsresponderid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_smsresponder', 'vtiger_smsrespondercf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_smsresponder' => 'smsresponderid',
        'vtiger_smsrespondercf'=>'smsresponderid');

//	/**	/**
//	 * Mandatory for Listing (Related listview)	 * Mandatory for Listing (Related listview)
//	 */	 */
//	var $list_fields = Array (	var $list_fields = Array (
//		/* Format: Field Label => Array(tablename, columnname) */		/* Format: Field Label => Array(tablename, columnname) */
//		// tablename should not have prefix 'vtiger_'		// tablename should not have prefix 'vtiger_'
//		'<entityfieldlabel>' => Array('smsresponder', '<entitycolumn>'),		'<entityfieldlabel>' => Array('smsresponder', '<entitycolumn>'),
//		'Assigned To' => Array('crmentity','smownerid')		'Assigned To' => Array('crmentity','smownerid')
//	);	);
//	var $list_fields_name = Array (	var $list_fields_name = Array (
//		/* Format: Field Label => fieldname */		/* Format: Field Label => fieldname */
//		'<entityfieldlabel>' => '<entityfieldname>',		'<entityfieldlabel>' => '<entityfieldname>',
//		'Assigned To' => 'assigned_user_id',		'Assigned To' => 'assigned_user_id',
//	);	);
//
//	// Make the field link to detail view	// Make the field link to detail view
//	var $list_link_field = '<entityfieldname>';	var $list_link_field = '<entityfieldname>';
//
//	// For Popup listview and UI type support	// For Popup listview and UI type support
//	var $search_fields = Array(	var $search_fields = Array(
//		/* Format: Field Label => Array(tablename, columnname) */		/* Format: Field Label => Array(tablename, columnname) */
//		// tablename should not have prefix 'vtiger_'		// tablename should not have prefix 'vtiger_'
//		'<entityfieldlabel>' => Array('smsresponder', '<entitycolumn>'),		'<entityfieldlabel>' => Array('smsresponder', '<entitycolumn>'),
//		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
//	);	);
//	var $search_fields_name = Array (	var $search_fields_name = Array (
//		/* Format: Field Label => fieldname */		/* Format: Field Label => fieldname */
//		'<entityfieldlabel>' => '<entityfieldname>',		'<entityfieldlabel>' => '<entityfieldname>',
//		'Assigned To' => 'assigned_user_id',		'Assigned To' => 'assigned_user_id',
//	);	);
//
//	// For Popup window record selection	// For Popup window record selection
//	var $popup_fields = Array ('<entityfieldname>');	var $popup_fields = Array ('<entityfieldname>');
//
//	// For Alphabetical search	// For Alphabetical search
//	var $def_basicsearch_col = '<entityfieldname>';	var $def_basicsearch_col = '<entityfieldname>';
//
//	// Column value to use on detail view record text display	// Column value to use on detail view record text display
//	var $def_detailview_recname = '<entityfieldname>';	var $def_detailview_recname = '<entityfieldname>';
//
//	// Used when enabling/disabling the mandatory fields for the module.	// Used when enabling/disabling the mandatory fields for the module.
//	// Refers to vtiger_field.fieldname values.	// Refers to vtiger_field.fieldname values.
//	var $mandatory_fields = Array('<entityfieldname>','assigned_user_id');	var $mandatory_fields = Array('<entityfieldname>','assigned_user_id');
//
//	var $default_order_by = '<entityfieldname>';	var $default_order_by = '<entityfieldname>';
//	var $default_sort_order='ASC';	var $default_sort_order='ASC';

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
