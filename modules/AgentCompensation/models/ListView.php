<?php
class AgentCompensation_ListView_Model extends Vtiger_ListView_Model{
    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();

        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');

        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);

        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if (!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        if (empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $orderBy = 'modifiedtime';
            $sortOrder = 'DESC';
        }

        if (!empty($orderBy)) {
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
            if ($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE) {
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
            }
        }
        $listQuery = $this->getQuery();


        $sourceModule = $this->get('src_module');
        if (!empty($sourceModule)) {
            if (method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
                if (!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        if (!empty($orderBy)) {
            if ($orderByFieldModel && $orderByFieldModel->isReferenceField()) {
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach ($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();

                    $columnList = array();
                    foreach ($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
                    }
                    if (count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0], 'last_name'=>$columnList[1]), 'Users', '').' '.$sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    }
                }
                $listQuery .= ' ORDER BY '. implode(',', $referenceNameFieldOrderBy);
            } elseif (!empty($orderBy) && $orderBy === 'smownerid') {
                $fieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $moduleModel);
                if ($fieldModel->getFieldDataType() == 'owner') {
                    $orderBy = 'COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname)';
                }
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            } else {
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            }
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if (empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

        $listQuery .= " LIMIT $startIndex,".($pageLimit+1);
        $params =[];
        if($this->get('src_field') == 'agentcompensationid' && $sourceModule == 'Actuals' ){
            $listQuery = $this->buildQueryForAgentCompensation($listQuery,$params);
        }

        $listResult = $db->pquery($listQuery, $params);
        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult);

        //this is done at the return for different versions
        $pagingModel->calculatePageRange($listViewEntries);

        if ($db->num_rows($listResult) > $pageLimit) {
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }

        $index = 0;
        foreach ($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            //agent owner display
            try {
                $agentRecordModel   = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'AgentManager');
                $vanlineRecordModel = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'VanlineManager');
                $displayValue       = $agentRecordModel->get('agency_name')?'('.$agentRecordModel->get('agency_code').') '.$agentRecordModel->get('agency_name'):$vanlineRecordModel->get('vanline_name');
                if ($displayValue != null) {
                    $record['agentid'] = $displayValue;
                } else {
                    $record['agentid'] = '--';
                }
            } catch (Exception $e) {
                $record['agentid'] = '--';
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        return $listViewRecordModels;

    }
    public static function buildQueryForAgentCompensation($listQuery,&$params){
        $business_line = $_REQUEST['business_line'];
        $billing_type = $_REQUEST['billing_type'];
        $authority = $_REQUEST['authority'];
        $contract = $_REQUEST['contract'];
        $tariff = $_REQUEST['tariff'];
        $effective_date = $_REQUEST['effective_date'];

        $join = " INNER JOIN vtiger_agentcompensationgroup ON vtiger_agentcompensationgroup.agentcompensation_id = vtiger_crmentity.crmid ";
        $conditions = " agentcompgr_status = 'Active' AND ";
        if(!empty($business_line)){
            $conditions .= " CONCAT(' |##| ',agentcompgr_businessline,' |##| ') LIKE ? AND ";
            $params[] = "% |##| ".trim($business_line)." |##| %";
        }
        if(!empty($billing_type)){
            $conditions .= " CONCAT(' |##| ',agentcompgr_billingtype,' |##| ') LIKE ? AND ";
            $params[] = "% |##| ".trim($billing_type)." |##| %";
        }
        if(!empty($authority)){
            $conditions .= " CONCAT(' |##| ',agentcompgr_authority,' |##| ') LIKE ? AND ";
            $params[] = "% |##| ".trim($authority)." |##| %";
        }
        if(!empty($contract)){
            $conditions .= " agentcompgr_type = 'Contracts' AND agentcompgr_tariffcontract = ? AND ";
            $params[] = $contract;
        }
        if(!empty($tariff)){
            $conditions .= " agentcompgr_type = 'Tariffs' AND agentcompgr_tariffcontract = ? AND ";
            $params[] = $tariff;
        }
        if(!empty($effective_date)){
            $effective_date = DateTimeField::convertToDBFormat($effective_date);
            $conditions .= " agentcompgr_effdatefrom <= ? AND agentcompgr_effdateto >= ? AND ";
            $params[] = $effective_date;
            $params[] = $effective_date;
        }

        if(!empty($conditions)){
            $pos = stripos($listQuery, 'where');
            if ($pos) {
                $split = spliti('where', $listQuery);
                $listQuery = $split[0] .$join. ' WHERE '.$conditions . $split[1];
            }
        }
        
        return $listQuery;
    }
}