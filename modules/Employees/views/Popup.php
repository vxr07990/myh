<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

class Employees_Popup_View extends Vtiger_Popup_View
{

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
        $role     = $request->get('role');
        $currentUser = vglobal('current_user');
        if($relatedParentModule == '') {
            $relatedParentModule = $request->get('relatedparent_module');
            $relatedParentId     = $request->get('relatedparent_id');
        }

	if (getenv('INSTANCE_NAME') == 'graebel') {
	    if (($request->get('popup_type') != '' && $request->get('popup_type') == 'get_drivers') || $request->get('src_module') == 'Trips') {
		$searchParmams[0][] = [
		    'isqualify',
		    'e',
		    '1',
		];
		$searchParmams[2][] = [
		    'employees_isdriver',
		    'e',
		    '1',
		];
		$cvId = 'Trips';
	    }
	}

	if ( $request->get('src_module') == 'ClaimsSummary' && $request->get("src_field") == "claimssummary_representative") {
		$db = PearDatabase::getInstance();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$accesibleAgents = $currentUser->getBothAccessibleOwnersIdsForUser();
		$recordOwner = (int)$request->get('owner');
		if(!in_array($recordOwner, $accesibleAgents))
			array_push($accesibleAgents, $recordOwner);

		$result = $db->pquery("SELECT * FROM vtiger_employeeroles er INNER JOIN vtiger_crmentity cr ON er.employeerolesid = cr.crmid WHERE cr.deleted = 0 AND er.emprole_class = 'Claims Adjuster' AND agentid IN ( ". generateQuestionMarks($accesibleAgents)." )", [$accesibleAgents]);

		if ($db->num_rows($result) > 0) {
				$searchParmams[0][] = [
					'employee_lastname',
					'n',
					'',
				];
			$i = 0;
			while ($arr = $db->fetch_array($result)) { //OR
				$searchParmams[1][] = [
					'employee_primaryrole',
					'e',
					$arr['emprole_desc'],
				];
				$i++;
				$searchParmams[1][] = [
					'employee_secondaryrole',
					'e',
					$arr['employeerolesid'],
				];
				$i++;
			}
		}else{ //will return 0 records because primary role is mandatory (cannot be empty)
			$searchParmams[1][] = [
				'employee_primaryrole',
				'e',
				'',
			];
		}
	}

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
        if(($sourceModule != 'Leads' || $sourceModule != 'Opportunities' || $sourceModule != 'Orders') && $moduleName != 'Employees'){
        $isRecordExists          = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);
        if ($isRecordExists) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        } elseif ($isRecordExists === NULL) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        }
        }

        if (!empty($relatedParentModule) && !empty($relatedParentId) && $relatedParentModule != 'EmployeeRoles') {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel     = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label);
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
            $listViewModel = Employees_ListView_Model::getInstanceForPopup($moduleName, $cvId);
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

        if($sourceModule == 'Accounts' && $moduleName =='EmployeeRoles'){
            $searchRole = true;
        }

        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        //$transformedSearchParams = Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
        //$listViewModel->set('search_params', $transformedSearchParams);
        if (getenv('IGC_MOVEHQ')) {
            if (!empty($relatedParentModule) && !empty($relatedParentId) && $relatedParentModule != 'EmployeeRoles') {
                $this->listViewHeaders = $listViewModel->getHeaders();
                $models                = $listViewModel->getEntries($pagingModel);
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
            } elseif ($sourceField == 'personnelID' ) {
                $this->listViewHeaders = $listViewModel->getListViewHeaders();
//                $aux = $listViewModel->getListViewEntries($pagingModel);
                $ordersTaksRecord = Vtiger_Record_Model::getInstanceById($sourceRecord, 'OrdersTask');
                $assignedDate = $ordersTaksRecord->get('disp_assigneddate');
                $ordersTaksInstance = Vtiger_Module_Model::getInstance('OrdersTask');
                $roleId = $request->get('roleId') ? $request->get('roleId') : '' ;
                $employees = $ordersTaksInstance->getEmployeesByDateAndRole($assignedDate, $assignedDate, $roleId, '', 'employeePopup' );
                $this->listViewEntries = $employees[$assignedDate];
            } elseif ($sourceField == 'driver_id' ) {
                $this->listViewHeaders = $listViewModel->getListViewHeaders();
                $agentId = $request->get('agentId');
                $firstLoadDate = DateTimeField::convertToDBFormat($request->get('date'));
                if($firstLoadDate == '') {
                    $firstLoadDate = '1970-01-01';
                }
                $ordersTaksInstance = Vtiger_Module_Model::getInstance('OrdersTask');
                $roleId = Vtiger_Module_Model::getInstance('EmployeeRoles')->getRoleIdFromRoleClassAndAgentId('Driver',$agentId);
                if($roleId){
                    $employees = $ordersTaksInstance->getEmployeesByDateAndRole($firstLoadDate, $firstLoadDate, $roleId, '', 'employeePopup' );
                    $this->listViewEntries = $employees[$firstLoadDate];
                }else{
                    $this->listViewEntries = [];
                }
            } else {
                if(!empty($role) && $sourceModule =='Accounts'){
                    $listViewModel->set('employeeRoles', $role);
                }else{
                $listViewModel->set('employeeRoles', $relatedParentId);
                }
                $this->listViewHeaders = $listViewModel->getListViewHeaders();
                $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
            }
        } else {
            if (!empty($relatedParentModule) && !empty($relatedParentId)) {
                $this->listViewHeaders = $listViewModel->getHeaders();
                $models                = $listViewModel->getEntries($pagingModel);
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
        }
        // If there are no related records with parent module then, we should show all the records
        if (!$parent_related_records && !empty($relatedParentModule) && !empty($relatedParentId) && $relatedParentModule != 'EmployeeRoles') {
            $relatedParentModule = NULL;
            $relatedParentId     = NULL;
            $listViewModel       = Employees_ListView_Model::getInstanceForPopup($moduleName, $cvId);
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

        //OT3313
        //@TODO intead of this use NewLoadLocalDispatch function getOnNoticeEmployees
//        $this->getDriversOnNotice($this->listViewEntries);
        //----- <-- what is this for? \_--_/

		$viewer->assign('POPUPTYPE',$request->get('popup_type'));
		if ($sourceField == 'driver_id' ) {
                    $date = DateTimeField::convertToDBFormat($request->get('date'));
                }else{
                    $date = DateTimeField::convertToDBFormat(date('m-d-Y'));
                }
		$entries = $this->getOnNoticeEmployees($date,$this->listViewEntries);

        $noOfEntries = count($this->listViewEntries);
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
        $viewer->assign('LISTVIEW_ENTRIES', $entries);
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

        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('VIEWID', $cvId);
        $viewer->assign('MULTI_SELECT', $multiSelectMode);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }


    public function getOnNoticeEmployees($date, $entries){

        $moduleModel = Vtiger_Module_Model::getInstance('OrdersTask');
        $onNoticeEmployees = $moduleModel->getOutOfServiceEmployeesByDate($date, $date, 'On Notice');
        if(!empty($onNoticeEmployees)){
            $onNoticeEmployees = $onNoticeEmployees[$date];

            foreach ($entries as $entry) {
                if(in_array($entry->getId(), $onNoticeEmployees)){
                    $entry->set('on_notice', true);
                }else{
                    $entry->set('on_notice', false);
                }
            }
        }

        return $entries;
    }
}
