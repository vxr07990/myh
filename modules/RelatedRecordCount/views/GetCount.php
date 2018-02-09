<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class RelatedRecordCount_GetCount_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $items = array();

        $record = $request->get('record');
        $mode = $request->get('mode');
        if(empty($record)){
            return $items;
        }

        if($mode=='showRecentActivities'){
            $totalUpdated = ModTracker_Record_Model::getTotalRecordCount($record);
            $items[] = array('color'=>'', 'label'=>$totalUpdated);
        }elseif($mode=='showAllComments'){
            $parentCommentModels = ModComments_Record_Model::getAllParentComments($record);
            $items[] = array('color'=>'', 'label'=>count($parentCommentModels));
        }elseif($mode=='showRelatedList'){
            $pmoduleName = $request->get('pmodule');
            $relatedModuleName = $request->get('relatedModule');

            $count = $this->getRelatedListCount($pmoduleName, $relatedModuleName, $record);

//            if($pmoduleName == "Tariffs" ){
//                if($relatedModuleName == "TariffSections"){
//                    $moduleModel = new RelatedRecordCount_Module_Model();
//                    $count = $moduleModel->getCountRelatedListTariffSections($record);
//                }elseif($relatedModuleName == "EffectiveDates"){
//                    $moduleModel = new RelatedRecordCount_Module_Model();
//                    $count = $moduleModel->getCountRelatedListEffectiveDates($record);
//                }elseif($relatedModuleName == "TariffReportSections"){
//                    $moduleModel = new RelatedRecordCount_Module_Model();
//                    $count = $moduleModel->getCountRelatedListTariffReportSections($record);
//                }
//            }
//            elseif($pmoduleName == "EffectiveDates" ){
//                if($relatedModuleName == "TariffServices"){
//                    $moduleModel = new RelatedRecordCount_Module_Model();
//                    $count = $moduleModel->getCountRelatedListTariffServices($record);
//                }
//            }
            if(!$count || $count == 'null'){
                $count = 0;
            }
            $items[] = array('color'=>'', 'label'=> $count);
        }else{
            $pmoduleName = $request->get('pmodule');
            $relatedModuleName = $request->get('relatedModule');
            $moduleModel = new RelatedRecordCount_Module_Model();
            $listSetting = $moduleModel->getSettings($pmoduleName, $relatedModuleName);

            if(empty($listSetting)){
                return $items;
            }

            if(!empty($listSetting)){
                foreach($listSetting as $k=>$setting){
                    $items[$k] = array('color'=>$setting['color'], 'label'=>$moduleModel->getCountLabel($setting, $record));
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($items);
        $response->emit();
	}

	public function getRelatedListCount($moduleName, $relatedModuleName, $parentId)
    {
        global $currentModule;
        $oldModule = $currentModule;
        $currentModule = $moduleName;
        try {
            $currentUser    = vglobal('current_user');
            $customView     = new CustomView();
            $viewName       = $customView->getDefaultFilter($relatedModuleName);
            $queryGenerator = new QueryGenerator($relatedModuleName, $currentUser);
            $queryGenerator->initForCustomViewById($viewName);
            $transformedSearchParams = [];
            $glue                    = "";
            if (count($queryGenerator->getWhereFields()) > 0 && (count($transformedSearchParams)) > 0) {
                $glue = QueryGenerator::$AND;
            }
            $queryGenerator->parseAdvFilterList($transformedSearchParams, $glue);
            $whereCondition = $queryGenerator->getWhereClause();
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
            $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
            $relationListView->set('query_generator', $queryGenerator);
            $relationListView->set('whereCondition', $whereCondition);
            $totalCount = $relationListView->getRelatedEntriesCount();
        } catch (Exception $e)
        {
        } finally {
            $currentModule = $oldModule;
        }
        return $totalCount;
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}
