<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger ListView Model Class
 */
class Contracts_ListView_Model extends Vtiger_ListView_Model
{
    /**
     * Function to get the list view entries
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();

        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');

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

        
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        if (empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $orderBy = 'modifiedtime';
            $sortOrder = 'DESC';
        }

        if (!empty($orderBy)) {
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
            if ($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE) {
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
                //$queryGenerator->whereFields[] = $orderByFieldName;
            }
        }
        $listQuery = $this->getQuery();

        $sourceModule = $this->get('src_module');
        if (!empty($sourceModule)) {
            if (method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
                if (!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        if (!empty($orderBy)) {
            if ($orderByFieldModel && $orderByFieldModel->isReferenceField()) {
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach ($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();

                    $columnList = array();
                    foreach ($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
                    }
                    if (count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0], 'last_name'=>$columnList[1]), 'Users', '').' '.$sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    }
                }
                $listQuery .= ' ORDER BY '. implode(',', $referenceNameFieldOrderBy);
            } elseif (!empty($orderBy) && $orderBy === 'smownerid') {
                $fieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $moduleModel);
                if ($fieldModel->getFieldDataType() == 'owner') {
                    $orderBy = 'COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname)';
                }
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            } else {
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            }
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if (empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

        $listQuery .= " LIMIT $startIndex,".($pageLimit+1);

        $listResult = $db->pquery($listQuery, array());

        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult);

        $pagingModel->calculatePageRange($listViewEntries);

        if ($db->num_rows($listResult) > $pageLimit) {
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }

        $index = 0;
        foreach ($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            //agent owner display
            $sql = "SELECT agency_name, agency_code FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
            $result = $db->pquery($sql, array($rawData['agentid']));
            $row = $result->fetchRow();
            if ($row != null) {
                $record['agentid'] = $row['agency_name'].' ('.$row['agency_code'].')';
            } else {
                $record['agentid'] = '--';
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }
        //old securities
        // $db = PearDatabase::getInstance();

        // $userRecordModels = array();
        // $userModel = Users_Record_Model::getCurrentUserModel();
        // $currentUserId = $userModel->getId();

        // $isAdmin = $userModel->isAdminUser();

        //file_put_contents("logs/devLog.log", "\n MODULENAME: ".$moduleName, FILE_APPEND);

        //if($moduleName=='Agents' || $moduleName=='Vanlines'){
            return $listViewRecordModels;
        //}

        //file_put_contents('logs/devLog.log', "\n isAdmin: $isAdmin", FILE_APPEND);

        //if not admin then remove orders user does not have access too
        /* if(!$isAdmin){
            $userEntries = array();

            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            $group2group = $userGroups;

            $sql = "SELECT DISTINCT groupid FROM `vtiger_group2grouprel` WHERE containsgroupid=?";
            foreach($group2group as $group) {
                $result = $db->pquery($sql, array($group));
                while($row =& $result->fetchRow()) {
                    $userGroups[] = $row['groupid'];
                }
            }

            $userGroupNames = array();

            foreach($userGroups as $group){
                $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }

            $groupOwned = array();
            foreach($userGroups as $group){

                //if vanline group is selected, add record to list view for selected agents
                $sql = "SELECT vtiger_contract2agent.contractid FROM `vtiger_contract2agent` INNER JOIN `vtiger_agentmanager` ON vtiger_contract2agent.agentid = vtiger_agentmanager.agentmanagerid
                INNER JOIN `vtiger_groups`ON vtiger_agentmanager.agency_name = vtiger_groups.groupname WHERE vtiger_groups.groupid = ?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    if(!in_array($row[0], $groupOwned)){
                        $groupOwned[] = $row[0];
                        $row = $result->fetchRow();
                    }
                }

                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    $groupOwned[] = $row[0];
                    $row = $result->fetchRow();
                }
            }

            foreach($listViewRecordModels as $key => $recordModel){
                //add opporunities owned by current users agent group to list
                foreach($groupOwned as $ownedEntity){
                    if($ownedEntity == $key  && !in_array($recordModel, $userEntries)){
                        $userEntries[$key] = $recordModel;
                    }
                }
            }
            $pagingModel->calculatePageRange($userEntries);
            //file_put_contents("logs/devLog.log", "\n USERENTRIES: ".count($userEntries), FILE_APPEND);
            return $userEntries;
        } else{
            //file_put_contents('logs/devLog.log', "\n LISTVIEWRECORDMODELS: ".print_r($listViewRecordModels, true), FILE_APPEND);
            return $listViewRecordModels;
        } */
    }
}
