<?php

class AgentCompensationGroup_Popup_View extends Vtiger_Popup_View {
    /*
         * Function to initialize the required data in smarty to display the List View Contents
         */
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        $moduleName          = $this->getModule($request);
        $pageNumber          = $request->get('page');
        $sourceModule        = $request->get('src_module');
        $sourceField         = $request->get('src_field');
        $searchValue         = $request->get('search_value');
        $agentid     = $request->get('agentId');
        if($sourceField == 'agentcompgr_tariffcontract' && $sourceModule == 'AgentCompensationGroup') {
            global $adb;
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', $pageNumber);

            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            $listViewRecordModels=array();
            $queryTariffs="select tariffsid, tariff_name, setype from `vtiger_tariffs`
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_tariffs.tariffsid
                        WHERE deleted=0 AND (vtiger_tariffs.`tariff_status` != 'Inactive' OR vtiger_tariffs.`tariff_status` IS NULL)
                        ";
            $queryTariffsManager="SELECT tariffmanagerid, tariffmanagername, setype FROM vtiger_tariffmanager
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_tariffmanager.tariffmanagerid
                        WHERE deleted=0";

            $params=array();
            if($agentid != '') {
                $queryTariffs .=" AND agentid = ?";
                $params[]=$agentid;
            }

            if($searchValue !='') {
                $queryTariffs .=" AND tariff_name LIKE ?";
                $queryTariffsManager .=" AND tariffmanagername LIKE ?";
                $params[]="%{$searchValue}%";
                $params[]="%{$searchValue}%";
            }
            $query="SELECT * FROM (({$queryTariffs}) UNION ({$queryTariffsManager})) as temp_table";
            $query .= " LIMIT $startIndex,".($pageLimit+1);

            $rs=$adb->pquery($query,$params);
            if($adb->num_rows($rs)>0) {
                while($row=$adb->fetch_array($rs)) {
                    $tariffsid = $row['tariffsid'];
                    $listViewRecordModels[$tariffsid]=Vtiger_Record_Model::getInstanceById($tariffsid);
                }
            }
            $viewer->assign('LISTVIEW_ENTRIES', $listViewRecordModels);
            $viewer->assign('SOURCE_MODULE', $sourceModule);
            $viewer->assign('SOURCE_FIELD', $sourceField);
            $viewer->assign('PAGING_MODEL', $pagingModel);
            $viewer->assign('MODULE', $moduleName);
            $viewer->assign('AGENTID', $agentid);
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }else {
            parent::initializeListViewContents($request,$viewer);
        }
    }

    /**
     * Function to get listView count
     *
     * @param Vtiger_Request $request
     */
    public function getListViewCount(Vtiger_Request $request)
    {
        $moduleName          = $this->getModule($request);
        $pageNumber          = $request->get('page');
        $sourceModule        = $request->get('src_module');
        $sourceField         = $request->get('src_field');
        $searchValue         = $request->get('search_value');
        $agentid     = $request->get('agentId');
        if($sourceField == 'agentcompgr_tariffcontract' && $sourceModule == 'AgentCompensationGroup') {
            global $adb;
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', $pageNumber);

            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            $listViewRecordModels=array();
            $queryTariffs="select tariffsid, tariff_name, setype from `vtiger_tariffs`
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_tariffs.tariffsid
                        WHERE deleted=0
                        ";
            $queryTariffsManager="SELECT tariffmanagerid, tariffmanagername, setype FROM vtiger_tariffmanager
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_tariffmanager.tariffmanagerid
                        WHERE deleted=0";

            $params=array();
            if($agentid != '') {
                $queryTariffs .=" AND agentid = ?";
                $params[]=$agentid;
            }

            if($searchValue !='') {
                $queryTariffs .=" AND tariff_name LIKE ?";
                $queryTariffsManager .=" AND tariffmanagername LIKE ?";
                $params[]="%{$searchValue}%";
                $params[]="%{$searchValue}%";
            }
            $query="SELECT count(*) as count FROM (({$queryTariffs}) UNION ({$queryTariffsManager})) as temp_table";
            $rs=$adb->pquery($query,$params);
            return $queryResult = $adb->query_result($rs, 0, 'count');
        }else{
            return parent::getListViewCount($request);
        }
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
}
