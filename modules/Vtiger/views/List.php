<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Vtiger_List_View extends Vtiger_Index_View
{
    protected $listViewEntries = false;
    protected $listViewCount   = false;
    protected $listViewLinks   = false;
    protected $listViewHeaders = false;
    protected $listViewHeadersExtra = false;
    protected $disallowModules = [
        'Cubesheets',
        'Surveys'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $viewer        = $this->getViewer($request);
        $moduleName    = $request->getModule();
        $this->checkAllowedModule($moduleName);
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $viewer->assign('LOCKED_VIEWS', json_encode(Vtiger_Module_Model::getLockedFilters()));
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView     = new CustomView();
            $this->viewName = $customView->getViewId($moduleName);
        }
        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('VIEWID', $this->viewName);
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
            "modules.$moduleName.resources.List",
            'modules.CustomView.resources.CustomView',
            "modules.$moduleName.resources.CustomView",
            "libraries.jquery.ckeditor.ckeditor",
            "libraries.jquery.ckeditor.adapters.jquery",
            "modules.Emails.resources.MassEdit",
            "modules.Vtiger.resources.CkEditor",
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
        if(empty($cvId)){
            $cvId = 0;
        }
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
        $pagingModel->set('viewid', $request->get('viewname'));
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
        if (!$this->listViewHeaders) {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewHeadersExtra = $listViewModel->getListViewExtraHeaders();
        }
        // TODO: if two fields have the same name in different modules, bad stuff will happen
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule(), $this->listViewHeaders);
        $listViewModel->set('search_params', $transformedSearchParams);
        //To make smarty to get the details easily accesible
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }
        if (!$this->listViewEntries) {
            $entries = $listViewModel->getListViewEntries($pagingModel);
            $moduleInstance = Vtiger_Module::getInstance($moduleName);
            $hostTable = $moduleInstance->basetable;
            $hostKey = $moduleInstance->basetableid;
            $db = &PearDatabase::getInstance();
            foreach ($entries as $entry) {
                $id = $entry->get('id');
                foreach ($this->listViewHeadersExtra as $headerField) {
                    $fieldName = $headerField['fieldname'];
                    $tableName = $headerField['tablename'];
                    $linkColumn = $headerField['linkcolumn'];
                    $whereColumn = $headerField['wherecolumn'];
                    $whereValue = $headerField['wherevalue'];
                    $res = $db->pquery("SELECT $fieldName FROM $hostTable
                                          INNER JOIN $tableName ON($hostTable.$hostKey=$tableName.$linkColumn)
                                          WHERE $hostTable.$hostKey=$id AND $tableName.$whereColumn=?",
                                       [$whereValue]);
                    $value = [];
                    while ($row = $res->fetchRow()) {
                        $rawValue = $row[$fieldName];
                        $displayValue = $this->getCustomViewDisplayName($rawValue, $headerField);
                        $value[] =$displayValue;
                    }
                    $entry->set($headerField['label'], implode(',', $value));
                }
            }
            $this->listViewEntries = $entries;
        }
        $noOfEntries = count($this->listViewEntries);
        //file_put_contents('logs/devLog.log', "\n NoOfEntries: $noOfEntries", FILE_APPEND);
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
        $viewer->assign('EXTRA_HEADERS', $this->listViewHeadersExtra);
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
            $viewer->assign('LISTVIEW_COUNT', $totalCount);
        }
        $viewer->assign('LIST_VIEW_MODEL', $listViewModel);
        $viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
    }

    public function getCustomViewDisplayName($rawValue, $fieldData)
    {
        return $rawValue;
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

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel, $headers)
    {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel, $headers);
    }

    protected function checkAllowedModule($moduleName) {
        //This is a stubbed out function that will eventually be driven from a database or from activation like user allowed module views.
        if (in_array($moduleName, $this->disallowModules)) {
            throw new Exception(vtranslate('LBL_VIEW_DISABLED'));
        }
        return null;
    }
}
