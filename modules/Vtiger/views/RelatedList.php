<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

use Aws\Sdk;

class Vtiger_RelatedList_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $moduleName        = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId          = $request->get('record');
        $label             = $request->get('tab_label');
        $requestedPage     = $request->get('page');
        $currentUser = vglobal('current_user');

        $viewName = $request->get('viewname');
        if (empty($viewName)) {
            $customView     = new CustomView();
            //$viewName = $customView->getDefaultFilter($relatedModuleName);
            $viewName = $customView->getViewId($relatedModuleName);
        }

        $searchParmams = $request->get('search_params');
        if (empty($searchParmams)) {
            $searchParmams = [];
        }
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, Vtiger_Module_Model::getInstance($relatedModuleName));
        // generate where conditions based on custom view and search params
        $queryGenerator = new QueryGenerator($relatedModuleName, $currentUser);
        $queryGenerator->initForCustomViewById($viewName);

        if (empty($transformedSearchParams)) {
            $transformedSearchParams = array();
        }
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($transformedSearchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($transformedSearchParams, $glue);

        $whereCondition = $queryGenerator->getWhereClause();

        if (empty($requestedPage)) {
            $requestedPage = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $requestedPage);
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
        $orderBy           = $request->get('orderby');
        $sortOrder         = $request->get('sortorder');
        if ($sortOrder == 'ASC') {
            $nextSortOrder = 'DESC';
            $sortImage     = 'icon-chevron-down';
        } else {
            $nextSortOrder = 'ASC';
            $sortImage     = 'icon-chevron-up';
        }
        if (!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder', $sortOrder);

        }

        $relationListView->set('query_generator', $queryGenerator);
        $relationListView->set('whereCondition', $whereCondition);

        //To make smarty to get the details easily accesible
        foreach ($searchParmams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName]      = $fieldSearchInfo;
            }
        }

        $models             = $relationListView->getEntries($pagingModel);
        $links              = $relationListView->getLinks();
        $header             = $relationListView->getHeaders();

        //@TODO This should really be in Contracts, but Accounts calls it too... so wanted it here in one place for now
        $isParentView = false;
        if ($relatedModuleName == 'Contracts') {
            if ($label == 'Contracts' || $label == 'Agreements') {
                $isParentView = true;
            }
            $models = [];
            foreach ($relationListView->getEntries($pagingModel) as $contract) {
                $isParent = false;
                //just to be sure here.
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $parent_contract = $contract->get('parent_contract');
                    if ($parent_contract == '--' || !$parent_contract) {
                        //we're a parent yeah!
                        $isParent = true;
                    }

                    if ($label == 'Contracts' || $label == 'Agreements') {
                        if (!$isParent) {
                            //skip non-parent contracts
                            continue;
                        }
                    } elseif ($label == 'Sub-contracts' || $label == 'Sub-Agreements') {
                        if ($isParent) {
                            //skip parent contracts
                            continue;
                        }
                    }
                }
                $models[] = $contract;
            }

            if (getenv('INSTANCE_NAME') == 'sirva') {
//                foreach ($models as $recordId => $recordModel) {
//                    try {
//                        $nat_account_no = $models[$recordId]->get('nat_account_no');
//                        if ($nat_account_no) {
//                            $actRecord = Vtiger_Record_Model::getInstanceById($nat_account_no, 'Accounts');
//                            $recordModel->set('nat_account_no', $actRecord->get('apn'));
//                        }
//                    } catch (Exception $e) {
//                        //actually do nothing it's OK
//                    }
//
//                    try {
//                        $billing_apn = $models[$recordId]->get('billing_apn');
//                        if ($billing_apn) {
//                            $actRecord = Vtiger_Record_Model::getInstanceById($billing_apn, 'Accounts');
//                            $recordModel->set('billing_apn', $actRecord->get('apn'));
//                        }
//                    } catch (Exception $e) {
//                        //actually do nothing it's OK
//                    }
//                }
                if ($isParentView) {
                    $tempHeader = [];
                    foreach ($header as $headerName => $headerValue) {
                        if (
                            ($headerName == 'rate_per_100') ||
                            ($headerName == 'parent_contract') ||
                            ($headerName == 'billing_apn')
                        ) {
                            continue;
                        }
                        $tempHeader[$headerName] = $headerValue;
                    }
                    $header = $tempHeader;
                } else {
                    $tempLink = [];
                    //make the links say Sub-* so it's clearer.
                    foreach ($links as $index => $link) {
                        $tempItems = [];
                        foreach ($link as $id => $linkItem) {
                            if (method_exists($linkItem, 'getLabel')) {
                                $nameToReplace = vtranslate('SINGLE_'.$moduleName, $moduleName);
                                $linkItemLabel = str_replace($nameToReplace, 'Sub-'.$nameToReplace, $linkItem->getLabel());
                                $linkItem->set('linklabel', $linkItemLabel);
                            }
                            $tempItems[$id] = $linkItem;
                        }
                        $tempLink[$index] = $tempItems;
                    }
                    $links = $tempLink;
                }
            }
        }

        if($relatedModuleName == 'Media') {
            $this->initializeS3Client();
            $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            $moduleModel      = Vtiger_Module_Model::getInstance('Media');
            $cubesheetsId     = $request->get('record');
            $userModel        = Users_Record_Model::getCurrentUserModel();
            $userId           = $userModel->getId();
            $isAdmin          = $userModel->isAdminUser();
            $db               = PearDatabase::getInstance();
            $query    = "SELECT `vtiger_media`.*, `vtiger_crmentity`.createdtime
                       FROM `vtiger_media`
                       JOIN `vtiger_mediarel`
                         ON `vtiger_media`.mediaid=`vtiger_mediarel`.mediaid
                       JOIN `vtiger_crmentity`
                         ON `vtiger_media`.mediaid=`vtiger_crmentity`.crmid
                      WHERE `vtiger_mediarel`.crmid=?
                        AND deleted=0";
            $queryParams = [$cubesheetsId];
            foreach($searchParmams as $key=>$arr) {
                if(array_key_exists('fieldName', $arr) && array_key_exists($arr['fieldName'], $moduleModel->get('fields'))) {
                    $query .= " AND " . $arr['fieldName'] . '=?';
                    $queryParams[] = $arr['searchValue'];
                }
            }
            $result      = $db->pquery($query, $queryParams);
            $mediaArray = [];
            for ($i = 0; $i < $db->num_rows($result); $i++) {
                $mediaid              = $db->query_result($result, $i, 'mediaid');
                $mediaRecordModel = Vtiger_Record_Model::getInstanceById($mediaid, 'Media');
                if($mediaRecordModel->get('is_video') == 0) {
                    $mediaRecordModel->set('thumbnail', $this->generateThumbnailUrl($mediaid.'_'.$mediaRecordModel->get('thumb_file_name')));
                } else {
                    $mediaRecordModel->set('thumbnail', 'layouts/vlayout/skins/images/video_thumb.png');
                }
                $mediaRecordModel->set('createdtime', DateTimeField::convertToUserTimeZone($mediaRecordModel->get('createdtime'))->format('Y-m-d H:i:s'));
                $mediaRecordModel->set('modifiedtime', DateTimeField::convertToUserTimeZone($mediaRecordModel->get('modifiedtime'))->format('Y-m-d H:i:s'));
                $models[$mediaid] = $mediaRecordModel;
            }
        }

        $noOfEntries        = count($models);
        $relationModel      = $relationListView->getRelationModel();
        $relatedModuleModel = $relationModel->getRelationModuleModel();
        $relationField      = $relationModel->getRelationField();
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view'), 'CVID' => $viewName];
        $linkModels    = $relationListView->getListViewMassActions($linkParams);
        $viewer             = $this->getViewer($request);
        $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);
        $viewer->assign('IS_PARENT', $isParent);
        $viewer->assign('RELATED_RECORDS', $models);
        $viewer->assign('PARENT_RECORD', $parentRecordModel);
        $viewer->assign('RELATED_LIST_LINKS', $links);

        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleModel);
        $viewer->assign('RELATED_ENTIRES_COUNT', $noOfEntries);
        $viewer->assign('RELATION_FIELD', $relationField);
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            $totalCount = $relationListView->getRelatedEntriesCount();
            $pageLimit  = $pagingModel->getPageLimit();
            $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('TOTAL_ENTRIES', $totalCount);
            $viewer->assign('PERFORMANCE', true);
        }
        if($relatedModuleName == 'Calendar' && in_array($moduleName,['Campaigns','Leads','Opportunities','Orders','Accounts','Contacts','Estimates','Actuals','HelpDesk'])){
            $viewer->assign('IS_CALENDAR_STATUS_EDITABLE', 'true');
            $taskstatusArray = Calendar_Module_Model::getStatusValues('task');
            $viewer->assign('TASKSTATUS_ARRAY', $taskstatusArray);
            $eventstatusArray = Calendar_Module_Model::getStatusValues('event');
            $viewer->assign('EVENTSTATUS_ARRAY', $eventstatusArray);
        }

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('PAGING', $pagingModel);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('COLUMN_NAME', $orderBy);
        $viewer->assign('IS_EDITABLE', $relationModel->isEditable());
        $viewer->assign('IS_DELETABLE', $relationModel->isDeletable());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($relatedModuleName));
        $viewer->assign('LOCKED_VIEWS', json_encode(Vtiger_Module_Model::getLockedFilters()));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('VIEWID', $viewName);
        $viewer->assign('VIEW', $request->get('view'));

        return $viewer->view('RelatedList.tpl', $moduleName, 'true');
    }

    private function initializeS3Client() {
        $sharedConfig = [
            'region'  => 'us-east-1',
            'version' => 'latest',
            'http'    => [
                'verify' => false
            ]
        ];
        $sdk          = new Sdk($sharedConfig);
        $this->client = $sdk->createS3();
    }

    private function generateThumbnailUrl($fileName) {
        $key = getenv('INSTANCE_NAME')."_survey_images/".$fileName;
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => 'live-survey',
            'Key'    => $key
        ]);
        $req = $this->client->createPresignedRequest($cmd, '+1 minutes');
        $url = (string) $req->getUri();

        return $url;
    }

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel)
    {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
    }
}
