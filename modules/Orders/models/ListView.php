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
class Orders_ListView_Model extends Vtiger_ListView_Model
{
    /**
     * Function to get the Module Model
     * @return Vtiger_Module_Model instance
     */
    public function getModule()
    {
        return $this->get('module');
    }

    /**
     * Function to get the Quick Links for the List view of the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $moduleLinks = $this->getModule()->getSideBarLinks($linkParams);

        $listLinkTypes = array('LISTVIEWSIDEBARLINK', 'LISTVIEWSIDEBARWIDGET');
        $listLinks = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $listLinkTypes);

        if ($listLinks['LISTVIEWSIDEBARLINK']) {
            foreach ($listLinks['LISTVIEWSIDEBARLINK'] as $link) {
                $moduleLinks['SIDEBARLINK'][] = $link;
            }
        }

        if ($listLinks['LISTVIEWSIDEBARWIDGET']) {
            foreach ($listLinks['LISTVIEWSIDEBARWIDGET'] as $link) {
                $moduleLinks['SIDEBARWIDGET'][] = $link;
            }
        }

        return $moduleLinks;
    }

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

        $basicLinks = $this->getBasicLinks();

        foreach ($basicLinks as $basicLink) {
            $links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
        }

        $advancedLinks = $this->getAdvancedLinks();

        foreach ($advancedLinks as $advancedLink) {
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


        $massActionLinks = array();
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
                'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
                'linkicon' => ''
            );
        }

        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
        if ($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_ADD_COMMENT',
                'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showAddCommentForm',
                'linkicon' => ''
            );
        }

        $massActionLinks[] = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'LBL_PRINT',
            'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=printRecords',
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
     * Function to get the list view header
     * @return <Array> - List of Vtiger_Field_Model instances
     */
    public function getListViewHeaders()
    {
        $listViewContoller = $this->get('listview_controller');
        $module = $this->getModule();
        $enableGuests = QueryGenerator::isCustomViewGuestModulesEnabled($module->getName());
        $headerFieldModels = array();
        $headerFields = $listViewContoller->getListViewHeaderFields();
        $db = &PearDatabase::getInstance();
        foreach ($headerFields as $fieldName => $webserviceField) {
            if ($webserviceField && !in_array($webserviceField->getPresence(), array(0, 2))) {
                continue;
            }
            if($enableGuests) {
                $module = Vtiger_Module::getInstance($db->pquery('SELECT `name` FROM vtiger_tab where tabid=?', [$webserviceField->getTabId()])->fetchRow()['name']);
            }

            //@TODO: duplicated needs refactored:
            //include/ListView/ListViewController.php include/QueryGenerator/QueryGenerator.php modules/CustomView/models/Record.php modules/CustomView/models/Record.php modules/Opportunities/models/ListView
            preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);
			if(count($matches) > 0) {
                list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
                
                if($referenceParentField == 'guest_blocks' && $referenceModule == 'MoveRoles'){

                    $referenceFieldModel = MoveRoles_Field_Model::getFieldModelFromName($referenceFieldName);

                    $label = explode('_',$referenceFieldName);
                    array_shift($label);
                    $label = implode(' ', $label);
                    $referenceFieldModel->set('label',$label);
                    $referenceFieldModel->set('column','vtiger_crmentity' . $referenceFieldName . '.label');

                    $headerFieldModels[$fieldName] = $referenceFieldModel->set('name', $fieldName);
                }else{

                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
                    $referenceFieldModel = Vtiger_Field_Model::getInstance($referenceFieldName, $referenceModuleModel);
                    $referenceFieldModel->set('webserviceField', $webserviceField);

                    $referenceFieldModel->set('listViewRawFieldName', $referenceParentField.$referenceFieldName);

                    $headerFieldModels[$fieldName] = $referenceFieldModel->set('name', $fieldName); // resetting the fieldname as we use it to fetch the value from that name
                }

				$matches=null;
			} else {
				$fieldInstance = Vtiger_Field_Model::getInstance($fieldName,$module);
				$fieldInstance->set('listViewRawFieldName', $fieldInstance->get('column'));
				$headerFieldModels[$fieldName] = $fieldInstance;
			}
        }
        return $headerFieldModels;
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

        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }

        if (count($searchParams)) {
            $flag = false;
            $column_array = $searchParams[0]['columns'];

            foreach ($column_array as $key => $parametro) {
                if (!is_array($parametro["value"]) && $parametro["value"] == "") {
                    unset($column_array[$key]);
                    $flag = true;
                }
            }

            if ($flag) {
                end($column_array);
                $key = key($column_array);

                $column_array[$key]["column_condition"] = "";

                reset($column_array);
            }

            $searchParams[0]['columns'] = $column_array;
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
            $orderBy = 'vtiger_crmentity.modifiedtime';
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

        $collapse = true;

        if($queryGenerator->guestBlocksColumns && !QueryGenerator::isCustomViewGuestModulesEnabled($moduleName)){
            $collapse = false;
        }

        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult, $collapse);

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
            if($collapse) {
                $record['id'] = $recordId;
            } else {
                $record['id'] = $record['_recordId'];
            }
            //agent owner display
            $sql = "SELECT agency_name, agency_code FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
            $result = $db->pquery($sql, [$rawData['agentid']]);
            $row = $result->fetchRow();
            if ($row != null) {
                $record['agentid'] = '('.$row['agency_code'].') '.$row['agency_name'];
            } else {
                $record['agentid'] = '--';
            }

            if ($collapse) {
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
            } else {
                $listViewRecordModels[] = $moduleModel->getRecordFromArray($record, $rawData);
            }
        }
        //old securities
        //$db = PearDatabase::getInstance();

        //$userRecordModels = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        //$currentUserId = $userModel->getId();

        //$isAdmin = $userModel->isAdminUser();

        //if not admin then remove orders user does not have access too
        /*if(!$isAdmin){
            $userOrders = array();

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
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    $groupOwned[] = $row[0];
                    $row = $result->fetchRow();
                }
            }
            foreach($listViewRecordModels as $key => $recordModel){
                //add orders owned by current users agent group to list
                foreach($groupOwned as $ownedEntity){
                    if($ownedEntity == $key  && !in_array($recordModel, $userOrders)){
                        $userOrders[$key] = $recordModel;
                    }
                }
                if(empty($sourceModule)){
                    //include orders where users agent group is a participating agent
                    $participatingAgents = array();
                    $participatingAgentNames = array();
                    $sql = "SELECT agentid FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions!=3";
                    $result = $db->pquery($sql, array($key));
                    $row = $result->fetchRow();
                    while($row != null){
                        $participatingAgents[] = $row[0];
                        $row = $result->fetchRow();
                    }
                    foreach($participatingAgents as $participatingAgent){
                        $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                        $result = $db->pquery($sql, array($participatingAgent));
                        $row = $result->fetchRow();
                        $participatingAgentNames[] = $row[0];
                    }
                    /*$sql = "SELECT participating_agents_full FROM `vtiger_orders` WHERE ordersid=?";
                    $result = $db->pquery($sql, array($key));
                    $row = $result->fetchRow();
                    $participatingAgentsFull = $row[0];
                    $participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
                    $sql = "SELECT participating_agents_no_rates FROM `vtiger_orders` WHERE ordersid=?";
                    $result = $db->pquery($sql, array($key));
                    $row = $result->fetchRow();
                    $participatingAgentsNoRates = $row[0];
                    $participatingAgentsNoRates = explode(' |##| ', $participatingAgentsNoRates);
                    $participatingAgents = array_merge($participatingAgentsFull, $participatingAgentsNoRates);
                    foreach($participatingAgentNames as $participatingAgentName){
                        foreach($userGroupNames as $groupName){
                            if($groupName == $participatingAgentName && !in_array($recordModel, $userOrders)){
                                $userOrders[$key] = $recordModel;
                            }
                        }
                    }
                }
            }
            //salesPerson list parsing
            $userRole = $userModel->getRole();
            $sql = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
            $result = $db->pquery($sql, array($userRole));
            $row = $result->fetchRow();
            $roleName = $row[0];
            $oppRelated = array(
                'Potentials' => array('vtiger_potential', 'potentialid', 'potentialid'),
                'Opportunities' => array('vtiger_potential', 'potentialid', 'potentialid'),
                'Estimates' => array('vtiger_quotes', 'quoteid', 'potentialid'),
                'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
                'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
                'Stops' => array('vtiger_stops', 'stopsid', 'stop_opp'),
                'Surveys' => array('vtiger_surveys', 'surveysid', 'potential_id'),
                'Cubesheets' => array('vtiger_cubesheets', 'cubesheetsid', 'potential_id'),
            );
            $orderRelated = array(
                'Orders' => array('vtiger_orders', 'ordersid', 'ordersid'),
                'Estimates' => array('vtiger_quotes', 'quoteid', 'orders_id'),
                'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
                'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
                'HelpDesk' => array('vtiger_crmentityrel', 'relcrmid', 'crmid'),
                'Claims' => array('vtiger_claims', 'claimsid', 'claims_order'),
                'Stops' => array('vtiger_stops', 'stopsid', 'stop_order'),
                'OrdersMilestone' => array('vtiger_ordersmilestone', 'ordersmilestoneid', 'ordersid'),
                'OrdersTask' => array('vtiger_orderstask', 'orderstaskid', 'ordersid'),
                'Storage' => array('vtiger_storage', 'storageid', 'storage_orders'),
                'Trips' => array('vtiger_crmentityrel', 'relcrmid', 'crmid'),
            );
            $leadsRelated = array(
                'Leads' => array('vtiger_leaddetails', 'leadid', 'leadid'),
                'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
                'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
            );
            if(strpos($roleName, 'Sales Person') !== false && (array_key_exists($moduleName, $orderRelated) || array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $leadsRelated))){
                $salesEntries = array();
                foreach($listViewRecordModels as $key => $recordModel){
                    if(array_key_exists($moduleName, $orderRelated)){
                        if($moduleName == 'Orders'){
                            $sql = "SELECT sales_person FROM `vtiger_orders` WHERE ordersid=?";
                        } else{
                            $sql = "SELECT vtiger_orders.sales_person FROM `vtiger_orders` INNER JOIN ".$orderRelated[$moduleName][0]." ON vtiger_orders.ordersid = ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][2]." WHERE ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][1]."=?";
                        }
                        //file_put_contents('logs/devLog.log', "\n ORDER SQL: $sql", FILE_APPEND);
                        $result = $db->pquery($sql, array($key));
                        $row = $result->fetchRow();
                        $salesPerson = $row[0];
                        //file_put_contents('logs/devLog.log', "\n ORDER SALES PERSON: $salesPerson", FILE_APPEND);
                        if($salesPerson == $currentUserId && !in_array($recordModel, $salesEntries)){
                            $salesEntries[$key] = $recordModel;
                        }
                    }
                    if(array_key_exists($moduleName, $oppRelated)){
                        if($moduleName == 'Potentials' || $moduleName == 'Opportunities'){
                            $sql = "SELECT sales_person FROM `vtiger_potential` WHERE potentialid=?";
                        } else{
                            $sql = "SELECT vtiger_potential.sales_person FROM `vtiger_potential` INNER JOIN ".$oppRelated[$moduleName][0]." ON vtiger_potential.potentialid = ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][2]." WHERE ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][1]."=?";
                        }
                        //file_put_contents('logs/devLog.log', "\n OPP SQL: $sql", FILE_APPEND);
                        $result = $db->pquery($sql, array($key));
                        $row = $result->fetchRow();
                        $salesPerson = $row[0];
                        //file_put_contents('logs/devLog.log', "\n OPP SALES PERSON: $salesPerson", FILE_APPEND);
                        if($salesPerson == $currentUserId && !in_array($recordModel, $salesEntries)){
                            $salesEntries[$key] = $recordModel;
                        }
                    }
                    if(array_key_exists($moduleName, $leadsRelated)){
                        if($moduleName == 'Leads'){
                            $sql = "SELECT sales_person FROM `vtiger_leaddetails` WHERE leadid=?";
                        } else{
                            $sql = "SELECT vtiger_leaddetails.sales_person FROM `vtiger_leaddetails` INNER JOIN ".$leadsRelated[$moduleName][0]." ON vtiger_leaddetails.leadid = ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][2]." WHERE ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][1]."=?";
                        }
                        //file_put_contents('logs/devLog.log', "\n LEAD SQL: $sql", FILE_APPEND);
                        $result = $db->pquery($sql, array($key));
                        $row = $result->fetchRow();
                        $salesPerson = $row[0];
                        //file_put_contents('logs/devLog.log', "\n LEAD SALES PERSON: $salesPerson", FILE_APPEND);
                        if($salesPerson == $currentUserId && !in_array($recordModel, $salesEntries)){
                            $salesEntries[$key] = $recordModel;
                        }
                    }
                    if((array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $orderRelated)) && $moduleName == 'Documents'){
                        //extra logic to allow sales persons to see any record with no assigned order or opportunity person
                        $sql = "SELECT ".$oppRelated[$moduleName][2]." FROM ".$oppRelated[$moduleName][0]." WHERE ".$oppRelated[$moduleName][1]."=?";
                        $result = $db->pquery($sql, array($key));
                        $row = $result->fetchRow();
                        $assignedOpp = $row[0];
                        $sql = "SELECT ".$orderRelated[$moduleName][2]." FROM ".$orderRelated[$moduleName][0]." WHERE ".$orderRelated[$moduleName][1]."=?";
                        $result = $db->pquery($sql, array($key));
                        $row = $result->fetchRow();
                        $assignedOrder = $row[0];
                        //file_put_contents('logs/devLog.log', "\n assopp: $assignedOpp, assord: $assignedOrder", FILE_APPEND);
                        if(!$assignedOpp && !$assignedOrder && !in_array($recordModel, $salesEntries)){
                            $salesEntries[$key] = $recordModel;
                        }
                    }
                }
                $pagingModel->calculatePageRange($salesEntries);
                return $salesEntries;
            }
            $pagingModel->calculatePageRange($userOrders);
            return $userOrders;
        }*/ //else{
            return $listViewRecordModels;
        //}
    }

    /**
     * Function to get the list view entries
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
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

        //file_put_contents("logs/devLog.log", "\n participatingOrders: ".print_r($participatingOrders, true), FILE_APPEND);
        $listResult = $db->pquery($listQuery, array());
        //$queryResult = $db->query_result($listResult, 0, 'count');

        //file_put_contents("logs/devLog.log", "\n queryResult: ".$queryResult, FILE_APPEND);
        return $db->getRowCount($listResult);
    }

    public function getQuery()
    {
        $queryGenerator = $this->get('query_generator');
        $listQuery = $queryGenerator->getQuery();
        return $listQuery;
    }
    /**
     * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
     * @param <String> $moduleName - Module Name
     * @param <Number> $viewId - Custom View Id
     * @return Vtiger_ListView_Model instance
     */
    public static function getInstance($moduleName, $viewId='0')
    {
        $db = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');

        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $moduleName);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        $customView = new CustomView();
        if (!empty($viewId) && $viewId != "0") {
            $queryGenerator->initForCustomViewById($viewId);

            //Used to set the viewid into the session which will be used to load the same filter when you refresh the page
            $viewId = $customView->getViewId($moduleName);
        } else {
            $viewId = $customView->getViewId($moduleName);
            if (!empty($viewId) && $viewId != 0) {
                $queryGenerator->initForDefaultCustomView();
            } else {
                $entityInstance = CRMEntity::getInstance($moduleName);
                $listFields = $entityInstance->list_fields_name;
                $listFields[] = 'id';
                $queryGenerator->setFields($listFields);
            }
        }
        $controller = new ListViewController($db, $currentUser, $queryGenerator);

        return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
    }

    /**
     * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
     * @param <String> $value - Module Name
     * @param <Number> $viewId - Custom View Id
     * @return Vtiger_ListView_Model instance
     */
    public static function getInstanceForPopup($value)
    {
        $db = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');

        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($value);

        $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);

        $listFields = $moduleModel->getPopupViewFieldsList();

        $listFields[] = 'id';
        $queryGenerator->setFields($listFields);

        $controller = new ListViewController($db, $currentUser, $queryGenerator);

        return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
    }

    /*
     * Function to give advance links of a module
     *	@RETURN array of advanced links
     */
    public function getAdvancedLinks()
    {
        $moduleModel = $this->getModule();
        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
        $advancedLinks = array();
        $importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
        if ($importPermission && $createPermission) {
            $advancedLinks[] = array(
                            'linktype' => 'LISTVIEW',
                            'linklabel' => 'LBL_IMPORT',
                            'linkurl' => $moduleModel->getImportUrl(),
                            'linkicon' => ''
            );
        }

        $exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
        if ($exportPermission) {
            $advancedLinks[] = array(
                    'linktype' => 'LISTVIEW',
                    'linklabel' => 'LBL_EXPORT',
                    'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$this->getModule()->getExportUrl().'")',
                    'linkicon' => ''
                );
        }

        $duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
        if ($duplicatePermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_FIND_DUPLICATES',
                'linkurl' => 'Javascript:Vtiger_List_Js.showDuplicateSearchForm("index.php?module='.$moduleModel->getName().
                                '&view=MassActionAjax&mode=showDuplicatesSearchForm")',
                'linkicon' => ''
            );
        }

        return $advancedLinks;
    }

    /*
     * Function to get Setting links
     * @return array of setting links
     */
    public function getSettingLinks()
    {
        return $this->getModule()->getSettingLinks();
    }

    /*
     * Function to get Basic links
     * @return array of Basic links
     */
    public function getBasicLinks()
    {
        $basicLinks = array();
        $moduleModel = $this->getModule();
        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
        if ($createPermission) {
            $basicLinks[] = array(
                    'linktype' => 'LISTVIEWBASIC',
                    'linklabel' => 'LBL_ADD_RECORD',
                    'linkurl' => $moduleModel->getCreateRecordUrl(),
                    'linkicon' => ''
            );
        }
        return $basicLinks;
    }

    public function extendPopupFields($fieldsList)
    {
        $moduleModel = $this->get('module');
        $queryGenerator = $this->get('query_generator');

        $listFields = $moduleModel->getPopupViewFieldsList();

        $listFields[] = 'id';
        $listFields = array_merge($listFields, $fieldsList);
        $queryGenerator->setFields($listFields);
    }
}
