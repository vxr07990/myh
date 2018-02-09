<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Orders_LDDList_View extends Vtiger_List_View
{
    protected $listViewEntries = false;
    protected $listViewCount   = false;
    protected $listViewLinks   = false;
    protected $listViewHeaders = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        $viewer        = $this->getViewer($request);
        $moduleName    = $request->getModule();
        if (empty($this->viewName) && !isset($_REQUEST['viewname'])) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView                               = new CustomView();
            $viewid                                   = $customView->getViewIdByName('Unplanned Long Distance Dispatch', $moduleName, true);
            $_SESSION['lvs'][$moduleName]["viewname"] = $viewid;
            $this->viewName                           = $viewid;
            $_REQUEST['viewname']                     = $viewid;
            $request =  new Vtiger_Request($_REQUEST, $_REQUEST);
        }

        parent::preProcess($request, false);

        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $this->viewName = $request->get('viewname');

        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('VIEWID', $this->viewName);
        $viewer->assign('PAGETITLE', 'Long Distance Dispatch');
        $this->getFilters($viewer);
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'ListViewPreProcess.tpl';
    }

    //Note : To get the right hook for immediate parent in PHP,
    // specially in case of deep hierarchy
    /*function preProcessParentTplName(Vtiger_Request $request) {
        return parent::preProcessTplName($request);
    }*/
    protected function preProcessDisplay(Vtiger_Request $request)
    {
        parent::preProcessDisplay($request);
    }

    public function process(Vtiger_Request $request)
    {
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $moduleModel    = Vtiger_Module_Model::getInstance($moduleName);
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView                               = new CustomView();
            $viewid                                   = $customView->getViewIdByName('Unplanned Long Distance Dispatch', $moduleName, true);
            $_SESSION['lvs'][$moduleName]["viewname"] = $viewid;
            $this->viewName                           = $viewid;
            $request->set('viewname',$viewid);
        }
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ListViewContents.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->view('ListViewPostProcess.tpl', $moduleName);
        parent::postProcess($request);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.List',
            "modules.$moduleName.resources.LDDList",
            'modules.CustomView.resources.CustomView',
            "modules.$moduleName.resources.CustomView",
            "modules.Emails.resources.MassEdit",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    /*
     * Function to initialize the required data in smarty to display the List View Contents
     */
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();
        $cvId       = $this->viewName;
        $pageNumber = $request->get('page');
        $orderBy    = $request->get('orderby');
        $sortOrder  = $request->get('sortorder');
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "icon-chevron-down";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "icon-chevron-up";
        }
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
        $currentUser   = Users_Record_Model::getCurrentUserModel();
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view'), 'CVID' => $cvId];
        $linkModels    = $listViewModel->getListViewMassActions($linkParams);
        $pagingModel   = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', 50);
        $pagingModel->set('viewid', $request->get('viewname'));
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        }
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $listViewModel->set('operator', $operator);
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
        }
        if (!empty($searchKey) && !empty($searchValue)) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        $searchParmams = $request->get('search_params');
        if (empty($searchParmams)) {
            $searchParmams = [];
        }
        //Added to filter Cancelled Orders
        $searchParmams[][] = [ 'ordersstatus', 'k', 'Cancelled', ];

		$oldSearchParmams = $searchParmams;
		
        $searchParmams = $this->array_remove_empty($searchParmams);
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
        $listViewModel->set('search_params', $transformedSearchParams);
        //To make smarty to get the details easily accesible
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }
        if (!$this->listViewHeaders) {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }

        # Commented conditional as this was blocked by previous execution by earlier
        # code instances. Uncomment if this introduces undesired functionality.
        #if (!$this->listViewEntries) {
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        #}

        $noOfEntries = count($this->listViewEntries);
        $viewer->assign('MODULE', $moduleName);
        if (!$this->listViewLinks) {
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);
        $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('COLUMN_NAME', $orderBy);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', $noOfEntries);
        $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        # Commented out conditional wrapper to allow page processing to occur.
        # Uncomment if undesired functionality occurs.
        #if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            if (!$this->listViewCount) {
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $totalCount = $this->listViewCount;
            $pagingModel->set('limit', 50);
            $pageLimit  = $pagingModel->getPageLimit();
            $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('LISTVIEW_COUNT', $totalCount);
        #}
        $viewer->assign('LIST_VIEW_MODEL', $listViewModel);
        $viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
		$searchParmams = $this->smartyEasilyAccesibleParams($oldSearchParmams);
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('LDDList', true);
    }
	public function smartyEasilyAccesibleParams($searchParmams){
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }
		
		return $searchParmams;
	}
	
    /**
     * Function returns the number of records for the current filter
     *
     * @param Vtiger_Request $request
     */
    public function getRecordsCount(Vtiger_Request $request)
    {
        $moduleName         = $request->getModule();
        $cvId               = $request->get('viewname');
        $count              = $this->getListViewCount($request);
        $result             = [];
        $result['module']   = $moduleName;
        $result['viewname'] = $cvId;
        $result['count']    = $count;
        $response           = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    /**
     * Function to get listView count
     *
     * @param Vtiger_Request $request
     */
    public function getListViewCount(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $cvId       = $request->get('viewname');
        if (empty($cvId)) {
            $cvId = '0';
        }
        $searchKey     = $request->get('search_key');
        $searchValue   = $request->get('search_value');
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
        $searchParmams = $request->get('search_params');
        $listViewModel->set('search_params', $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule()));
        $listViewModel->set('search_key', $searchKey);
        $listViewModel->set('search_value', $searchValue);
        $listViewModel->set('operator', $request->get('operator'));
        $count = $listViewModel->getListViewCount();

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
        $pagingModel->set('limit', 50);
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

    public function array_remove_empty($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->array_remove_empty($haystack[$key]);
            }

            if (empty($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel)
    {
        //return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
        if (empty($listSearchParams)) {
            $listSearchParams = array();
        }
        $advFilterConditionFormat = array();
        $glueOrder = array('and','or');
        $groupIterator = 0;
        foreach ($listSearchParams as $groupInfo) {
            if (empty($groupInfo)) {
                continue;
            }
            $groupConditionInfo = array();
            $groupColumnsInfo = array();
            $groupConditionGlue = $glueOrder[$groupIterator];
            foreach ($groupInfo as $fieldSearchInfo) {
                $advFilterFieldInfoFormat = array();
                $fieldName = $fieldSearchInfo[0];
                $operator = $fieldSearchInfo[1];
                $fieldValue = $fieldSearchInfo[2];
                $fieldInfo = $moduleModel->getField($fieldName);

                if (!$fieldInfo) {
                    continue;
                }

                   //Request will be having in terms of AM and PM but the database will be having in 24 hr format so converting
                    //Database format

                    if ($fieldInfo->getFieldDataType() == "time") {
                        $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                    }

                if ($fieldName == 'amount' && $fieldInfo->getFieldDataType() == 'currency') {
                    $fieldValue = CurrencyField::convertToDBFormat($fieldValue);
                }

                if ($fieldName == 'date_start' || $fieldName == 'due_date' || $fieldInfo->getFieldDataType() == "datetime") {
                    $dateValues = explode(',', $fieldValue);
                        //Indicate whether it is fist date in the between condition
                         $isFirstDate = true;
                    foreach ($dateValues as $key => $dateValue) {
                        $dateTimeCompoenents = explode(' ', $dateValue);
                        if (empty($dateTimeCompoenents[1])) {
                            if ($isFirstDate) {
                                $dateTimeCompoenents[1] = '00:00:00';
                            } else {
                                $dateTimeCompoenents[1] = '23:59:59';
                            }
                        }
                        $dateValue = implode(' ', $dateTimeCompoenents);
                        $dateValues[$key] = $dateValue;
                        $isFirstDate = false;
                    }
                    $fieldValue = implode(',', $dateValues);
                }
                    //checks if estimated weight needs to search for a range
                    if ($fieldName == 'orders_eweight') {
                        if (stristr($fieldValue, ',') !== false) {
                            $values = explode(',', $fieldValue);
                            //lower value
                            if ($values[0] != '') {
                                $fieldValue = $values[0];
                            } else {
                                $fieldValue = 0;
                            }
                            $operator = 'h';
                            //need to add the lower value myself
                            $advFilterFieldInfoFormat['columnname'] = $fieldInfo->getCustomViewColumnName();
                            $advFilterFieldInfoFormat['comparator'] = $operator;
                            $advFilterFieldInfoFormat['value'] = $fieldValue;
                            $advFilterFieldInfoFormat['column_condition'] = $groupConditionGlue;
                            $groupColumnsInfo[] = $advFilterFieldInfoFormat;

                            //higher value
                            if ($values[1] != '') {
                                $fieldValue = $values[1];
                            } else {
                                $fieldValue = 99999;
                            }
                            $operator = 'm';
                            //the higher value will be added automatically by the lines below
                        } else {
                            $operator = 'h';
                        }
                    }

                $advFilterFieldInfoFormat['columnname'] = $fieldInfo->getCustomViewColumnName();
                $advFilterFieldInfoFormat['comparator'] = $operator;
                $advFilterFieldInfoFormat['value'] = $fieldValue;
                $advFilterFieldInfoFormat['column_condition'] = $groupConditionGlue;
                $groupColumnsInfo[] = $advFilterFieldInfoFormat;
            }
            $noOfConditions = count($groupColumnsInfo);
            //to remove the last column condition
            $groupColumnsInfo[$noOfConditions-1]['column_condition']  = '';
            $groupConditionInfo['columns'] = $groupColumnsInfo;
            $groupConditionInfo['condition'] = 'and';
            $advFilterConditionFormat[] = $groupConditionInfo;
            $groupIterator++;
        }
        //We aer removing last condition since this condition if there is next group and this is the last group
        unset($advFilterConditionFormat[count($advFilterConditionFormat)-1]['condition']);
        return $advFilterConditionFormat;
    }

    public function getFilters(Vtiger_Viewer $viewer)
    {
        $db                   = PearDatabase::getInstance();
        $destination_zone_arr = [];
		$currentUser          = Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = $currentUser->getAccessibleOwnersForUser('', true, true);
        unset($accesibleAgents['agents']);
        unset($accesibleAgents['vanlines']);
        $accesibleAgents = array_keys($accesibleAgents);
		
        $result = $db->pquery("SELECT DISTINCT(za_zone), zoneadminid FROM vtiger_zoneadmin INNER JOIN vtiger_crmentity ON vtiger_zoneadmin.zoneadminid = vtiger_crmentity.crmid WHERE deleted = 0 AND agentid IN ( ". generateQuestionMarks($accesibleAgents)." )", [$accesibleAgents]);
		
        if($result && $db->num_rows($result) > 0){
            while ($row = $db->fetch_row($result)){
                $destination_zone_arr[$row['zoneadminid']] = $row['za_zone'];

            }
        }
		
        $viewer->assign('ZONE_ARR', $destination_zone_arr);
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $parentHeaderCssScriptInstances = parent::getHeaderCss($request);
        $headerCss                      = [
            '~/layouts/vlayout/modules/Orders/LDD.css',
        ];
        $cssScripts                     = $this->checkAndConvertCssStyles($headerCss);
        $headerCssScriptInstances       = array_merge($parentHeaderCssScriptInstances, $cssScripts);

        return $headerCssScriptInstances;
    }
}
