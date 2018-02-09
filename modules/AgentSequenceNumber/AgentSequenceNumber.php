<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/24/2017
 * Time: 11:11 AM
 */

include_once 'modules/Vtiger/CRMEntity.php';

class AgentSequenceNumber extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_agentsequencenumber';
    public $table_index= 'agentsequencenumberid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_agentsequencenumbercf', 'agentsequencenumberid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_agentsequencenumber', 'vtiger_agentsequencenumbercf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_agentsequencenumber' => 'agentsequencenumberid',
        'vtiger_agentsequencenumbercf'=>'agentsequencenumberid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Agent' => array('vtiger_agentsequencenumber', 'agent_sn_agentmanagerid'),
        'Module' => array('vtiger_agentsequencenumber', 'agent_sn_modulename'),
        'Format' => array('vtiger_agentsequencenumber', 'agent_sn_format'),
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Agent' => 'agent_sn_agentmanagerid',
        'Module' => 'agent_sn_modulename',
        'Format' => 'agent_sn_format',
    );

    // Make the field link to detail view
    public $list_link_field = 'agent_sn_modulename';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Agent' => array('vtiger_agentsequencenumber', 'agent_sn_agentmanagerid'),
        'Module' => array('vtiger_agentsequencenumber', 'agent_sn_modulename'),
        'Format' => array('vtiger_agentsequencenumber', 'agent_sn_format'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Agent' => 'agent_sn_agentmanagerid',
        'Module' => 'agent_sn_modulename',
        'Format' => 'agent_sn_format',
    );

    // For Popup window record selection
    public $popup_fields = array('agent_sn_agentmanagerid', 'agent_sn_modulename');

    // For Alphabetical search
    public $def_basicsearch_col = 'agent_sn_agentmanagerid';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'agent_sn_format';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('agent_sn_agentmanagerid','agent_sn_modulename','agent_sn_format');

    public $default_order_by = 'agent_sn_agentmanagerid';
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
