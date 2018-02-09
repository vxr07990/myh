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
class Opportunities_ListView_Model extends Vtiger_ListView_Model
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
    /*public function getListViewMassActions($linkParams) {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleModel = $this->getModule();

        $linkTypes = array('LISTVIEWMASSACTION');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);


        $massActionLinks = array();
        if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
                'linkicon' => ''
            );
        }
        if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
                'linkicon' => ''
            );
        }

        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
        if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
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
            'linklabel' => 'LBL_DUPLICATE',
            'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=duplicateRecords',
            'linkicon' => ''
        );

        foreach($massActionLinks as $massActionLink) {
            $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $links;
    }*/

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
            //@TODO: duplicated needs refactored:
            //include/ListView/ListViewController.php include/QueryGenerator/QueryGenerator.php modules/CustomView/models/Record.php modules/CustomView/models/Record.php modules/Opportunities/models/ListView
              // check if the field is reference field
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
                // $queryGenerator->whereFields[] = $orderByFieldName;
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
        //file_put_contents('logs/devLog.log', "\n row count: " . $db->num_rows($listResult), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n p-limit: " . $pageLimit, FILE_APPEND);

        if ($db->num_rows($listResult) > $pageLimit) {
            //file_put_contents('logs/devLog.log', "\n yep, next page exists", FILE_APPEND);
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
            $recordModel = Vtiger_Record_Model::getInstanceById($record['id'], 'Opportunities');
            //opp display workaround
            $salesDisplay = '';
            $ownerDisplay = '';

            // This was not working due to $listQuery overwriting smownerid in the join clause which was giving us the wrong smownerid
            //$sql = "SELECT first_name, last_name FROM `vtiger_users` WHERE id=?";
            //$result = $db->pquery($sql, array($rawData['smownerid']));

            //assigned_user_id display
            Opportunities_Record_Model::handleAdditionalListViewLogic($rawData, $record);

                        //forecaster amount display
            if (getenv('INSTANCE_NAME') == 'sirva') {
                if($recordModel->getPrimaryEstimateRecordModel(false)) {
                    $db = PearDatabase::getInstance();
                    $currentUser = Users_Record_Model::getCurrentUserModel();
                    $estimateRecordId = $recordModel->getPrimaryEstimateRecordModel(false)->getId();
                    $total = $db->pquery("SELECT total FROM `vtiger_quotes` WHERE quoteid = ?", [$estimateRecordId])->fetchRow()['total'];
                    $decimalSeperator = $currentUser->get('currency_decimal_seperator') ? $currentUser->get('currency_decimal_seperator') : '.';
                    $groupingSeperator = $currentUser->get('currency_grouping_seperator') ? $currentUser->get('currency_grouping_seperator') : ',';
                    $total = number_format($total, 2, $decimalSeperator, $groupingSeperator);
                    $total = '$' . $total;
                    if ($total) {
                        $record['amount'] = $total;
                    } else {
                        $record['amount'] = '--';
                    }
                }
                
                $this->gatherParticipatingAgentInfo($record);
            }

            //assemble array into list view record model
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        return $listViewRecordModels;
    }

    public function gatherParticipatingAgentInfo(&$record) {
        // Don't even worry about this in the off chance an ID doesn't exist.
        if(!$record['id']) {
            return;
        }

        $types = [
            "oa" => "Origin Agent",
            "da" => "Destination Agent",
            "ba" => "Booking Agent",
            "ea" => "Estimating Agent",
            "ha" => "Hauling Agent"
        ];
        $fields = ["code","name","city","state"];
        $model = Opportunities_Record_Model::getInstanceById($record['id'], "Opportunities");
        foreach($types as $type => $name) {
            foreach($fields as $field) {
                $list_field = $type . "_" . $field;

                if(array_key_exists($list_field, $record)) {

                    $record[$list_field] = $model->getParticipantInfo($name, $field);
                }
            }
        }
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
            //$listQuery = 'SELECT count(DISTINCT `vtiger_crmentity`.crmid) AS count ';
            $listQuery = 'SELECT count(*) AS count ';
            for ($i=1; $i<$splitCount; $i++) {
                $listQuery = $listQuery. ' FROM ' .$split[$i];
            }
        }

        //file_put_contents('logs/devLog.log', "\n CQ: $listQuery", FILE_APPEND);

        if ($this->getModule()->get('name') == 'Calendar') {
            $listQuery .= ' AND activitytype <> "Emails"';
        }
        //old securities
        //$userModel = Users_Record_Model::getCurrentUserModel();
        //$currentUserId = $userModel->getId();

        //$isAdmin = $userModel->isAdminUser();

        /*if(!$isAdmin){

            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            $userGroupNames = array();

            foreach($userGroups as $group){
                $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }

            $listQuery .= ' AND (vtiger_crmentity.smownerid = '.$currentUserId.' ';
            foreach($userGroups as $userGroup){
                $listQuery .= 'OR vtiger_crmentity.smownerid = '.$userGroup.' ';
            }
            $listQuery .= ')';

            $allOpportunities = array();
            $sql = "SELECT vtiger_potential.potentialid FROM `vtiger_potential` LEFT JOIN `vtiger_crmentity` ON vtiger_potential.potentialid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();

            while($row != null){
                $allOpportunities[] = $row[0];
                $row = $result->fetchRow();
            }

            $participatingOpps = array();

            foreach($allOpportunities as $currentOpp){
                $participatingAgents = array();
                /*$sql = "SELECT participating_agents_full FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($currentOpp));
                $row = $result->fetchRow();
                $participatingAgentsFull = $row[0];
                $participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($currentOpp));
                $row = $result->fetchRow();
                $participatingAgentsNoRates = $row[0];
                $participatingAgentsNoRates = explode(' |##| ', $participatingAgentsNoRates);
                $participatingAgents = array_merge($participatingAgentsFull, $participatingAgentsNoRates);
                foreach($participatingAgents as $participatingAgent){
                    foreach($userGroups as $group){
                        if($group == $participatingAgent && !in_array($currentOpp, $participatingOpps)){
                            $participatingOpps[] = $currentOpp;
                        }
                    }
                }
                $participatingAgentNames = array();
                $sql = "SELECT agent_id FROM `vtiger_participating_agents` WHERE crmentity_id=? AND permission!=3 AND status=1";
                $result = $db->pquery($sql, array($currentOpp));
                $row = $result->fetchRow();
                while($row != null){
                    $participatingAgents[] = $row[0];
                    $row = $result->fetchRow();
                }
                //file_put_contents('logs/devLog.log', "\n participatingAgents: ".print_r($participatingAgents, true), FILE_APPEND);
                foreach($participatingAgents as $participatingAgent){
                    $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                    $result = $db->pquery($sql, array($participatingAgent));
                    $row = $result->fetchRow();
                    $participatingAgentNames[] = $row[0];
                }
                foreach($participatingAgentNames as $participatingAgentName){
                    foreach($userGroupNames as $groupName){
                        if($groupName == $participatingAgentName  && !in_array($currentOpp, $participatingOpps)){
                            $participatingOpps[] = $recordModel;
                        }
                    }
                }
            }
        }*/
        //file_put_contents('logs/devLog.log', "\n list count sql: $listQuery", FILE_APPEND);
        $listResult = $db->pquery($listQuery, array());
        $queryResult = $db->num_rows($listResult);
        //$queryResult = $db->query_result($listResult, 0, 'count');
        //return $queryResult+count($participatingOpps);
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
