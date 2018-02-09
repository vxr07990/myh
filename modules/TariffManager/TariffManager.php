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

class TariffManager extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_tariffmanager';
    public $table_index= 'tariffmanagerid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_tariffmanagercf', 'tariffmanagerid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_tariffmanager', 'vtiger_tariffmanagercf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_tariffmanager' => 'tariffmanagerid',
        'vtiger_tariffmanagercf'=>'tariffmanagerid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Name' => array('tariffmanager', 'tariffmanagername'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Name' => 'tariffmanagername',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'tariffmanagername';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Name' => array('tariffmanager', 'tariffmanagername'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Name' => 'tariffmanagername',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('tariffmanagername');

    // For Alphabetical search
    public $def_basicsearch_col = 'tariffmanagername';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'tariffmanagername';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('tariffmanagername','assigned_user_id');

    public $default_order_by = 'tariffmanagername';
    public $default_sort_order='ASC';
    
    public function save_module()
    {
        //custom save for module
    }
    
    public function saveentity($module, $fileid = '')
    {
        $db = PearDatabase::getInstance();
        parent::saveentity($module, $fileid);
        $columns = array_merge($this->column_fields, $_REQUEST);
        /* if(empty($columns['record'])){
            //new records will have an empty record but currentid gets set correctly in parent
            if(!empty($columns['currentid'])) {
                $columns[ 'record' ] = $columns[ 'currentid' ];
            } else {
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE setype = ? AND createdtime = ? AND modifiedtime = ? AND label = ?";
                $result = $db->pquery($sql, ['TariffManager', $this->column_fields['createdtime'], $this->column_fields['modifiedtime'], $this->column_fields['tariffmanagername']]);
                $row = $result->fetchRow();
                $columns['record'] = $row[0];
            }
        } */
        //$columns['record'] = $_REQUEST['currentid'];
        $recordId = $_REQUEST['currentid'];
        if (!$recordId) {
            $recordId = $columns['record'];
            if (!$recordId) {
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE setype = ? AND createdtime = ? AND modifiedtime = ? AND label = ?";
                $result = $db->pquery($sql, ['TariffManager', $this->column_fields['createdtime'], $this->column_fields['modifiedtime'], $this->column_fields['tariffmanagername']]);
                $row = $result->fetchRow();
                $recordId = $row[0];
            }
        }
        $columns['record'] = $recordId;
        //file_put_contents('logs/devLog.log', "\n COLUMNS: ".print_r($columns, true), FILE_APPEND);
        foreach ($columns as $fieldName=>$value) {
            file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$fieldName.' - '.$value."\n", FILE_APPEND);
            if (substr($fieldName, 0, 7) == 'Vanline') {
                $vanlineId = strstr(substr($fieldName, 7), 'State', true);
                $applyToAllAgents = $columns['assignVanline'.$vanlineId.'Agents'] == 'on' ? 1 : 0;
                
                $sql = "SELECT vanlineid, tariffid FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND tariffid=?";
                $result = $db->pquery($sql, array($vanlineId, $recordId));
                $row = $result->fetchRow();
                
                $params = array();
                
                if ($row != null && $value == 'unassigned') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND tariffid=?";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } elseif ($row == null && $value == 'assigned') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_tariff2vanline` (vanlineid, tariffid, apply_to_all_agents) VALUES (?,?,?)";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                    $params[] = $applyToAllAgents;
                } elseif ($row != null) {
                    //Assignment exists and should - update apply_to_all_agents column
                    $sql = "UPDATE `vtiger_tariff2vanline` SET apply_to_all_agents=? WHERE vanlineid=? AND tariffid=?";
                    $params[] = $applyToAllAgents;
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } else {
                    //Assignment is already in correct state
                    $sql = null;
                }
                
                if (isset($sql)) {
                    file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$sql."\n".print_r($params, true)."\n", FILE_APPEND);
                    $result = $db->pquery($sql, $params);
                }
            } elseif (substr($fieldName, 0, 11) == 'assignAgent') {
                preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').print_r($m, true)."\n", FILE_APPEND);
                $agentId = substr($fieldName, $m[0][1]);
                
                $sql = "SELECT agentid, tariffid FROM `vtiger_tariff2agent` WHERE agentid=? AND tariffid=?";
                $result = $db->pquery($sql, array($agentId, $recordId));
                $row = $result->fetchRow();
                
                if ($row != null && $value == '0') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_tariff2agent` WHERE agentid=? AND tariffid=?";
                } elseif ($row == null && $value == 'on') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_tariff2agent` (agentid, tariffid) VALUES (?,?)";
                } else {
                    //Assignment is already in correct state
                    $sql = null;
                }
                
                //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$sql."\n", FILE_APPEND);

                if (isset($sql)) {
                    $result = $db->pquery($sql, array($agentId, $recordId));
                }
            }
        }
        if ($_REQUEST['valuation_name']) {
            $this->saveValuationSettings($_REQUEST);
        }
    }

    public function saveValuationSettings($request)
    {
        $db = PearDatabase::getInstance();

        // If record id is empty need to get the id to save the valuation settings
        if (empty($request['record'])) {
            $sql = 'SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE tariffmanagername = ? LIMIT 1';
            $result = $db->pquery($sql, [$request['tariffmanagername']]);
            while ($row = $result->fetchRow()) {
                $request['record'] = $row['tariffmanagerid'];
            }
        }

        // Get the current valuation types for tariff to see if we need to set any to delete
        $sql = 'SELECT id FROM `vtiger_valuation_tariff_types` WHERE related_id = ?';
        $result = $db->pquery($sql, [$request['record']]);
        $currentRecords = array();
        while ($result != null && $row = $result->fetchRow()) {
            $currentRecords[] = $row['id'];
        }
        for ($i=0;$i<count($request['valuation_name']);$i++) {
            if (!empty($request['record']) && !empty($request['valuation_name'][$i])) {

                // Free is an enum type which accepts y or n
                $request['free'][$i] == 'y'?'y':'n';

                if ($request['id'][$i]) {
                    $submittedIds[] = $request['id'][$i];
                    $sql = 'UPDATE `vtiger_valuation_tariff_types` SET valuation_name = ?, per_pound = ?, max_amount = ?, additional_price_per = ?, free = ?, additional_price_per_sit = ?, free_amount = ? WHERE id = ? AND related_id = ?';
                    $params = array(
                        $request['valuation_name'][$i],
                        $request['per_pound'][$i],
                        $request['max_amount'][$i],
                        $request['additional_price_per'][$i],
                        $request['free'][$i],
                        $request['additional_price_per_sit'][$i],
                        $request['free_amount'][$i],
                        $request['id'][$i],
                        $request['record'],
                    );
                    $db->pquery($sql, $params);
                } else {
                    $sql = 'INSERT INTO `vtiger_valuation_tariff_types` (related_id, valuation_name, per_pound, max_amount, additional_price_per, additional_price_per_sit, free, free_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                    $params = array(
                        $request['record'],
                        $request['valuation_name'][$i],
                        $request['per_pound'][$i],
                        $request['max_amount'][$i],
                        $request['additional_price_per'][$i],
                        $request['additional_price_per_sit'][$i],
                        $request['free'][$i],
                        $request['free_amount'][$i],
                    );
                    $db->pquery($sql, $params);
                }
            }
        }

        if ($currentRecords) {
            foreach ($currentRecords as $id) {
                if (!in_array($id, $submittedIds)) {
                    $sql = 'UPDATE `vtiger_valuation_tariff_types` SET active = ? WHERE id = ? LIMIT 1';
                    $db->pquery($sql, ['n', $id]);
                }
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
