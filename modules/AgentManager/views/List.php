<?php

class AgentManager_List_View extends Vtiger_List_View
{
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
        //file_put_contents('logs/log2.log', print_r($this->listViewEntries, true));
        $noOfEntries = count($this->listViewEntries);
        /*$db          = PearDatabase::getInstance();
        $userId      = $currentUser->get('id');
        $sql         = "SELECT  `vtiger_role`.depth
                FROM  `vtiger_role`
                JOIN  `vtiger_user2role` ON  `vtiger_role`.roleid =  `vtiger_user2role`.roleid
                WHERE  `vtiger_user2role`.userid =?";
        $result      = $db->pquery($sql, [$userId]);
        $row         = $result->fetchRow();
        $depth       = $row[0];
        if ($depth == 6) {
            $sql                 = "SELECT agency_code FROM `vtiger_user2agency` WHERE userid = ?";
            $result              = $db->pquery($sql, [$userId]);
            $row                 = $result->fetchRow();
            $agencyId            = $row[0];
            $tempListViewEntries = [];
            foreach ($this->listViewEntries as $key => $value) {
                if ($key == $agencyId) {
                    $tempListViewEntries[$key] = $value;
                }
            }
            $this->listViewEntries = $tempListViewEntries;
            $pagingModel->calculatePageRange($this->listViewEntries);
        }*/
        $viewer->assign('MODULE', $moduleName);
        if (!$this->listViewLinks) {
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }

        //so remove the button to ADD and Duplicate agencies IFF the user is sales manager or below
        if ($currentUser->getUserRoleDepth() >= 4) {
            $this->listViewLinks['LISTVIEWBASIC'] = [];
            $tempListViewMassAction = [];
            foreach ($linkModels['LISTVIEWMASSACTION'] as $linkModel) {
                if ($linkModel->linklabel != 'LBL_DUPLICATE') {
                    $tempListViewMassAction[] = $linkModel;
                }
            }
            $linkModels['LISTVIEWMASSACTION'] = $tempListViewMassAction;
        }

        //file_put_contents('logs/devLog.log', "\n listViewLinks : ".print_r($this->listViewLinks,true),FILE_APPEND);
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
        //file_put_contents('logs/devLog.log', "\n LISTVIEW_ENTRIES_COUNT: ".$noOfEntries, FILE_APPEND);
        //if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
        //file_put_contents('logs/devLog.log', "\n IN PAGE COUNT", FILE_APPEND);
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
        //}
        $viewer->assign('LIST_VIEW_MODEL', $listViewModel);
        $viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
    }
}
