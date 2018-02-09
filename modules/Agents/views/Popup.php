<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Agents_Popup_View extends Vtiger_Popup_View
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
        if ($sourceModule == 'OrdersTask') {
            $this->listViewEntries = $this->getApplicableParticipatingAgents($sourceRecord);
        } elseif ($sourceModule == 'Storage') {
            if(!getenv('IGC_MOVEHQ') || getenv('INSTANCE_NAME') == 'graebel') {
                $this->listViewEntries = $this->getApplicableParticipatingAgents($sourceRecord);
            }
        } elseif ($sourceModule == 'Claims' || $sourceModule == 'ClaimItems') {
            //logic for all claims modules
            $isRelationOperation = $request->get('isRelationOperation');
            $claimSourceModule = $request->get('claimSourceModule');
            $claimSourceRecord = $request->get('claimSourceRecord');
            $linkedClaim = $request->get('linked_claim');

            if ($isRelationOperation != '' && $isRelationOperation == 'true') {
                $orderId = $this->getOrderId($claimSourceModule, $claimSourceRecord);
            } elseif ($linkedClaim != '') {
                $orderId = $this->getOrderId('Claims', $linkedClaim);
            } else {
                $orderId = $this->getOrderId($sourceModule, $sourceRecord);
            }

            $this->listViewEntries = $this->getApplicableParticipatingAgents($orderId);
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
     *     Gets the appropriate participating agents based on the given source record
     *
     * @param int sourceRecord The given source Order CRM id
     *
     * @return array Array of AgentManager records for the given Order
     */
    public function getApplicableParticipatingAgents($sourceRecord)
    {
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        $agentType = $request->get('agent_type');
        $storageLocation = $request->get('storage_location');
        if ($request->get('storage_orders') != '') {
            $sourceRecord = $request->get('storage_orders');
        }
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT agents_id FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND deleted=0";
        $params = [$sourceRecord];

        if ($agentType != '') {
            $sql .= ' AND agent_type=?';
            array_push($params, $agentType);
        } elseif ($storageLocation != '') {
            if ($storageLocation == 'Origin' || $storageLocation == 'Destination') {
                $storageLocation .= ' Agent';
                $sql .= ' AND agent_type=?';
                array_push($params, $storageLocation);
            }
        }


        $result = $db->pquery($sql, $params);

        $agents = [];
        if ($result) {
            while ($row = & $result->fetchRow()) {
                $agents[] = Vtiger_Record_Model::getInstanceById($row[0]);
            }
        }

        return $agents;
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
            $listViewModel     = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);

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

    public function getOrderId($sourceModule, $sourceRecord)
    {
        $db = PearDatabase::getInstance();

        switch ($sourceModule) {
            case 'ClaimsSummary':
                $sql = 'SELECT claimssummary_orderid FROM vtiger_claimssummary WHERE claimssummaryid=?';
                $orderId = $db->pquery($sql, [$sourceRecord])->fetchRow()[0];
            break;
            case 'Claims':
                $sql = 'SELECT claimssummary_orderid FROM vtiger_claimssummary LEFT JOIN vtiger_claims ON vtiger_claims.claimssummary_id=vtiger_claimssummary.claimssummaryid WHERE claimsid=?';
                $orderId = $db->pquery($sql, [$sourceRecord])->fetchRow()[0];
            break;

        }
        return $orderId;
    }
}
