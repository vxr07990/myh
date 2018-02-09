<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class TariffManager_Popup_View extends Vtiger_Popup_View
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
        $moveType            = $request->get('move_type');
        $currentUser = vglobal('current_user');
        $agent               = $request->get('agentId');

        //To handle special operation when selecting record from Popup
        $getUrl = $request->get('get_url');
        //Check whether the request is in multi select mode
        $multiSelectMode = $request->get('multi_select');
        if (empty($multiSelectMode)) {
            $multiSelectMode = false;
        }
        if (empty($cvId)) {
            $customView     = new CustomView();
            $cvId = $customView->getViewId($moduleName);
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
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
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
        //old method invalidated
        // This doesn't load the correct tariffs due to it ignoring $assignedTo
        //$agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        //$this->listViewEntries = $this->getCurrentUserTariffs(false, $agents);
        // Returns an agent of a vanline, else returns the argument.
        $agent                 = $this->getVanlineAgent($agent);
        $this->listViewEntries = $this->getTariffs($agent, $this->isLocal($moveType));

        // Don't know if other brands want these tariffs for these.
        if(getenv('INSTANCE_NAME') == 'sirva' && $this->isLocal($moveType)) {
          $this->addMAXTariffs();
        }

        //limit contract popup list via the new flag "contract_allowed" in the tariff record.
        if ($sourceModule == 'Contracts') {
            $tempEntries = [];
            foreach ($this->listViewEntries as $recordModel) {
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    //only sirva has this checkbox, maybe expanding
                    //contracts_allowed is a checkbox
                    if(strpos($recordModel->get('tariff_type'), 'Max') !== false) {
                        $recordModel->set('tariffmanagername', $recordModel->get('tariff_name'));
                        $recordModel->set('tariff_type', 'Intrastate');
                        $tempEntries[] = $recordModel;
                    }
                    if (
                        $recordModel->get('contracts_allowed') &&
                        $recordModel->get('contracts_allowed') != 'no'
                    ) {
                        $tempEntries[] = $recordModel;
                    }
                } else {
                    $tempEntries[] = $recordModel;
                }
            }
            $this->listViewEntries = $tempEntries;
        }

        $noOfEntries           = count($this->listViewEntries);
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

    public function isLocal($moveType) {
        // I'm unsure if O&I is local, but on estimates interstate tariffs do not populate, so putting it here.
        $localMoveTypes = ['Intrastate','Local Canada','Intra-Provincial', 'O&I'];
        return in_array($moveType, $localMoveTypes);
    }

    public function getTariffs($agentId, $local = false) {
      $db = PearDatabase::getInstance();
      if(!$local) {
        $sql = "SELECT `vtiger_tariff2agent`.`tariffid` FROM `vtiger_tariffmanager`
                  JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_tariffmanager`.`tariffmanagerid`
                  JOIN `vtiger_tariff2agent` ON `vtiger_tariff2agent`.`tariffid` = `vtiger_tariffmanager`.`tariffmanagerid`
                  WHERE `vtiger_tariff2agent`.`agentid` = ? AND `vtiger_crmentity`.deleted != 1";

        $res = $db->pquery($sql, [$agentId]);
        $tariffs = [];
        if(!$res) {
          return false;
        }
        while($row = $res->fetchRow()) {
          $tariffs[] = TariffManager_Record_Model::getInstanceById($row[0]);
        }
        return $tariffs;
      }else{
          return [];
      }
    }

    public function getVanlineAgent($vanlineId) {
      $db = PearDatabase::getInstance();

      $sql = "SELECT am.agentmanagerid FROM `vtiger_agentmanager` AS am WHERE am.vanline_id = ? LIMIT 1";

      $res = $db->pquery($sql, [$vanlineId]);
      if($db->num_rows($res) > 0) {
        return $res->fetchRow()[0];
      }else return $vanlineId;
    }

    public function addMAXTariffs() {
      $db = PearDatabase::getInstance();

      $sql = "SELECT tariffsid FROM vtiger_tariffs
                JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_tariffs.tariffsid
                WHERE tariff_type LIKE 'Max%' AND vtiger_crmentity.deleted != 1";

      $res = $db->query($sql);
      while($row = $res->fetchRow()) {
        $this->listViewEntries[] = Tariffs_Record_Model::getInstanceById($row[0]);
      }
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
     *     Gets the tariffs for the current user, or a user based on the agent that it is given for local or interstate
     *
     * @param boolean local Optional boolean True to get Local Tariffs, False to get Interstate Tariffs, defaults to false
     * @param array   agents Optional array of agents to get tariffs for
     *
     * @return array Array of Tariff IDs for the given parameters
     */
    public function getCurrentUserTariffs($local = false, $agents = null)
    {
        //file_put_contents('logs/devLog.log', "\n In getCurrentUserTariffs", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n local: $local", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n agents: ".print_r($agents, true), FILE_APPEND);
        $isAdmin = Users_Record_Model::getCurrentUserModel()->isAdminUser();
        $db      = PearDatabase::getInstance();
        if (empty($agents[0])) {
            //$agents = Users_Record_Model::getCurrentUserModel()->getUserAgents();
            $agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        }
        //file_put_contents('logs/devLog.log', "\n isAdmin: $isAdmin", FILE_APPEND);
        if (count($agents) == 0) {
            return [];
        }
        file_put_contents('logs/devLog.log', "\n In getCurrentUserTariffs", FILE_APPEND);
        $tariffs    = [];
        $idcolumn   = $local?'`vtiger_tariffs`.tariffsid':'`vtiger_tariff2agent`.tariffid';
        $tablename  = $local
            ?"vtiger_tariffs`
		JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
		JOIN `vtiger_groups` ON `vtiger_groups`.`groupid` = `vtiger_crmentity`.`smownerid` AND `vtiger_crmentity`.`setype` = 'Tariffs'
		JOIN `vtiger_agentmanager` ON `vtiger_groups`.`groupname` = `vtiger_agentmanager`.`agency_name"
            :'vtiger_tariff2agent`
		JOIN `vtiger_crmentity` ON `vtiger_tariff2agent`.`tariffid` = `vtiger_crmentity`.`crmid';
        $columnname = $local?'`vtiger_agentmanager`.`agentmanagerid`':'`vtiger_tariff2agent`.agentid';
        if ($isAdmin) {
            $sql    = "SELECT DISTINCT tariffid FROM `$tablename` WHERE `vtiger_crmentity`.`deleted` != 1";
            $result = $db->pquery($sql, []);
            while ($row =& $result->fetchRow()) {
                $tariffs[] = $local?Tariffs_Record_Model::getInstanceById($row[0]):TariffManager_Record_Model::getInstanceById($row[0]);
            }
        } else {
            foreach ($agents as $agencyId => $agencyName) {
                $sql    = "SELECT $idcolumn FROM `$tablename` WHERE $columnname=? AND `vtiger_crmentity`.deleted != 1";
                $result = $db->pquery($sql, [$agencyId]);
                while ($row =& $result->fetchRow()) {
                    //set the ID as the index so we don't dupe
                    $tariffs[$row[0]] = $local?Tariffs_Record_Model::getInstanceById($row[0]):TariffManager_Record_Model::getInstanceById($row[0]);
                }
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
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
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
