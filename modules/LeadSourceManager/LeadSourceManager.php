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

class LeadSourceManager extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_leadsourcemanager';
    public $table_index= 'leadsourcemanagerid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_leadsourcemanagercf', 'leadsourcemanagerid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_leadsourcemanager', 'vtiger_leadsourcemanagercf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_leadsourcemanager' => 'leadsourcemanagerid',
        'vtiger_leadsourcemanagercf'=>'leadsourcemanagerid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'source_name' => array('leadsourcemanager', 'source_name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Lead Source' => 'source_name',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'source_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Lead Source' => array('leadsourcemanager', 'source_name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Lead Source' => 'source_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('source_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'source_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'source_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('source_name','assigned_user_id');

    public $default_order_by = 'source_name';
    public $default_sort_order='ASC';

    public function save_module()
    {
        //custom save
    }

    public function saveentity($module, $fileid = '')
    {
        //override the request values for agentid, brand, agency_code
        $fieldList = array_merge($this->column_fields, $_REQUEST);
        $sourceRecord = $fieldList['sourceRecord'];
        $sourceModule = $fieldList['sourceModule'];

        //if there is a source record pull that agentid.
        if (empty($record) && $sourceModule && $sourceRecord) {
            $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);

            if ($sourceModule == 'AgentManager') {
                $srcAgent = $sourceRecordModel->getId();
            } else {
                $srcAgent = $sourceRecordModel->get('agentid');
            }
            $fieldList['agentid'] = $srcAgent;
        }

        //get agency info from the agent record using the agent id.
        if ($fieldList['agentid'] != 'turtles') {
            $agentRecordModel = Vtiger_Record_Model::getInstanceById($fieldList['agentid']);
            $fieldList['agency_related'] = $fieldList['agentid'];
            $fieldList['vanline_related'] = $fieldList['vanlinemanager_id'] = $agentRecordModel->get('vanline_id');
            $fieldList['brand'] = $agentRecordModel->getBrand();
            $fieldList['agency_code'] = $agentRecordModel->get('agency_code');
        } else {
            //so if no agentid then..
            //because there's no source and no agentid sent in.
            //we are going to assume this is vanline assigned
            //@TODO So I am thinking maybe this is a stupid assumption, but let's revisit later.
            $fieldList['agentid'] = $fieldList['vanlinemanager_id'];
        }

        //enforce that these aren't passed in.
        $this->column_fields['agentid'] = $fieldList['agentid'];
        $this->column_fields['agency_code'] = $fieldList['agency_code'];
        $this->column_fields['vanlinemanager_id'] = $fieldList['vanlinemanager_id'];
        $this->column_fields['agency_related'] = $fieldList['agency_related'];
        $this->column_fields['vanline_related'] = $fieldList['vanline_related'];
        $this->column_fields['brand'] = $fieldList['brand'];

        //So parent::saveentity pulls the fields based on presence(0,2) and displaytype (1,3,4).
        //I tried changing the display type to all three of those and it doesn't do what I want.
        //which is hide on edit and show on detail.
        parent::saveentity($module, $fileid);

        //so blah bypass doing it that way and just update the values after the insert.

        //this should be set in crmentity
        $recordId = $this->id;

        //bring in the database object.
        $db = PearDatabase::getInstance();

        if (!$recordId) {
            //but if not attempt to find it.
            $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE setype = ? AND createdtime = ? AND modifiedtime = ? AND label = ?";
            $result = $db->pquery($sql, ['LeadSourceManager', $this->column_fields['createdtime'], $this->column_fields['modifiedtime'], $this->column_fields['source_name']]);
            $row = $result->fetchRow();
            $recordId = $row[0];
        }

        //IFF we have the id, update the data with the vanlinemanager_id and agency_code.
        if ($recordId) {
            //@todo? should it select first to verify existence?
            $stmt = 'UPDATE `vtiger_leadsourcemanager` SET
					`vanlinemanager_id` = ?
					, `agency_code` = ?
					WHERE `leadsourcemanagerid` = ?
					LIMIT 1';
            $db->pquery($stmt, [
                $fieldList['vanlinemanager_id'],
                $fieldList['agency_code'],
                $recordId
            ]);
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
