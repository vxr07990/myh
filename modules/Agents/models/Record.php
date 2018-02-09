<?php

class Agents_Record_Model extends Vtiger_Record_Model
{
    /**
     * Static Function to get the list of records matching the search key
     * @param <String> $searchKey
     * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
     */
    public static function getSearchResult($searchKey, $module=false)
    {
        $db = PearDatabase::getInstance();

        $query = 'SELECT label, crmid, setype, createdtime, agent_number FROM vtiger_crmentity JOIN `vtiger_agents` ON `vtiger_crmentity`.crmid=`vtiger_agents`.agentsid WHERE (label LIKE ? OR agent_number LIKE ?) AND vtiger_crmentity.deleted = 0';
        $params = array("%$searchKey%", "%$searchKey%");

        if ($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }
        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';

        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);

        $moduleModels = $matchingRecords = $leadIdsList = array();
        for ($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);

        for ($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
            if(strlen($row['label'])<20){
                $row['label'] = $row['label'].' ('.$row['agent_number'].')';
            }
            else{
                $row['label'] ='('.$row['agent_number'].') '.$row['label'];
            }

            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id'] = $row['crmid'];
                $moduleName = $row['setype'];
                if (!array_key_exists($moduleName, $moduleModels)) {
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel = $moduleModels[$moduleName];
                $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }

        return $matchingRecords;
    }

    //We could have built this correctly using two fieldnames in the entitiy identifier!  ... but we can also override it!
    public function getDisplayName()
    {
        return '('. $this->get('agent_number') . ') '.$this->get('agentname');
        //return parent::getDisplayName();
    }

    public function getAddress()
    {
        return $this->get('agent_address1') . ', ' . $this->get('agent_city') . ', ' . $this->get('agent_state') . ', ' . $this->get('agent_zip');
    }

    public function getAllBranchDefaults($record = false)
    {
        //we need to get the branch defaults.
        //@TODO try and do this better jez.
        $returnArray = [];

        if (!$record) {
            $record = $this->getId();
        }

        $vendorCRM = CRMEntity::getInstance('Agents');
        $venderAgreementsModuleModel = Vtiger_Module_Model::getInstance('BranchDefaults');
        $relatedList = $vendorCRM->get_related_list($record, '', $venderAgreementsModuleModel->getId());

        $db = PearDatabase::getInstance();
        if ($db) {
            $res = $db->pquery($relatedList['query']);
            if (method_exists($res, 'fetchRow')) {
                while ($row = $res->fetchRow()) {
                    $returnArray[$row['crmid']] = Vtiger_Record_Model::getInstanceById($row['crmid']);
                }
            }
        }
        return $returnArray;
    }
}
