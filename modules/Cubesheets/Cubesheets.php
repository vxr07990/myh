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

class Cubesheets extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_cubesheets';
    public $table_index= 'cubesheetsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_cubesheetscf', 'cubesheetsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_cubesheets', 'vtiger_cubesheetscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_cubesheets' => 'cubesheetsid',
        'vtiger_cubesheetscf'=>'cubesheetsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Survey Name' => array('cubesheets', 'cubesheet_name'),
        'Contact' => array('cubesheets', 'contact_id'),
        'Opportunity' => array('cubesheets', 'potential_id'),
        'Surveyor' => array('crmentity', 'smownerid'),
        'Primary Survey' => array('cubesheets', 'is_primary')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Survey Name' => 'cubesheet_name',
        'Contact' => 'contact_id',
        'Opportunity' => 'potential_id',
        'Surveyor' => 'assigned_user_id',
        'Primary Survey' => 'is_primary',
    );

    // Make the field link to detail view
    public $list_link_field = 'cubesheet_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Survey Name' => array('cubesheets', 'cubesheet_name'),
        'Surveyor' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Survey Name' => 'cubesheet_name',
        'Surveyor' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('cubesheet_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'cubesheet_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'cubesheet_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('cubesheet_name','assigned_user_id');

    public $default_order_by = 'cubesheet_name';
    public $default_sort_order='ASC';

    public function saveentity($module, $fileid = '') {
        $fieldList = array_merge($_REQUEST, $this->column_fields);
        $fieldTariff = $fieldList['effective_tariff']? :$fieldList['local_tariff'];
        if (!$fieldTariff) {
            $data = Estimates_Record_Model::getAllowedTariffsForUser($fieldList['agentid']);
            // Pretty sure there is no business line, so we assume interstate
            if (true || $fieldList['business_line_est'] == "Interstate Move") {
                foreach ($data as $id => $info) {
                    if ($info['is_managed_tariff']) {
                        $fieldTariff = $fieldList['effective_tariff'] = $this->column_fields['effective_tariff'] = $_REQUEST['effective_tariff'] = $id;
                        break;
                    }
                }
            } else {
                foreach ($data as $id => $info) {
                    if (!$info['is_managed_tariff']) {
                        $fieldTariff = $fieldList['effective_tariff'] = $this->column_fields['effective_tariff'] = $_REQUEST['effective_tariff'] = $id;
                        break;
                    }
                }
            }
        }
        if(!$fieldList['effective_date'])
        {
            $fieldList['effective_date'] = $this->column_fields['effective_date'] = $_REQUEST['effective_date'] = date('Y-m-d');
        }
        parent::saveentity($module, $fileid);
    }

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
