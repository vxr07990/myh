<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contracts_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request)
    {
        $searchValue = $request->get('search_value');
        $searchModule = $request->get('search_module');

        $parentRecordId = $request->get('parent_id');
        $parentModuleName = $request->get('parent_module');
        $relatedModule = $request->get('module');
        $assignedTo = $request->get('assignedTo');
        
        $searchModuleModel = Vtiger_Module_Model::getInstance($searchModule);
        $records = $searchModuleModel->searchRecord($searchValue, $parentRecordId, $parentModuleName, $relatedModule);
        
        //limit the search results if it's searching TariffManager to only show appropriate Tariffs
        if ($searchModule == 'TariffManager') {
            $assignedResults = $this->getCurrentUserTariffs(false, array($this->getAssignedAgent($assignedTo)));
            $newRecords = array('TariffManager'=>array());
            foreach ($records['TariffManager'] as $key=>$value) {
                if (in_array($key, $assignedResults)) {
                    $newRecords['TariffManager'][$key] = $value;
                }
            }
            $records = $newRecords;
        }
        
        $result = array();
        if (is_array($records)) {
            foreach ($records as $moduleName=>$recordModels) {
                foreach ($recordModels as $recordModel) {
                    $result[] = array('label'=>decode_html($recordModel->getName()), 'value'=>decode_html($recordModel->getName()), 'id'=>$recordModel->getId());
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    /**
     *     Gets the agent or vanline ID for the provided assignedTo
     *     @param integer assignedTo The CRMID that will be used for the lookup
     *     @return integer The agent or vanline ID for the provided assignedTo, or if user returns the same value
     */
    public function getAssignedAgent($assignedTo)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT groupid, groupname FROM `vtiger_groups`";
        $result = $db->pquery($sql, array());
        $groups = array();
        while ($row =& $result->fetchRow()) {
            $groups[$row[0]] = $row[1];
        }
        if (array_key_exists($assignedTo, $groups)) {
            $sql = "SELECT agentmanagerid, agency_name FROM `vtiger_agentmanager`";
            $result = $db->pquery($sql, array());
            $agents = array();
            while ($row =& $result->fetchRow()) {
                $agents[$row[1]] = $row[0];
            }
            if (array_key_exists($groups[$assignedTo], $agents)) {
                return $agents[$groups[$assignedTo]];
            } else {
                //TODO: Handle Vanline Groups
            }
        }
        return $assignedTo;
    }
    /**
     *     Gets the tariffs for the current user, or a user based on the agent that it is given for local or interstate
     *     @param boolean local Optional boolean True to get Local Tariffs, False to get Interstate Tariffs, defaults to false
     *	   @param array agents Optional array of agents to get tariffs for
     *     @return array Array of Tariff IDs for the given parameters
     */
    public function getCurrentUserTariffs($local=false, $agents=null)
    {
        $db = PearDatabase::getInstance();
        if (empty($agents[0])) {
            $agents = Users_Record_Model::getCurrentUserModel()->getUserAgents();
        }
        if (count($agents) == 0) {
            return array();
        }
        $tariffs = array();
        $idcolumn = $local ? '`vtiger_tariffs`.tariffsid' : 'tariffid';
        $tablename = $local ? "vtiger_tariffs`
		JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
		JOIN `vtiger_groups` ON `vtiger_groups`.`groupid` = `vtiger_crmentity`.`smownerid` AND `vtiger_crmentity`.`setype` = 'Tariffs'
		JOIN `vtiger_agentmanager` ON `vtiger_groups`.`groupname` = `vtiger_agentmanager`.`agency_name" : 'vtiger_tariff2agent';
        $columnname = $local ? '`vtiger_agentmanager`.`agentmanagerid`' : 'agentid';
        foreach ($agents as $agencyId) {
            $sql = "SELECT $idcolumn FROM `$tablename` WHERE $columnname=?";
            $result = $db->pquery($sql, array($agencyId));
            while ($row =& $result->fetchRow()) {
                $tariffs[] = $row[0];
            }
        }
        return $tariffs;
    }
}
