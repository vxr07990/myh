<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_ListView_Model extends Vtiger_ListView_Model
{

    /**
     * Function to get the list of listview links for the module
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams)
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleModel = $this->getModule();

        $linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
        if ($createPermission) {
            $basicLinks = array(
                    array(
                            'linktype' => 'LISTVIEWBASIC',
                            'linklabel' => 'LBL_ADD_RECORD',
                            'linkurl' => $moduleModel->getCreateRecordUrl(),
                            'linkicon' => ''
                    ),
                    array(
                            'linktype' => 'LISTVIEWBASIC',
                            'linklabel' => 'LBL_ADD_FOLDER',
                            'linkurl' => 'javascript:Documents_List_Js.triggerAddFolder("'.$moduleModel->getAddFolderUrl().'")',
                            'linkicon' => ''
                    )
            );
            foreach ($basicLinks as $basicLink) {
                $links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
            }
        }

        $exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
        if ($exportPermission) {
            $advancedLink = array(
                    'linktype' => 'LISTVIEW',
                    'linklabel' => 'LBL_EXPORT',
                    'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$moduleModel->getExportUrl().'")',
                    'linkicon' => ''
            );
            $links['LISTVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($advancedLink);
        }

        if ($currentUserModel->isAdminUser()) {
            $settingsLinks = $this->getSettingLinks();
            foreach ($settingsLinks as $settingsLink) {
                $links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
            }
        }
        return $links;
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

        $linkTypes = array('LISTVIEWMASSACTION');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

                //Opensource fix to make documents module mass editable
                if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
                    $massActionLinks[] = array(
                                'linktype' => 'LISTVIEWMASSACTION',
                                'linklabel' => 'LBL_EDIT',
                                'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
                                'linkicon' => ''
                        );
                }

        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module=' . $moduleModel->getName() . '&action=MassDelete");',
                'linkicon' => ''
            );
        }

        $massActionLinks[] = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'LBL_MOVE',
            'linkurl' => 'javascript:Documents_List_Js.massMove("index.php?module=' . $moduleModel->getName() . '&view=MoveDocuments");',
            'linkicon' => ''
        );

        $massActionLinks[] = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'LBL_EDITFILTER',
            'linkurl' => 'javascript:triggerEditFilter()',
            'linkicon' => ''
        );

        $massActionLinks[] = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'LBL_DELETEFILTER',
            'linkurl' => 'javascript:triggerDeleteFilter()',
            'linkicon' => ''
        );

        $massActionLinks[] = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'LBL_CREATEFILTER',
            'linkurl' => 'javascript:triggerCreateFilter()',
            'linkicon' => ''
        );

        foreach ($massActionLinks as $massActionLink) {
            $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $links;
    }

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

        $folderKey = $this->get('folder_id');
        $folderValue = $this->get('folder_value');
        if (!empty($folderValue)) {
            $queryGenerator->addCondition($folderKey, $folderValue, 'e');
        }

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
        if (!empty($orderBy) && $orderBy === 'smownerid') {
            $fieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $moduleModel);
            if ($fieldModel->getFieldDataType() == 'owner') {
                $orderBy = 'COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname)';
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
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0], 'last_name'=>$columnList[1]), 'Users').' '.$sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    }
                }
                $listQuery .= ' ORDER BY '. implode(',', $referenceNameFieldOrderBy);
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
        //$db = PearDatabase::getInstance();

        //$userRecordModels = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        //$currentUserId = $userModel->getId();

        //$isAdmin = $userModel->isAdminUser();
        /* if(!$isAdmin){
            $userDocuments = array();

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

            file_put_contents('logs/groupLog.log', date('Y-m-d H:i:s - ').print_r($userGroups, true)."\n", FILE_APPEND);

            $userGroupNames = array();

            foreach($userGroups as $group){
                $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }

            $groupOwned = array();
            foreach($userGroups as $group){
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
                    if($ownedEntity == $key  && !in_array($recordModel, $userDocuments)){
                        $userDocuments[$key] = $recordModel;
                    }
                }
            }
            return $userDocuments;
        } */ //else{
            return $listViewRecordModels;
        //}
    }
}
