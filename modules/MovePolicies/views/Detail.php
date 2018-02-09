<?php 

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MovePolicies_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }



    public function postProcess(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);


        $tariffItems = $recordModel->getTariffItems();
        unset($tariffItems['items_count']);
        
        $miscTariffItems = $recordModel->getMiscTariffItems();

        $viewer = $this->getViewer($request);
        $viewer->assign('TARIFF_ITEMS', $tariffItems);
        $viewer->assign('MISC_TARIFF_ITEMS', $miscTariffItems);

        parent::postProcess($request);
    }
    
    /**
     * Function returns recent changes made on the record
     *
     * @param Vtiger_Request $request
     */
    public function showRecentActivities(Vtiger_Request $request)
    {
        $parentRecordId = $request->get('record');
        $pageNumber     = $request->get('page');
        $limit          = $request->get('limit');
        $moduleName     = $request->getModule();
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $recentActivities = $this->getUpdates($parentRecordId, $pagingModel);
        $recentActivitiesDetails = $this->getDetails($parentRecordId);
        $pagingModel->calculatePageRange($recentActivities);
        if ($pagingModel->getCurrentPage() == ModTracker_Record_Model::getTotalRecordCount($parentRecordId) / $pagingModel->getPageLimit()) {
            $pagingModel->set('nextPageExists', false);
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('RECENT_ACTIVITIES_DETAILS', $recentActivitiesDetails);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        echo $viewer->view('RecentActivities.tpl', $moduleName, 'true');
    }
    
    protected function getUpdates($parentRecordId, $pagingModel)
    {
        $db = PearDatabase::getInstance();
        $recordInstances = array();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery = "SELECT * FROM vtiger_modtracker_basic WHERE crmid = ? ".
                                        " ORDER BY changedon DESC LIMIT $startIndex, $pageLimit";

        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);

        for ($i=0; $i<$rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstance =  new ModTracker_Record_Model();
            $recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
            $recordInstances[] = $recordInstance;
        }
        return $recordInstances;
    }
        
    protected function getDetails($parentRecordId)
    {
        $db = PearDatabase::getInstance();
        $allDetailFields = array();
        $selectedDetailFields = array();
        //custom fields details
        $CustomFieldsDetailQuery = "SELECT * FROM vtiger_modtracker_detail JOIN vtiger_modtracker_basic ".
                                    "ON vtiger_modtracker_basic.id=vtiger_modtracker_detail.id WHERE crmid = ?";

        $resultCF = $db->pquery($CustomFieldsDetailQuery, array($parentRecordId));
        $rowsCF = $db->num_rows($resultCF);
        for ($j=0; $j<$rowsCF;$j++) {
            $allDetailFields[] = $db->query_result_rowdata($resultCF, $j);
        }
        //filter custom fields
        for ($j=0; $j<$rowsCF;$j++) {
            $exploded = explode('_', $allDetailFields[$j]['fieldname']);
            $itemId = $exploded[2];
            $query = "SELECT item_des FROM vtiger_movepolicies_items WHERE policies_id = ? and item_id = ?";
            $result = $db->pquery($query, array($parentRecordId, $itemId));
            if ($result && $db->num_rows($result) > 0) {
                $row = $db->query_result_rowdata($result, 0);
            }
            if ($exploded[1]=='auth') {
                $allDetailFields[$j]['label'] = $row[0].' - Authorization';
                $selectedDetailFields[] = $allDetailFields[$j];
            } elseif ($exploded[1]=='authlimit') {
                $allDetailFields[$j]['label'] = $row[0].' - Authorization Limits';
                $selectedDetailFields[] = $allDetailFields[$j];
            } elseif ($exploded[1]=='remarks') {
                $allDetailFields[$j]['label'] = $row[0].' - Remarks';
                $selectedDetailFields[] = $allDetailFields[$j];
            }
        }
        return $selectedDetailFields;
    }
}
