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
class Vtiger_ListView_Model extends Vtiger_Base_Model
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
            $massEditLink = 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
            if($moduleModel->isCheckBeforeEditDeleteRequired()) {
                $massEditLink = 'javascript:Vtiger_List_Js.checkAndTriggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
            }
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $massEditLink,
                'linkicon' => ''
            );
        }
        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            $massDeleteLink = 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");';
            if($moduleModel->isCheckBeforeEditDeleteRequired()) {
                $massDeleteLink = 'javascript:Vtiger_List_Js.checkAndTriggerMassDelete("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
            }
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => $massDeleteLink,
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
        $duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
        if($duplicatePermission) {
            $massActionLinks[] = [
                'linktype'  => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DUPLICATE',
                'linkurl'   => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=duplicateRecords',
                'linkicon'  => ''
            ];
        }
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
        $headerFieldModels = array();
        $headerFields = $listViewContoller->getListViewHeaderFields();
        foreach ($headerFields as $fieldName => $webserviceField) {
            if ($webserviceField && !in_array($webserviceField->getPresence(), array(0, 2))) {
                continue;
            }

            if($webserviceField && $webserviceField->parentReferenceField && !in_array($webserviceField->parentReferenceField->getPresence(), array(0,2))){
				continue;
			}

            //@TODO: duplicated needs refactored:
            //include/ListView/ListViewController.php include/QueryGenerator/QueryGenerator.php modules/CustomView/models/Record.php modules/CustomView/models/Record.php modules/Opportunities/models/ListView
            // check if the field is reference field
			preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);
			if(count($matches) > 0) {
				list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
				$referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
				$referenceFieldModel = Vtiger_Field_Model::getInstance($referenceFieldName, $referenceModuleModel);
				$referenceFieldModel->set('webserviceField', $webserviceField);

				$referenceFieldModel->set('listViewRawFieldName', $referenceParentField.$referenceFieldName);

				$headerFieldModels[$fieldName] = $referenceFieldModel->set('name', $fieldName); // resetting the fieldname as we use it to fetch the value from that name
				$matches=null;
			} else {
				$fieldInstance = Vtiger_Field_Model::getInstance($fieldName,$module);
				$fieldInstance->set('listViewRawFieldName', $fieldInstance->get('column'));
				$headerFieldModels[$fieldName] = $fieldInstance;
			}
        }
        return $headerFieldModels;
    }

    public function getListViewExtraHeaders()
    {
        $listViewContoller = $this->get('listview_controller');
        return $listViewContoller->getListViewExtraHeaders();
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
        if ($sortOrder == 'NONE'||$sortOrder == 'none') {
            $sortOrder = '';
        }
        if ($orderBy == 'NONE' || $orderBy== 'none') {
            $orderBy = '';
        }

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
            /*********************************/
                /*if($moduleName === 'AgentManager') {
                    $currentUserModel = Users_Record_Model::getCurrentUserModel();
                    $currentUserId = $currentUserModel->getId();
                    $sql = "SELECT depth FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE userid=?";
                    $result = $db->pquery($sql, array($currentUserId));
                    $row = $result->fetchRow();
                    $depth = $row[0];

                    $validAgencies = array();

                    if ($depth == 3) {
                        //file_put_contents('logs/devLog.log', "\n depth is 3", FILE_APPEND);
                        $sql = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid=?";
                        $result = $db->pquery($sql, array($currentUserId));
                        $queryAddon = '';
                        while($row =& $result->fetchRow())
                        {
                            if($queryAddon == '') {
                                $queryAddon = ' AND (vanline_id='.$row[0];
                            } else {
                                $queryAddon .= ' OR vanline_id='.$row[0];
                            }
                        }
                        if($queryAddon != '') {
                            $queryAddon .= ')';
                        }
                        //file_put_contents('logs/devLog.log', "\n \$vanlineid: ".$vanlineid, FILE_APPEND);

                        $listQuery .= $queryAddon;
                        //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').$listQuery."\n", FILE_APPEND);
                    }
                }*/
            /*********************************/

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
            if($orderByFieldModel && $orderByFieldModel->getName() == 'effective_tariff') {
                // This isn't pretty but it works because of the JOINS done by QueryGenerator.
                // Because for whatever reason a generator of queries cannot handle order by.
                $listQuery .= " ORDER BY vtiger_tariffmanager.tariffmanagername ".$sortOrder.", vtiger_tariffs.tariff_name ".$sortOrder;
            }else if ($orderByFieldModel && $orderByFieldModel->isReferenceField()) {
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

        if($sourceModule == 'TariffServices' && $this->get('src_field') == 'tariff_section') {
            // @TODO: Remove this horrific thing and do it right
            $listQuery = preg_replace('/\sWHERE\s/i', ' WHERE related_tariff=\''.$db->sql_escape_string($_REQUEST['related_tariff']).'\' AND ', $listQuery, 1);
        }


        
        $listResult = $db->pquery($listQuery, array());

        $collapse = true;

        if($queryGenerator->guestBlocksColumns && !QueryGenerator::isCustomViewGuestModulesEnabled($moduleName)){
            $collapse = false;
        }

        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult, $collapse);

        //this is done at the return for different versions
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
            if (isset($rawData['agentid'])) {
                try {
                    $agentRecordModel   = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'AgentManager');
                    $vanlineRecordModel = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'VanlineManager');
                    $displayValue       =
                    $agentRecordModel->get('agency_name')?' ('.$agentRecordModel->get('agency_code').') '.$agentRecordModel->get('agency_name'):$vanlineRecordModel->get('vanline_name');
                    if ($displayValue != null) {
                        $record['agentid'] = $displayValue;
                    } else {
                        $record['agentid'] = '--';
                    }
                } catch (Exception $e) {
                    $record['agentid'] = '--';
                }
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        return $listViewRecordModels;

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

        //file_put_contents('logs.devLog.log', "\n MODULE NAME: ".$moduleName, FILE_APPEND);

        $listQuery = $this->getQuery();
        //old securities
        /*********************************/
        /* if($moduleName === 'AgentManager') {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $currentUserId = $currentUserModel->getId();
            $sql = "SELECT depth FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid = `vtiger_role`.roleid WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $depth = $row[0];

            $validAgencies = array();

            if ($depth == 3) {
                file_put_contents('logs/devLog.log', "\n depth is 3", FILE_APPEND);
                $sql = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid=?";
                $result = $db->pquery($sql, array($currentUserId));
                $queryAddon = '';
                while($row =& $result->fetchRow())
                {
                    if($queryAddon == '') {
                        $queryAddon = ' AND (vanline_id='.$row[0];
                    } else {
                        $queryAddon .= ' OR vanline_id='.$row[0];
                    }
                }
                if($queryAddon != '') {
                    $queryAddon .= ')';
                }
                //file_put_contents('logs/devLog.log', "\n \$vanlineid: ".$vanlineid, FILE_APPEND);

                $listQuery .= $queryAddon;
                file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').$listQuery."\n", FILE_APPEND);
            }
        } */
        /*********************************/

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

        // $userModel = Users_Record_Model::getCurrentUserModel();
        // $currentUserId = $userModel->getId();

        // $isAdmin = $userModel->isAdminUser();

        // $sql = "SELECT *
                // FROM  `vtiger_user2role`
                // JOIN  `vtiger_role` ON  `vtiger_user2role`.roleid =  `vtiger_role`.roleid
                // WHERE userid =?";
        // $result = $db->pquery($sql, array($currentUserId));
        // $row = $result->fetchRow();
        // $depth = $row['depth'];

        /* if(!$isAdmin && $depth != 3){
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


            $sql = "SELECT *
                    FROM  `vtiger_user2role`
                    JOIN  `vtiger_role` ON  `vtiger_user2role`.roleid =  `vtiger_role`.roleid
                    WHERE userid =?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            $depth = $row['depth'];
            if($depth == 6){
                $parents = explode('::',$row['parentrole']);
                //file_put_contents('logs/devLog.log',"\n parents : ".print_r($parents,true),FILE_APPEND);
                $sql = "SELECT `vtiger_user2role`.userid
                        FROM  `vtiger_user2role`
                        JOIN  `vtiger_role` ON  `vtiger_user2role`.roleid =  `vtiger_role`.roleid
                        WHERE `vtiger_role`.roleid=?";
                $result = $db->pquery($sql, array($parents[4]));
                $row = $result->fetchRow();
                $listQuery .= 'OR vtiger_crmentity.smownerid = '.$row[0].' ';
            }

            $listQuery .= ')';
            if($depth == 6){
                $sql = "SELECT agency_code
                        FROM  `vtiger_user2agency`
                        WHERE userid =?";
                $result = $db->pquery($sql, array($currentUserId));
                $row = $result->fetchRow();
                $listQuery .= ' AND agentmanagerid = '.$row[0];
            }
        }

        $oppRelated = array(
            'Estimates' => array('vtiger_quotes', 'quoteid', 'potentialid'),
            'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
            'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
            'Stops' => array('vtiger_stops', 'stopsid', 'stop_opp'),
            'Surveys' => array('vtiger_surveys', 'surveysid', 'potential_id'),
            'Cubesheets' => array('vtiger_cubesheets', 'cubesheetsid', 'potential_id'),
        );
        $orderRelated = array(
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

        $userRole = $userModel->getRole();
        $sql = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
        $result = $db->pquery($sql, array($userRole));
        $row = $result->fetchRow();
        $roleName = $row[0];
        $moduleName = $this->getModule()->get('name'); */

        /* if((array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $orderRelated) || array_key_exists($moduleName, $leadsRelated)) && strpos($roleName, 'Sales Person')){
            $salesEntries = array();
            if(array_key_exists($moduleName, $orderRelated)){
                $sql = "SELECT ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][1]." FROM ".$orderRelated[$moduleName][0]." INNER JOIN vtiger_orders ON ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][2]." = vtiger_orders.ordersid WHERE vtiger_orders.sales_person = ".$currentUserId;
                $result = $db->pquery($sql, array());
                $row = $result->fetchRow();
                while($row != null){
                    if(!in_array($row[0], $salesEntries)){
                            $salesEntries[] = $row[0];
                    }
                    $row = $result->fetchRow();
                }
            }
            if(array_key_exists($moduleName, $oppRelated)){
                $sql = "SELECT ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][1]." FROM ".$oppRelated[$moduleName][0]." INNER JOIN vtiger_potential ON ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][2]." = vtiger_potential.potentialid WHERE vtiger_potential.sales_person = ".$currentUserId;
                $result = $db->pquery($sql, array());
                $row = $result->fetchRow();
                while($row != null){
                    if(!in_array($row[0], $salesEntries)){
                            $salesEntries[] = $row[0];
                    }
                    $row = $result->fetchRow();
                }
            }
            if(array_key_exists($moduleName, $leadsRelated)){
                $sql = "SELECT ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][1]." FROM ".$leadsRelated[$moduleName][0]." INNER JOIN vtiger_leaddetails ON ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][2]." = vtiger_leaddetails.leadid WHERE vtiger_leaddetails.sales_person = ".$currentUserId;
                $result = $db->pquery($sql, array());
                $row = $result->fetchRow();
                while($row != null){
                    if(!in_array($row[0], $salesEntries)){
                            $salesEntries[] = $row[0];
                    }
                    $row = $result->fetchRow();
                }
            }
            if($moduleName == 'Documents'){
                $sql = "SELECT vtiger_notes.notesid FROM vtiger_notes LEFT JOIN vtiger_senotesrel ON vtiger_notes.notesid = vtiger_senotesrel.notesid WHERE vtiger_senotesrel.notesid IS NULL OR vtiger_senotesrel.notesid = ''";
                $result = $db->pquery($sql, array());
                $row = $result->fetchRow();
                while($row != null){
                    if(!in_array($row[0], $salesEntries)){
                            $salesEntries[] = $row[0];
                    }
                    $row = $result->fetchRow();
                }
            }
            return count($salesEntries);
        } */

        //file_put_contents("logs/devLog.log", "\n V-LISTQUERY: ".$listQuery, FILE_APPEND);

        $listResult = $db->pquery($listQuery, array());

        // not sure if this is the right thing to do here
        if (strpos($listQuery, 'GROUP BY ') === false) {
            $queryResult = $db->query_result($listResult, 0, 'count');
        } else {
            $queryResult = $db->num_rows($listResult);
        }
        return $queryResult;
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
        $verifySQL = "SELECT * FROM `vtiger_customview` WHERE cvid = '$viewId' AND entitytype = '$moduleName'";
        $result = $db->pquery($verifySQL, array());
        $returnedRows = $db->num_rows($result);
        if($returnedRows == 0){
            $viewId = '0';
        }
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
    public static function getInstanceForPopup($value, $viewId='0')
    {
        //file_put_contents("logs/devLog.log", "\n CORE POP-UP GETINSTANCE!!!!!!", FILE_APPEND);
        //file_put_contents("logs/devLog.log", "\n CORE POP-UP GETINSTANCE VALUE: $value", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');

        //file_put_contents("logs/devLog.log", "\n CORE POP-UP GETINSTANCE CURRENT USER: ".print_r($currentUser, true), FILE_APPEND);

        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($value);

        $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        $customView = new CustomView();
        if (!empty($viewId) && $viewId != "0") {
            $queryGenerator->initForCustomViewById($viewId);
        }else{
            $listFields = $moduleModel->getPopupViewFieldsList();

            $listFields[] = 'id';
            $queryGenerator->setFields($listFields);
        }

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
        if (($duplicatePermission) && (getenv('INSTANCE_NAME') != 'sirva')) {
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

    public function getDefaultSorting($cvId)
    {
        global $adb;
        $default=[];
        $rs=$adb->pquery("select sort_field, sort_order from vtiger_customview WHERE cvid=?", array($cvId));
        if($rs) {
            $default['sort_field'] = $adb->query_result($rs, 0, 'sort_field');
            $default['sort_order'] = $adb->query_result($rs, 0, 'sort_order');
        }
        return $default;
    }
}
