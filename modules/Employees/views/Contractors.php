<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

class Employees_Contractors_View extends Vtiger_List_View
{
    protected $default_local_dispatch_view = 68; //Harcode the filter that show the local dispatch tasks

    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $request->set('viewname', $this->default_local_dispatch_view);
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
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
        $viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'ListViewPreProcess.tpl';
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
        parent::postProcess($request);
    }

    /*
     * Function to initialize the required data in smarty to display the List View Contents
     */
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();
        $cvId       = $this->default_local_dispatch_view;
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
        //$searchParmams = $request->get('search_params');
        if (empty($searchParmams)) {
            $searchParmams = [];
        }
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
        if (!$this->listViewEntries) {
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
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
}
