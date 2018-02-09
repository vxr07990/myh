<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class OrdersTask_List_View extends Vtiger_List_View
{
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();
        $pageNumber = $request->get('page');
        $orderBy    = $request->get('orderby');
        $sortOrder  = $request->get('sortorder');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $cvId = $request->get('viewname');
        $searchParmams = array();

        $requestedSearch = $request->get('search_params');

        if (is_array($requestedSearch[0]) && count($requestedSearch[0]) > 0) {
            foreach ($requestedSearch[0] as $requestedSeach) {
                $searchParmams[0][] = $requestedSeach;
            }
        }


        if (empty($cvId) || $cvId == '') {
            $cvId = $this->getLDViewName();
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

        //To make smarty to get the details easily accesible
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }


        $viewer->assign('SEARCH_DETAILS', $searchParmams);
    }

        //VGS - Conrado: We need to hack this function to allow searching on "others" module fields :/
    //Overriden this here create the least impact

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel)
    {
        if (empty($listSearchParams)) {
            $listSearchParams = array();
        }
        $advFilterConditionFormat = array();
        $glueOrder = array('and', 'or');
        $groupIterator = 0;

        $moduleModels = array(
            Vtiger_Module_Model::getInstance('Orders'),
            Vtiger_Module_Model::getInstance('Trips'),
            Vtiger_Module_Model::getInstance('Estimates'),
        );


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
                    foreach ($moduleModels as $relatedModuleModel) {
                        $fieldInfo = $relatedModuleModel->getField($fieldName);

                        if ($fieldInfo) {
                            break;
                        }

                        continue;
                    }
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

                $advFilterFieldInfoFormat['columnname'] = $fieldInfo->getCustomViewColumnName();
                $advFilterFieldInfoFormat['comparator'] = $operator;
                $advFilterFieldInfoFormat['value'] = $fieldValue;
                $advFilterFieldInfoFormat['column_condition'] = $groupConditionGlue;
                $groupColumnsInfo[] = $advFilterFieldInfoFormat;
            }
            $noOfConditions = count($groupColumnsInfo);
            //to remove the last column condition
            $groupColumnsInfo[$noOfConditions - 1]['column_condition'] = '';
            $groupConditionInfo['columns'] = $groupColumnsInfo;
            $groupConditionInfo['condition'] = 'and';
            $advFilterConditionFormat[] = $groupConditionInfo;
            $groupIterator++;
        }
        //We aer removing last condition since this condition if there is next group and this is the last group
        unset($advFilterConditionFormat[count($advFilterConditionFormat) - 1]['condition']);
        return $advFilterConditionFormat;
    }
    
    function getLDViewName(){

        $viewid = Vtiger_Cache::get('local_operation_tasks_view','view_id');

        if(!$viewid){
            global $current_user;
            $moduleName = 'OrdersTask';
            $customView = new CustomView();
            $viewid = $customView->getCustomViewForUser($moduleName,$_REQUEST['view'],$current_user->id);
            if($viewid == 0 || $viewid== ''){
                $viewid = $customView->getViewIdByName('All Local Operation Task', $moduleName, true);                    
            }
            $_SESSION['lvs'][$moduleName]["viewname"] = $viewid;
                
            Vtiger_Cache::set('local_operation_tasks_view','view_id', $viewid);
        }

        return $viewid;
    }
}
