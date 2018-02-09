<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
include_once 'include/fields/DateTimeField.php';

class OrdersTask_NewLocalDispatchActuals_View extends OrdersTask_List_View
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
        parent::preProcess($request, false);
        $viewer        = $this->getViewer($request);
        $moduleName    = $request->getModule();
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView                               = new CustomView();
            $viewid                                   = $customView->getViewIdByName('Local Dispatch Actuals', $moduleName);
            //$_SESSION['lvs'][$moduleName]["viewname"] = $viewid;
            $this->viewName                           = $viewid;
        }
        
        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('VIEWID', $this->viewName);
        $viewer->assign('PAGETITLE', 'Local Dispatch Actuals');
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'ListViewPreProcess.tpl';
    }

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

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames = [
            'modules.Vtiger.resources.List',
            'modules.Vtiger.resources.RelatedList',
            'modules.CustomView.resources.CustomView',
			'modules.OrdersTask.resources.NewLocalDispatchActuals',
			'modules.OrdersTask.resources.EditExtraBlock',
            //'modules.OrdersTask.resources.CustomView',
            //'modules.OrdersTask.resources.LocalDispatch',
            //'modules.OrdersTask.resources.LocalDispatchCustomView',
            //'modules.OrdersTask.resources.jquery-splitter',
            
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/layouts/vlayout/modules/OrdersTask/resources/LocalDispatchStyle.css',
            //'~/layouts/vlayout/modules/OrdersTask/resources/jquery-splitter.css',
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    //@TODO: We need to filter by participating agent and completed
    
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();
        $pageNumber = $request->get('page');
        $orderBy    = $request->get('orderby');
        $sortOrder  = $request->get('sortorder');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $cvId = $request->get('viewname');
       
    // date format is applied on JS
        $dateFilterFrom = $request->get("from_date");
        $dateFilterTo = $request->get("to_date");
           
    // Comparators: 'LESS_OR_EQUAL' => 'm'; 'GREATER_OR_EQUAL' => 'h';

        if ($dateFilterFrom == '' && !isset($_SESSION['lvs'][$moduleName]["lda_date_from"])) {
            $dateFilterFrom = date('Y-m-d');
        } elseif ($dateFilterFrom == '' && isset($_SESSION['lvs'][$moduleName]["lda_date_from"]) && $_SESSION['lvs'][$moduleName]["lda_date_from"] != '') {
            $dateFilterFrom = vtlib_purify($_SESSION['lvs'][$moduleName]["lda_date_from"]);
        }
        
        if ($dateFilterTo == '' && !isset($_SESSION['lvs'][$moduleName]["lda_date_to"])) {
            $dateFilterTo = date('Y-m-d');
        } elseif ($dateFilterTo == '' && isset($_SESSION['lvs'][$moduleName]["lda_date_to"]) && $_SESSION['lvs'][$moduleName]["lda_date_to"] != '') {
            $dateFilterTo = vtlib_purify($_SESSION['lvs'][$moduleName]["lda_date_to"]);
        }
        
        $_SESSION['lvs'][$moduleName]["lda_date_from"] = $dateFilterFrom;
        $_SESSION['lvs'][$moduleName]["lda_date_to"] = $dateFilterTo;
        
        $searchParmams[0][] = ['disp_assigneddate', 'h', $dateFilterFrom];
        $searchParmams[0][] = ['disp_assigneddate', 'm', $dateFilterTo];
    //Added to filter Cancelled Orders
//      $searchParmams[0][]  = ['ordersstatus', 'k', 'Cancelled',];
        
        $requestedSearch = $request->get('search_params');

        if (is_array($requestedSearch[0]) && count($requestedSearch[0]) > 0) {
            foreach ($requestedSearch[0] as $requestedSeach) {
                $searchParmams[0][] = $requestedSeach;
            }
        }
        //add the dispatch_status filter if needed.
        $dispatchStatusFilter =  ['dispatch_status', 'e', 'Completed,Actuals Entered'];
        $addDispatchStatusFilter = true;
        foreach ($searchParmams[0] as $data){
            if ( $data[0] == 'dispatch_status' ) {
                $addDispatchStatusFilter = false;
            }
        }
        if($addDispatchStatusFilter){
            $searchParmams[0][] = $dispatchStatusFilter;
        }
        $viewer->assign('DATE_FROM', DateTimeField::convertToUserFormat($dateFilterFrom));
        $viewer->assign('DATE_TO', DateTimeField::convertToUserFormat($dateFilterTo));
        
        if (empty($cvId) || $cvId == '') {
           
                //If not view name exits then get it from custom view
                //This can return default view id or view id present in session
			$customView = new CustomView();
            $viewid = $customView->getViewIdByName('Local Dispatch Actuals', $moduleName);
            //$_SESSION['lvs'][$moduleName]["viewname"] = $viewid;
           
            $cvId = $viewid; //Local Dispatch custom view (filter)
        }
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
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
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view'), 'CVID' => $cvId];
        $linkModels    = $listViewModel->getListViewMassActions($linkParams);
        $pagingModel   = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('viewid', $cvId);//$request->get('viewname'));
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
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
        $listViewModel->set('search_params', $transformedSearchParams);

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
        $pagingModel->calculatePageRange($this->listViewEntries);
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
        
        $viewer->assign('LIST_VIEW_MODEL', $listViewModel);
        $viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        
        //To make smarty to get the details easily accesible
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                if (in_array($fieldSearchInfo[0], array('disp_assigneddate','service_date_from', 'date_spread', 'service_date_to'))) {
                    continue;
                }
                
                if($fieldSearchInfo[0] == 'dispatch_status'){
                    $searchValues = explode(',', $fieldSearchInfo[2]); //Hide values when all the options are selected
                    if(count($searchValues) == 2){
                        continue;
                    }
                }
                
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }
        
        
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('LocalDispatchActuals', "true");
		$ass = Vtiger_Field_Model::getInstance("assigned_employee", Vtiger_Module_Model::getInstance("OrdersTask"));
		$viewer->assign("PERSONNEL_FIELDMODEL", $ass);
                $ordersTaskInstance = Vtiger_Module_Model::getInstance($moduleName);
		$viewer->assign("PERSONNEL_ROLES", $ordersTaskInstance->getRoles());
    }
    
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
