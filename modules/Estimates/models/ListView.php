<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Estimates_ListView_Model extends Quotes_ListView_Model
{
    public function getListViewCount()
    {
        $db = PearDatabase::getInstance();

        $queryGenerator = $this->get('query_generator');

        
        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);
        
        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if (!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }
        $moduleName = $this->getModule()->get('name');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        

        $listQuery = $this->getQuery();


        $sourceModule = $this->get('src_module');
        if (!empty($sourceModule)) {
            $moduleModel = $this->getModule();
            if (method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
                if (!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }
        $position = stripos($listQuery, ' from ');
        if ($position) {
            $split = spliti(' from ', $listQuery);
            $splitCount = count($split);
            $listQuery = 'SELECT count(*) AS count ';
            for ($i=1; $i<$splitCount; $i++) {
                $listQuery = $listQuery. ' FROM ' .$split[$i];
            }
        }

        if ($this->getModule()->get('name') == 'Calendar') {
            $listQuery .= ' AND activitytype <> "Emails"';
        }
        //old securities
        // $userModel = Users_Record_Model::getCurrentUserModel();
        // $currentUserId = $userModel->getId();

        // $isAdmin = $userModel->isAdminUser();

        /* if(!$isAdmin){

            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            $listQuery .= ' AND (vtiger_crmentity.smownerid = '.$currentUserId.' ';
            foreach($userGroups as $userGroup){
                $listQuery .= 'OR vtiger_crmentity.smownerid = '.$userGroup.' ';
            }
            $listQuery .= ')';

            $allEstimates = array();
            $sql = "SELECT vtiger_quotes.quoteid FROM `vtiger_quotes` LEFT JOIN `vtiger_crmentity` ON vtiger_quotes.quoteid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            while($row != null){
                $allEstimates[] = $row[0];
                $row = $result->fetchRow();
            }

            // $participatingEstimates = array();

            /* foreach($allEstimates as $currentEstimate){
                $sql = "SELECT potentialid, orders_id FROM `vtiger_quotes` WHERE quoteid = ?";
                $result = $db->pquery($sql, array($currentEstimate));
                $row = $result->fetchRow();
                $potentialId = $row[0];
                $orderId = $row[1];

                //include entries where users agent group is a participating agent
                $participatingAgents = array();
                $sql = "SELECT participating_agents_full FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                $participatingAgentsFullOpportunities = $row[0];
                $participatingAgentsFullOpportunities = explode(' |##| ', $participatingAgentsFullOpportunities);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                $participatingAgentsNoRatesOpportunities = $row[0];
                $participatingAgentsNoRatesOpportunities = explode(' |##| ', $participatingAgentsNoRatesOpportunities);
                $sql = "SELECT participating_agents_full FROM `vtiger_orders` WHERE ordersid=?";
                $result = $db->pquery($sql, array($orderId));
                $row = $result->fetchRow();
                $participatingAgentsFullOrders = $row[0];
                $participatingAgentsFullOrders = explode(' |##| ', $participatingAgentsFullOrders);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_orders` WHERE ordersid=?";
                $result = $db->pquery($sql, array($orderId));
                $row = $result->fetchRow();
                $participatingAgentsNoRatesOrders = $row[0];
                $participatingAgentsNoRatesOrders = explode(' |##| ', $participatingAgentsNoRatesOrders);
                $participatingAgents = array_merge($participatingAgentsFullOpportunities, $participatingAgentsNoRatesOpportunities, $participatingAgentsFullOrders, $participatingAgentsNoRatesOrders);
                //file_put_contents("logs/devLog.log", "\n PARTICIPATING AGENTS: ".print_r($participatingAgents, true), FILE_APPEND);
                foreach($participatingAgents as $participatingAgent){
                    foreach($userGroups as $group){
                        if($group == $participatingAgent && !in_array($currentEstimate, $participatingEstimates)){
                            $participatingEstimates[] = $currentEstimate;
                        }
                    }
                }
            }
        } */
        
        $listResult = $db->pquery($listQuery, array());
        // copied from default models ListView
        if (strpos($listQuery, 'GROUP BY ') === false) {
            $queryResult = $db->query_result($listResult, 0, 'count');
        } else {
            $queryResult = $db->num_rows($listResult);
        }
        //return $queryResult+count($participatingEstimates);
        return $queryResult;
    }
    
    /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
    public function getListViewMassActions($linkParams)
    {

        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleModel = $this->getModule();

        $links = parent::getListViewMassActions($linkParams);
        $listviewLinks = $links['LISTVIEWMASSACTION'];


        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            foreach ($listviewLinks as $key => $value) {
                if($value->linklabel == 'LBL_DUPLICATE'){
                    $links['LISTVIEWMASSACTION'][$key]->linkurl = 'javascript:triggerDuplicate()';
                }
            }
        }

        return $links;
        
    }
}
