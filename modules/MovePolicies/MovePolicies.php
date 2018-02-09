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

class MovePolicies extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_movepolicies';
    public $table_index = 'movepoliciesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_movepoliciescf', 'movepoliciesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_movepolicies', 'vtiger_movepoliciescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_movepolicies' => 'movepoliciesid',
        'vtiger_movepoliciescf' => 'movepoliciesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_MOVE_POLICY_ID' => array('movepolicies', 'policies_id'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_MOVE_POLICY_ID' => 'policies_id',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = 'policies_id';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_MOVE_POLICY_ID' => array('movepolicies', 'policies_id'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_MOVE_POLICY_ID' => 'policies_id',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('policies_id');
    // For Alphabetical search
    public $def_basicsearch_col = 'policies_id';
    // Column value to use on detail view record text display
    public $def_detailview_recname = 'policies_id';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('policies_id', 'assigned_user_id');
    public $default_order_by = 'policies_id';
    public $default_sort_order = 'ASC';

    public function save_module()
    {
        //custom save for module
//        if ($this->column_fields['policies_tariffid'] != '') {
//            $tariffRecordModel = Vtiger_Record_Model::getInstanceById($this->column_fields['policies_tariffid'], 'TariffManager');
//            $tariffItems = $tariffRecordModel->getAllowedTariffItems();
//        }
//

        $request = new Vtiger_Request($_REQUEST);
        $itemsCount = $request->get('items_count');
        //for modtracker
        $modtrackerId = null;
        
        for ($i = 1; $i < $itemsCount+1; $i++) {
            $tariffItems[$i]['RatingItemID'] = $request->get('tariff_item_id_' . $i);
            $tariffItems[$i]['db_id'] = $request->get('tariff_item_dbid_' . $i);
            $tariffItems[$i]['TariffID'] = $request->get('tariff_id_' . $i);
            $tariffItems[$i]['ItemCode'] = $request->get('tariff_code_' . $i);
            $tariffItems[$i]['Description'] = $request->get('tariff_des_' . $i);
            $tariffItems[$i]['SectionID'] = $request->get('tariff_section_' . $i);
            $tariffItems[$i]['items_auth'] = $request->get('items_auth_' . $i);
            $tariffItems[$i]['items_authlimit'] = $request->get('items_authlimit_' . $i);
            $tariffItems[$i]['items_remarks'] = $request->get('items_remarks_' . $i);
        }
        
        if (is_array($tariffItems) && count($tariffItems) > 0) {
            $modtrackerId = $this->saveTariffItems($tariffItems, $request);
        }
        
        $miscItemsCount = $request->get('numMiscItems');
        for ($i = 1; $i < $miscItemsCount+1; $i++) {
            $miscTariffItems[$i]['RatingItemID'] = '';
            $miscTariffItems[$i]['db_id'] = $request->get('miscItemDbId-' . $i);
            ;
            $miscTariffItems[$i]['TariffID'] = 99999999;
            $miscTariffItems[$i]['ItemCode'] = 'misc';
            $miscTariffItems[$i]['Description'] = $request->get('miscItemDescription-' . $i);
            $miscTariffItems[$i]['SectionID'] = 99999999;
            $miscTariffItems[$i]['items_auth'] = $request->get('miscItemAuth-' . $i);
            $miscTariffItems[$i]['items_authlimit'] = $request->get('miscItemAuthLimit-' . $i);
            $miscTariffItems[$i]['items_remarks'] = $request->get('misItemRemarks-' . $i);
        }
        
        if (is_array($miscTariffItems) && count($miscTariffItems) > 0) {
            $this->saveMiscTariffItems($miscTariffItems, $request, $modtrackerId);
        }
    }

    public function saveTariffItems($tariffItems, Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $policyId = $this->id;
        $tariffCRMId = $this->column_fields['policies_tariffid'];
        $mode = $this->mode;
        
        //for modtracker
        $headerRow = false;
        //Need to add the new ones and update the existing ones
        $itemsIdsArray = array();
        
        foreach ($tariffItems as $index=>$tariffItem) {
            $itemsIdsArray[] = $tariffItem['RatingItemID'];
            if ($mode == 'edit') {
                $result = $db->pquery('SELECT id, item_auth, item_auth_limits, item_remarks FROM vtiger_movepolicies_items WHERE id=?', array($tariffItem['db_id']));
            } else {
                $result = false;
            }

            if ($result && $db->num_rows($result) > 0) {
                //Update existing
                $itemsId = $db->query_result($result, 0, 'id');
                $db->pquery('UPDATE vtiger_movepolicies_items SET tariff_section=?, item_auth =?, item_auth_limits=?, item_remarks = ? WHERE id =?', array($tariffItem['SectionID'], $tariffItem['items_auth'], $tariffItem['items_authlimit'], $tariffItem['items_remarks'], $itemsId));
            } else {
                //add New items
                $db->pquery('INSERT INTO vtiger_movepolicies_items (policies_id, tariff_crmid, tariff_id,tariff_section, item_id, item_des,item_code, item_auth, item_auth_limits, item_remarks) VALUES (?,?,?,?,?,?,?,?,?,?)', array($policyId, $tariffCRMId, $tariffItem['TariffID'], $tariffItem['SectionID'], $tariffItem['RatingItemID'], $tariffItem['Description'], $tariffItem['ItemCode'], $tariffItem['items_auth'], $tariffItem['items_authlimit'], $tariffItem['items_remarks']));
            }

            //modtracker function for tariff items
            if ($result && $db->num_rows($result) > 0) {
                $modtrackerId = $this->insertModtrackerDetail($headerRow, $request, ModTracker_Record_Model::UPDATE, $db, $result, $index, $tariffItem, $modtrackerId, '');
                if ($modtrackerId != null) {
                    $headerRow = true;
                }
            } else {
                $modtrackerId = $this->insertModtrackerDetail($headerRow, $request, ModTracker_Record_Model::CREATE, $db, $result, $index, $tariffItem, $modtrackerId, '');
                if ($modtrackerId != null) {
                    $headerRow = true;
                }
            }
        }

        
        //If change the tariff Id remove the old items
        $db->pquery('DELETE FROM vtiger_movepolicies_items WHERE policies_id=? AND tariff_crmid !=? AND tariff_crmid != 0', array($policyId, $tariffCRMId));
        $db->pquery('DELETE FROM vtiger_movepolicies_items WHERE policies_id=? AND tariff_id !=? AND tariff_id != 99999999', array($policyId, $tariffItem['TariffID']));
        
        return $modtrackerId;
    }
    
    public function saveMiscTariffItems($miscTariffItems, Vtiger_Request $request, $modtrackerId)
    {
        $db = PearDatabase::getInstance();
        $policyId = $this->id;
        $tariffCRMId = 0;
        $mode = $this->mode;
        //for modtracker
        if ($modtrackerId != null) {
            $headerRow = true;
        } else {
            $headerRow = false;
        }
        //Need to add the new ones and update the existing ones
        $itemsIdsArray = array();
        foreach ($miscTariffItems as $index=>$tariffItem) {
            $itemsIdsArray[] = $tariffItem['RatingItemID'];
            if ($mode == 'edit') {
                $result = $db->pquery('SELECT id, item_auth, item_auth_limits, item_remarks FROM vtiger_movepolicies_items WHERE id=?', array($tariffItem['db_id']));
            } else {
                $result = false;
            }

            if ($result && $db->num_rows($result) > 0) {
                //Update existing
                $itemsId = $db->query_result($result, 0, 'id');
                $db->pquery('UPDATE vtiger_movepolicies_items SET tariff_section=?, item_auth =?, item_auth_limits=?, item_remarks = ? WHERE id =?', array($tariffItem['SectionID'], $tariffItem['items_auth'], $tariffItem['items_authlimit'], $tariffItem['items_remarks'], $itemsId));
            } else {
                //add New items
                $db->pquery('INSERT INTO vtiger_movepolicies_items (policies_id, tariff_crmid, tariff_id,tariff_section, item_id, item_des,item_code, item_auth, item_auth_limits, item_remarks) VALUES (?,?,?,?,?,?,?,?,?,?)', array($policyId, $tariffCRMId, $tariffItem['TariffID'], $tariffItem['SectionID'], $tariffItem['RatingItemID'], $tariffItem['Description'], $tariffItem['ItemCode'], $tariffItem['items_auth'], $tariffItem['items_authlimit'], $tariffItem['items_remarks']));
            }
            
            //modtracker function for misc Tariff items
            if ($result && $db->num_rows($result) > 0) {
                $modtrackerId = $this->insertModtrackerDetail($headerRow, $request, ModTracker_Record_Model::UPDATE, $db, $result, $index, $tariffItem, $modtrackerId, '_misc');
                if ($modtrackerId != null) {
                    $headerRow = true;
                }
            } else {
                $modtrackerId = $this->insertModtrackerDetail($headerRow, $request, ModTracker_Record_Model::CREATE, $db, $result, $index, $tariffItem, $modtrackerId, '_misc');
                if ($modtrackerId != null) {
                    $headerRow = true;
                }
            }
        }
    }
    
    public function insertModtrackerHeader($db, Vtiger_Request $request, $status)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userID = $currentUser->get('id');
        
        $recordId = $request->get('record');
        $moduleName = $request->get('module');
        
        $modtrackerId = $db->getUniqueID('vtiger_modtracker_basic');
        $dateTime = date("Y-m-d H:i:s");
        $db->pquery('INSERT INTO vtiger_modtracker_basic (id, crmid, module, whodid, changedon, status) VALUES (?,?,?,?,?,?)', array($modtrackerId, $recordId, $moduleName, $userID, $dateTime, $status));
        
        return $modtrackerId;
    }
    
    public function insertModtrackerDetail($headerRow, $request, $status, $db, $result, $index, $tariffItem, $modtrackerId, $string)
    {
        //first compare the fields and save into fieldsToInsert array the ones that changed, if any, insert the header in table _basic and the details en table _details
        $fieldsToInsert = array();
        $fieldsBDToCompare = array('item_auth', 'item_auth_limits', 'item_remarks');
        $fieldsToCompare = array('items_auth', 'items_authlimit', 'items_remarks');
        if ($result && $db->num_rows($result) > 0) {
            $row = $db->query_result_rowdata($result, 0);
            for ($i=0;$i<count($fieldsToCompare);$i++) {
                if ($row[$fieldsBDToCompare[$i]] != $tariffItem[$fieldsToCompare[$i]]) {
                    //i had to compare with 2 arrays cause the names are different
                    $item['label'] = $fieldsToCompare[$i].'_'.$index.$string;
                    $item['oldValue'] = ($row[$fieldsBDToCompare[$i]]==''?  \NULL :$row[$fieldsBDToCompare[$i]]);
                    $item['newValue'] = ($tariffItem[$fieldsToCompare[$i]]==''?  \NULL :$tariffItem[$fieldsToCompare[$i]]);
                    $fieldsToInsert[] = $item;
                }
            }
            if (count($fieldsToInsert)>0) {
                if (!$headerRow) {
                    $modtrackerId = $this->insertModtrackerHeader($db, $request, $status);
                }
                for ($i=0;$i<count($fieldsToInsert);$i++) {
                    $db->pquery('INSERT INTO vtiger_modtracker_detail (id, fieldname, prevalue, postvalue) VALUES (?,?,?,?)', array($modtrackerId, $fieldsToInsert[$i]['label'], $fieldsToInsert[$i]['oldValue'], $fieldsToInsert[$i]['newValue']));
                }
            }
        }
        return $modtrackerId;
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
