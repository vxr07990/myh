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

class Claims extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_claims';
    public $table_index= 'claimsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_claimscf', 'claimsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_claims', 'vtiger_claimscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_claims' => 'claimsid',
        'vtiger_claimscf'=>'claimsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Claim Number' => array('claims', 'claims_number'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Claim Number' => 'claims_number',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'claims_number';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Claim Number' => array('claims', 'claims_number'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Claim Number' => 'claims_number',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('claims_number');

    // For Alphabetical search
    public $def_basicsearch_col = 'claims_number';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'claims_number';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('claims_number','assigned_user_id');

    public $default_order_by = 'claims_number';
    public $default_sort_order='ASC';

    public function save_module()
    {
        $request = new Vtiger_Request($_REQUEST);
        $itemsCount = $request->get('numStatus');
            
        for ($i = 1; $i < $itemsCount+1; $i++) {
            //                $statusItems[$i]['statusId'] = $request->get('statusId-' . $i);
                $statusItems[$i]['status'] = $request->get('status-' . $i);
            $statusItems[$i]['reason'] = $request->get('reason-' . $i);
            $statusItems[$i]['effective_date'] = $request->get('effective_date-' . $i);
        }
            
        if (is_array($statusItems) && count($statusItems) > 0) {
            $record = $request->get('record');
            $db = PearDatabase::getInstance();
            foreach ($statusItems as $item) {
                $date = strtotime(implode('/', explode('-', $item['effective_date'])));
                $newformat = date('Y-m-d', $date);
                $db->pquery('INSERT INTO vtiger_claims_statusgrid (recordid, status, reason, effective_date) VALUES (?,?,?,?)', array($record, $item['status'], $item['reason'], $newformat));
            }
        }
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
