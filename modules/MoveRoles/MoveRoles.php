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

class MoveRoles extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_moveroles';
    public $table_index= 'moverolesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_moverolescf', 'moverolesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_moveroles', 'vtiger_moverolescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_moveroles' => 'moverolesid',
        'vtiger_moverolescf'=>'moverolesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Role' => array('moveroles', 'moveroles_role'),
        'Employees' => array('moveroles', 'moveroles_employees')
        //'Accident Time' => Array('accidents', 'accidents_time')
        //'Assigned To' => Array('accidents','smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Role' => 'moveroles_role',
        'Employees' => 'moveroles_employees',
        //'Accident Time' => 'accidents_time',
        //'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'moveroles_role';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Role' => array('moveroles', 'moveroles_role'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id')
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Role' => 'moveroles_role',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('moveroles_role');

    // For Alphabetical search
    public $def_basicsearch_col = 'moveroles_role';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'moveroles_role';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('moveroles_role','assigned_user_id');

    public $default_order_by = 'moveroles_role';
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

    public function save_module()
    {
        //this needs to exist for saveentity to work correctly
    }

    public function saveentity($module, $fileid = '')
    {
        //custom save
        //NOTE TO SELF: the no repeat code breaks things that have multi-module saves
        parent::saveentity($module, $fileid);
        $db        = PearDatabase::getInstance();
        $fieldList = array_merge($_REQUEST, $this->column_fields);
        //save coordinating agent for customer service coordinator roles
        if (getenv('INSTANCE_NAME') == 'graebel' && $fieldList['moveroles_role'] == 'Customer Service Coordinator') {
            //if moverole is csc, create coordinating agent
            $agentsId = $db->pquery("SELECT agentsid FROM `vtiger_agents` WHERE agentmanager_id = ?", [$fieldList['agentid']])->fetchRow()['agentsid'];
            if ($fieldList['moveroles_employees']) {
                //employee set, source agent from employee
                //get agentid from db
                $employeeAgent = $db->pquery("SELECT `vtiger_crmentity`.agentid FROM `vtiger_crmentity`
											  INNER JOIN `vtiger_employees` ON `vtiger_employees`.employeesid = `vtiger_crmentity`.crmid
											  WHERE `vtiger_employees`.employeesid = ?", [$fieldList['moveroles_employees']])->fetchRow()['agentid'];
                //grab and set agent
                $agentsId = $db->pquery("SELECT agentsid FROM `vtiger_agents` WHERE agentmanager_id = ?", [$employeeAgent])
                                ->fetchRow()['agentsid'] ?: $agentsId;
            }
            //Don't set fieldlist for participants save
            $cAgent['agent_type_1'] = 'Coordinating Agent';
            $cAgent['agent_permission_1'] = 'full';
            $cAgent['agents_id_1'] = $agentsId;
            $cAgent['numAgents'] = 1;
            $cAgent['participantId_1'] = 'none';
            $result = $db->pquery("SELECT * FROM `vtiger_participatingagents` WHERE agent_type = ? AND rel_crmid = ? AND deleted=0", ['Coordinating Agent', $fieldList['moveroles_orders']]);
            $row = $result->fetchRow();
            if ($row) {
                //row exists, update
                $cAgent['participantId_1'] = $row['participatingagentsid'];
                $cAgent['agent_permission_1'] = $row['view_level'];
                $cAgent['agents_id_1'] = $agentsId;
                $cAgent['numAgents'] = 1;
            }
            //participants save
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                $participatingAgentsModel::saveParticipants($cAgent, $fieldList['moveroles_orders']);
            }
        } elseif (getenv('IGC_MOVEHQ') && $fieldList['moveroles_role'] == 'Customer Service Coordinator') {
            file_put_contents('logs/devLog.log', "\n hits this", FILE_APPEND);
            //if moverole is csc, create coordinating agent
            $agentsId = $db->pquery("SELECT agentsid FROM `vtiger_agents` WHERE agentmanager_id = ?", [$fieldList['agentid']])->fetchRow()['agentsid'];
            if ($fieldList['moveroles_employees']) {
                //employee set, source agent from employee
                //get agentid from db
                $employeeAgent = $db->pquery("SELECT `vtiger_crmentity`.agentid FROM `vtiger_crmentity`
											  INNER JOIN `vtiger_employees` ON `vtiger_employees`.employeesid = `vtiger_crmentity`.crmid
											  WHERE `vtiger_employees`.employeesid = ?", [$fieldList['moveroles_employees']])->fetchRow()['agentid'];
                //grab and set agent
                $agentsId = $db->pquery("SELECT agentsid FROM `vtiger_agents` WHERE agentmanager_id = ?", [$employeeAgent])->fetchRow()['agentsid'];
            }
            //set fieldlist for participants save
            $fieldList['agent_type_1'] = 'Coordinating Agent';
            $fieldList['agent_permission_1'] = 'full';
            $fieldList['agents_id_1'] = $agentsId;
            $fieldList['numAgents'] = 1;
            $fieldList['participantId_1'] = 'none';
            $result = $db->pquery("SELECT * FROM `vtiger_participatingagents` WHERE agent_type = ? AND rel_crmid = ? AND deleted=0", ['Coordinating Agent', $fieldList['moveroles_orders']]);
            $row = $result->fetchRow();
            if ($row) {
                //row exists, update
                $fieldList['participantId_1'] = $row['participatingagentsid'];
                $fieldList['agent_permission_1'] = $row['view_level'];
                ;
                $fieldList['agents_id_1'] = $agentsId;
                $fieldList['numAgents'] = 1;
            }
            //participants save
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                $fieldList['module'] = 'Orders';
                $fieldList['record'] = $fieldList['moveroles_orders'];
                $participatingAgentsModel::saveParticipants($fieldList, $fieldList['record']);
            }
        }
    }
}
