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

class AddressList extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_addresslist';
    public $table_index= 'addresslistid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_addresslistcf', 'addresslistid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_addresslist', 'vtiger_addresslistcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_addresslist' => 'addresslistid',
        'vtiger_addresslistcf'=>'addresslistid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_NAME' => array('vtiger_commissionplans', 'name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Address Type' => 'address_type',
        'Location Type' => 'location_type',
    );

    // Make the field link to detail view
    public $list_link_field = 'address_type';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Address Type' => array('vtiger_addresslist', 'address_type'),
        'Location Type' => array('vtiger_addresslist','location_type'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Address Type' => 'address_type',
        'Location Type' => 'location_type',
    );

    // For Popup window record selection
    public $popup_fields = array('address_type');

    // For Alphabetical search
    public $def_basicsearch_col = 'address_type';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'address_type';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('address_type','location_type');

    public $default_order_by = 'address_type';
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
    public function transferAddresses($sourceRecordId, $destinationRecordId){
        $desRecordModel = Vtiger_Record_Model::getInstanceById($destinationRecordId);
        if($desRecordModel->getModuleName() == 'Opportunities'){
            global $adb;
            $stmt = "SELECT vtiger_addresslist.* FROM `vtiger_addresslist` "
                . " INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = `vtiger_addresslist`.addresslistid"
                . " INNER JOIN `vtiger_addresslistrel` ON `vtiger_addresslistrel`.addresslistid = `vtiger_addresslist`.addresslistid"
                . " WHERE "
                . " vtiger_addresslistrel.crmid = ? "
                . " AND deleted = 0 "
                . " ORDER BY vtiger_addresslistrel.sequence ASC ";
            $params = [$sourceRecordId];
            $rsListSourceAddress = $adb->pquery($stmt,$params);
            $seq = 0;
            while ($row = $adb->fetchByAssoc($rsListSourceAddress)){
                $seq ++;
                $recordModel=Vtiger_Record_Model::getCleanInstance("AddressList");
                $recordModel->set('mode', '');
                $fieldModelList = $recordModel->getModule()->getFields();
                foreach ($fieldModelList as $fieldName => $fieldModel) {
                    $fieldValue = $row[$fieldName];
                    $fieldDataType = $fieldModel->getFieldDataType();
                    if ($fieldDataType == 'time') {
                        $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                    }
                    if ($fieldValue) {
                        if (!is_array($fieldValue)) {
                            $fieldValue = trim($fieldValue);
                        }
                        $recordModel->set($fieldName, $fieldValue);
                    }
                }
                $recordModel->save();
                //save related record
                $adb->pquery("INSERT INTO vtiger_addresslistrel(`addresslistid`,`crmid`,`sequence`) VALUES (?,?,?)",[$recordModel->getId(),$destinationRecordId,$seq]);
            }
        }
    }
}
