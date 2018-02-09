<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Contracts_Popup_View extends Vtiger_Popup_View
{
    protected $listViewEntries = false;
    protected $listViewHeaders = false;

    /*
     * Function to initialize the required data in smarty to display the List View Contents
     */
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName          = $this->getModule($request);
        $cvId                = $request->get('cvid');
        $pageNumber          = $request->get('page');
        $orderBy             = $request->get('orderby');
        $sortOrder           = $request->get('sortorder');
        $sourceModule        = $request->get('src_module');
        $sourceField         = $request->get('src_field');
        $sourceRecord        = $request->get('src_record');
        $searchKey           = $request->get('search_key');
        $searchValue         = $request->get('search_value');
        $currencyId          = $request->get('currency_id');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId     = $request->get('related_parent_id');
        $assignedTo          = $request->get('assignedTo');
        $loadDate            = $request->get('loadDate');
        $accountId           = $request->get('accountId');
        $businessLine        = $request->get('businessLine');
        $currentUser = vglobal('current_user');
        //To handle special operation when selecting record from Popup
        $getUrl = $request->get('get_url');
        //Check whether the request is in multi select mode
        $multiSelectMode = $request->get('multi_select');
        if (empty($multiSelectMode)) {
            $multiSelectMode = false;
        }
        if (empty($cvId)) {
            $customView     = new CustomView();
            $cvId = $customView->getDefaultFilter($moduleName);
        }
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $isRecordExists          = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);
        if ($isRecordExists) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        } elseif ($isRecordExists === null) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel     = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label);

            $searchParmams = $request->get('search_params');
            if (empty($searchParmams)) {
                $searchParmams = [];
            }
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, Vtiger_Module_Model::getInstance($moduleName));
            // generate where conditions based on custom view and search params
            $queryGenerator = new QueryGenerator($moduleName, $currentUser);
            $queryGenerator->initForCustomViewById($cvId);

            if (empty($transformedSearchParams)) {
                $transformedSearchParams = array();
            }
            $glue = "";
            if (count($queryGenerator->getWhereFields()) > 0 && (count($transformedSearchParams)) > 0) {
                $glue = QueryGenerator::$AND;
            }
            $queryGenerator->parseAdvFilterList($transformedSearchParams, $glue);
            $whereCondition = $queryGenerator->getWhereClause();
            $listViewModel->set('query_generator', $queryGenerator);
            $listViewModel->set('whereCondition', $whereCondition);
        } else {
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);

            $searchParmams = $request->get('search_params');
            if (empty($searchParmams)) {
                $searchParmams = [];
            }
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());

            $listViewModel->set('search_params', $transformedSearchParams);
            foreach ($searchParmams as $fieldListGroup) {
                foreach ($fieldListGroup as $fieldSearchInfo) {
                    $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                    $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                    $searchParmams[$fieldName]      = $fieldSearchInfo;
                }
            }
        }
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        } else {
            $defaulSorting = $listViewModel->getDefaultSorting($cvId);
            if ($defaulSorting['sort_field']) {
                $listViewModel->set('orderby', $defaulSorting['sort_field']);
                $listViewModel->set('sortorder', $defaulSorting['sort_order']);
            }
        }
        if (!empty($sourceModule)) {
            $listViewModel->set('src_module', $sourceModule);
            $listViewModel->set('src_field', $sourceField);
            $listViewModel->set('src_record', $sourceRecord);
        }
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $this->listViewHeaders = $listViewModel->getHeaders();
            $models                = $listViewModel->getEntries($pagingModel);
            $noOfEntries           = count($models);
            foreach ($models as $recordId => $recordModel) {
                foreach ($this->listViewHeaders as $fieldName => $fieldModel) {
                    $recordModel->set($fieldName, $recordModel->getDisplayValue($fieldName));
                }
                $models[$recordId] = $recordModel;
            }
            $this->listViewEntries = $models;
            if (count($this->listViewEntries) > 0) {
                $parent_related_records = true;
            }
        } else {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // If there are no related records with parent module then, we should show all the records
        if (!$parent_related_records && !empty($relatedParentModule) && !empty($relatedParentId)) {
            $relatedParentModule = null;
            $relatedParentId     = null;
            $listViewModel       = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
            if (!empty($orderBy)) {
                $listViewModel->set('orderby', $orderBy);
                $listViewModel->set('sortorder', $sortOrder);
            } else {
                $defaulSorting = $listViewModel->getDefaultSorting($cvId);
                if ($defaulSorting['sort_field']) {
                    $listViewModel->set('orderby', $defaulSorting['sort_field']);
                    $listViewModel->set('sortorder', $defaulSorting['sort_order']);
                }
            }
            if (!empty($sourceModule)) {
                $listViewModel->set('src_module', $sourceModule);
                $listViewModel->set('src_field', $sourceField);
                $listViewModel->set('src_record', $sourceRecord);
            }
            if ((!empty($searchKey)) && (!empty($searchValue))) {
                $listViewModel->set('search_key', $searchKey);
                $listViewModel->set('search_value', $searchValue);
            }
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // End
        //old method, agents doesn't seem to be used though..
        //$agent                 = $this->getAssignedAgent($assignedTo);
        $agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        $this->listViewEntries = $this->getApplicableContracts($loadDate, $agents, $accountId, $sourceModule, $sourceField, $businessLine);
        //file_put_contents('logs/devLog.log', "\n HEADERS: ".print_r($this->listViewHeaders, true), FILE_APPEND);
        if ($sourceField == 'agmt_id') {
            $this->listViewHeaders['contract_no']->set('label', 'Agmt Id');
            unset($this->listViewHeaders['parent_contract']);
            unset($this->listViewHeaders['billing_apn']);
            unset($this->listViewHeaders['rate_per_100']);
        }
        //file_put_contents('logs/devLog.log', "\n listViewEntiresAfter : ".print_r($this->listViewEntries,true), FILE_APPEND);
        $noOfEntries = count($this->listViewEntries);
        //file_put_contents('logs/devLog.log', "\n noOfEntries : ".$noOfEntries,FILE_APPEND);
        if (empty($sortOrder)) {
            $sortOrder = "ASC";
        }
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "downArrowSmall.png";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "upArrowSmall.png";
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $moduleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('RELATED_PARENT_MODULE', $relatedParentModule);
        $viewer->assign('RELATED_PARENT_ID', $relatedParentId);
        $viewer->assign('SEARCH_KEY', $searchKey);
        $viewer->assign('SEARCH_VALUE', $searchValue);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('GETURL', $getUrl);
        $viewer->assign('CURRENCY_ID', $currencyId);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);
        $viewer->assign('RELATED_ACCOUNT_ID', $accountId);
        $viewer->assign('BUSINESS_LINE', $businessLine);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', $noOfEntries);
        $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            if (!$this->listViewCount) {
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $totalCount = $this->listViewCount;
            $pageLimit  = $pagingModel->getPageLimit();
            $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
        }

        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('VIEWID', $cvId);

        $viewer->assign('LISTVIEW_COUNT', $noOfEntries);
        $pagingModel->set('range', ['start' => 1, 'end' => $noOfEntries]);
        //file_put_contents('logs/devLog.log', "\n PAGING MODEL RANGE: ".print_r($pagingModel->get('range'), true), FILE_APPEND);
        $viewer->assign('MULTI_SELECT', $multiSelectMode);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }

    /**
     *     Gets the agent or vanline ID for the provided assignedTo
     *
     * @param integer assignedTo The CRMID that will be used for the lookup
     *
     * @return integer The agent or vanline ID for the provided assignedTo, or if user returns the same value
     */
    public function getAssignedAgent($assignedTo)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT groupid, groupname FROM `vtiger_groups`";
        $result = $db->pquery($sql, []);
        $groups = [];
        while ($row =& $result->fetchRow()) {
            $groups[$row[0]] = $row[1];
        }
        if (array_key_exists($assignedTo, $groups)) {
            $sql    = "SELECT agentmanagerid, agency_name FROM `vtiger_agentmanager`";
            $result = $db->pquery($sql, []);
            $agents = [];
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
     *     Gets the appropriate contracts based on the loadDate and assignedTo provided
     *
     * @param string loadDate the date to use to find corresponding Contracts, must be in Y-m-d format
     * @param array  agents Array of agents to get tariffs for
     *
     * @return array Array of Contract Record Models for the given parameters
     */
    public function getApplicableContracts($loadDate, $agents = null, $accountId = null, $sourceModule, $sourceField = false, $businessLine = false)
    {
        if (!$loadDate) {
            $loadDate = date('Y-m-d');
        }
        //$db      = PearDatabase::getInstance();
        //$isAdmin = Users_Record_Model::getCurrentUserModel()->isAdminUser();
        /*
        if (empty($agents[0])) {
            //$agents = Users_Record_Model::getCurrentUserModel()->getUserAgents();
            $agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        }
        if (count($agents) == 0) {
            return [];
        }
        */

        $contracts = [];

        $contractIds = [];
        if (getenv('INSTANCE_NAME') != 'sirva' && $accountId && $businessLine) {
            $db = PearDatabase::getInstance();
            $sql    = "SELECT vtiger_contracts.contractsid,vtiger_contracts.business_line FROM `vtiger_crmentityrel` JOIN `vtiger_contracts` ON vtiger_contracts.contractsid = vtiger_crmentityrel.relcrmid WHERE relmodule = ? AND crmid = ?";

            $result = $db->pquery($sql, ['Contracts', $accountId]);
            while ($row =& $result->fetchRow()) {
                if (\MoveCrm\InputUtils::MultiselectIntersects($row['business_line'], $businessLine)) {
                    $contractIds[] = $row['contractsid'];
                }
            }
        } elseif ($accountId) {
            $db = PearDatabase::getInstance();
            $sql    = "SELECT vtiger_contracts.contractsid FROM `vtiger_crmentityrel` JOIN `vtiger_contracts` ON vtiger_contracts.contractsid = vtiger_crmentityrel.relcrmid WHERE relmodule = ? AND crmid = ?";

            $result = $db->pquery($sql, ['Contracts', $accountId]);
            while ($row =& $result->fetchRow()) {
                $contractIds[] = $row['contractsid'];
            }
        } elseif ($businessLine) {
            // this will be SO SLOW
            $db = PearDatabase::getInstance();
            $sql    = "SELECT vtiger_contracts.contractsid,vtiger_contracts.business_line FROM `vtiger_contracts`";

            $result = $db->pquery($sql, []);
            while ($row =& $result->fetchRow()) {
                if (\MoveCrm\InputUtils::MultiselectIntersects($row['business_line'], $businessLine)) {
                    $contractIds[] = $row['contractsid'];
                }
            }
        }

        foreach ($this->listViewEntries as $contract) {
            $parent_contract = $contract->get('parent_contract');
            if (getenv('INSTANCE_NAME') === 'sirva') {
                if ($sourceModule === 'Contracts' || $sourceField === 'agmt_id') {
                    if ($parent_contract !== '--') {
                        //only show parent contracts when we're in the Contracts module
                        continue;
                    }
                } else {
                    if ($parent_contract === '--') {
                        //only show sub contracts in other modules.
                        continue;
                    }
                }
                //$contracts[] = $contract;
                //continue;
            }

            //Only show contracts related to the selected account. if selected.
            if(getenv('INSTANCE_NAME') != 'sirva') {
            if (($accountId || $businessLine) && (!in_array($contract->getRawData()['contractsid'], $contractIds))) {
                continue;
                }
            } else {
                $contractAccount = $contract->getRawData()['account_id'];
                if ($accountId != $contractAccount) {
                    continue;
                }
            }

            //loop through the user's available contracts
            $begin_date = DateTimeField::convertToDBTimeZone($contract->get('begin_date'))->format('Y-m-d');
            $end_date   = DateTimeField::convertToDBTimeZone($contract->get('end_date'))->format('Y-m-d');
            $loadDate   = DateTimeField::convertToDBTimeZone($loadDate)->format('Y-m-d');

            $begin_dt = new DateTime($begin_date);
            $end_dt   = new DateTime($end_date);
            $load_dt  = new DateTime($loadDate);

            if ($load_dt >= $begin_dt) {
                //the load date is after the contract's begin date.
                if (!$contract->get('end_date') || ($load_dt <= $end_dt)) {
                    //the load date is before the contract's end date or there is no end date
                    $contracts[] = $contract;
                }
            }
        }
        /*
        if (!$isAdmin) {
            //file_put_contents('logs/devLog.log', "\n AGENTIDS: ".print_r($agents, true), FILE_APPEND);
            foreach ($agents as $agentId) {
                //file_put_contents('logs/devLog.log', "\n AGENTID: $agentId", FILE_APPEND);
                $sql     =
                    "SELECT `vtiger_groups`.groupid FROM `vtiger_groups` JOIN `vtiger_agentmanager` ON `vtiger_agentmanager`.agency_name = `vtiger_groups`.groupname WHERE `vtiger_agentmanager`.agentmanagerid = ?";
                $result  = $db->pquery($sql, [$agentId]);
                $row     = $result->fetchRow();
                $groupId = $row[0];
                $sql     = "SELECT DISTINCT `vtiger_contracts`.contractsid
                        FROM `vtiger_contracts`
                        JOIN `vtiger_crmentity`
                            ON `vtiger_contracts`.contractsid = `vtiger_crmentity`.crmid
                        LEFT JOIN `vtiger_contract2agent`
                            ON `vtiger_contracts`.contractsid = `vtiger_contract2agent`.contractid
                        WHERE  ( `vtiger_contract2agent`.agentid = ?
                                  OR `vtiger_crmentity`.smownerid = ? )
                        AND ? > `vtiger_contracts`.begin_date
                        AND ? < `vtiger_contracts`.end_date
                        AND `vtiger_crmentity`.deleted = 0";
                $params  = [$agentId, $groupId, $loadDate, $loadDate];
                if ($accountId != NULL) {
                    $sql .= ' AND `vtiger_contracts`.account_id = ?';
                    $params[] = $accountId;
                }
                //file_put_contents('logs/devLog.log', "\n params: ".print_r($params, $sql), FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n SQL: $sql", FILE_APPEND);
                $result = $db->pquery($sql, $params);
                while ($row =& $result->fetchRow()) {
                    $contracts[] = Contracts_Record_Model::getInstanceById($row[0]);
                }
            }
        } else {
            $sql    = "SELECT DISTINCT `vtiger_contracts`.contractsid
                        FROM `vtiger_contracts`
                        JOIN `vtiger_crmentity`
                            ON `vtiger_contracts`.contractsid = `vtiger_crmentity`.crmid
                        LEFT JOIN `vtiger_contract2agent`
                            ON `vtiger_contracts`.contractsid = `vtiger_contract2agent`.contractid
                        WHERE `vtiger_crmentity`.deleted = 0";
            $result = $db->pquery($sql, []);
            while ($row =& $result->fetchRow()) {
                $contracts[] = Contracts_Record_Model::getInstanceById($row[0]);
            }
        }
        */

        return $contracts;
    }

    /**
     *     Gets the tariffs for the current user, or a user based on the agent that it is given for local or interstate
     *
     * @param boolean local Optional boolean True to get Local Tariffs, False to get Interstate Tariffs, defaults to false
     * @param array   agents Optional array of agents to get tariffs for
     *
     * @return array Array of Tariff IDs for the given parameters
     */
    public function getCurrentUserTariffs($local = false, $agents = null)
    {
        $db = PearDatabase::getInstance();
        if (empty($agents[0])) {
            $agents = Users_Record_Model::getCurrentUserModel()->getUserAgents();
        }
        if (count($agents) == 0) {
            return [];
        }
        $tariffs    = [];
        $idcolumn   = $local?'`vtiger_tariffs`.tariffsid':'tariffid';
        $tablename  = $local?"vtiger_tariffs`
		JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
		JOIN `vtiger_groups` ON `vtiger_groups`.`groupid` = `vtiger_crmentity`.`smownerid` AND `vtiger_crmentity`.`setype` = 'Tariffs'
		JOIN `vtiger_agentmanager` ON `vtiger_groups`.`groupname` = `vtiger_agentmanager`.`agency_name":'vtiger_tariff2agent';
        $columnname = $local?'`vtiger_agentmanager`.`agentmanagerid`':'agentid';
        foreach ($agents as $agencyId) {
            $sql    = "SELECT $idcolumn FROM `$tablename` WHERE $columnname=?";
            $result = $db->pquery($sql, [$agencyId]);
            while ($row =& $result->fetchRow()) {
                $tariffs[] = $local?Tariffs_Record_Model::getInstanceById($row[0]):TariffManager_Record_Model::getInstanceById($row[0]);
            }
        }

        return $tariffs;
    }

    /**
     * Function to get listView count
     *
     * @param Vtiger_Request $request
     */
    public function getListViewCount(Vtiger_Request $request)
    {
        $moduleName          = $this->getModule($request);
        $cvId                = $request->get('cvid');
        $sourceModule        = $request->get('src_module');
        $sourceField         = $request->get('src_field');
        $sourceRecord        = $request->get('src_record');
        $orderBy             = $request->get('orderby');
        $sortOrder           = $request->get('sortorder');
        $currencyId          = $request->get('currency_id');
        $searchKey           = $request->get('search_key');
        $searchValue         = $request->get('search_value');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId     = $request->get('related_parent_id');
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel     = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label);
        } else {
            $listViewModel       = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
        }
        if (!empty($sourceModule)) {
            $listViewModel->set('src_module', $sourceModule);
            $listViewModel->set('src_field', $sourceField);
            $listViewModel->set('src_record', $sourceRecord);
            $listViewModel->set('currency_id', $currencyId);
        }
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        } else {
            $defaulSorting = $listViewModel->getDefaultSorting($cvId);
            if ($defaulSorting['sort_field']) {
                $listViewModel->set('orderby', $defaulSorting['sort_field']);
                $listViewModel->set('sortorder', $defaulSorting['sort_order']);
            }
        }
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $count = $listViewModel->getRelatedEntriesCount();
        } else {
            $count = $listViewModel->getListViewCount();
        }

        return $count;
    }

    /**
     * Function to get the page count for list
     * @return total number of pages
     */
    public function getPageCount(Vtiger_Request $request)
    {
        $listViewCount = $this->getListViewCount($request);
        $pagingModel   = new Vtiger_Paging_Model();
        $pageLimit     = $pagingModel->getPageLimit();
        $pageCount     = ceil((int) $listViewCount / (int) $pageLimit);
        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $result                    = [];
        $result['page']            = $pageCount;
        $result['numberOfRecords'] = $listViewCount;
        $response                  = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
