<?php

class EmployeeRoles_Record_Model extends Vtiger_Record_Model {
    /**
     * Static Function to get the list of records matching the search key
     *
     * @param  <String> $searchKey
     *
     * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
     */
    public static function getSearchResult($searchKey, $module = false)
    {
        $db = PearDatabase::getInstance();
        $query  = 'SELECT vtiger_crmentity.label, vtiger_crmentity.crmid, vtiger_crmentity.setype, vtiger_crmentity.createdtime, vtiger_employeeroles.emprole_class_type FROM vtiger_crmentity JOIN vtiger_employeeroles ON (vtiger_crmentity.crmid = vtiger_employeeroles.employeerolesid)WHERE vtiger_crmentity.label LIKE ? AND vtiger_crmentity.deleted = 0';
        $params = ["%$searchKey%"];
        if ($module !== false) {
            $query .= ' AND vtiger_crmentity.setype = ?';
            $params[] = $module;
        }

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if ($_REQUEST['agentId']) {
                $agentId = $_REQUEST['agentId'];
            } elseif ($_REQUEST['agentid']) {
                $agentId = $_REQUEST['agentid'];
            }
            if ($agentId) {
                //            $agentId = QueryGenerator::getAgentIdHasRecord($agentId,$module);
                if ($agentId != '') {
                    $query .= ' AND vtiger_crmentity.agentid IN (?';
                    $params[] = $agentId;

                    //Lookup corresponding VanlineManager record for the agentId if it's an AgentManager record, then include it
                    $agentRecord = Vtiger_Record_Model::getInstanceById($agentId);
                    if($agentRecord && $agentRecord->get('record_module') == 'AgentManager') {
                        $query .= ',?';
                        $params[] = $agentRecord->get('vanline_id');
                    }
                    $query .= ')';
                }
            }
        }

        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';
        $result   = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);
        $moduleModels = $matchingRecords = $leadIdsList = [];
        for ($i = 0; $i < $noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
        for ($i = 0, $recordsCount = 0; $i < $noOfRows && $recordsCount < 100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id']  = $row['crmid'];
                $moduleName = $row['setype'];
                if (!array_key_exists($moduleName, $moduleModels)) {
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel                              = $moduleModels[$moduleName];
                $modelClassName                           = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance                           = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }

        return $matchingRecords;
    }
}
