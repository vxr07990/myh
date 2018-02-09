<?php
class TariffSections_Module_Model extends Vtiger_Module_Model
{
    public function isSummaryViewSupported()
    {
        return false;
    }

    public function getSearchRecordsQuery($searchValue, $parentId=false, $parentModule=false)
    {
        return "SELECT * FROM vtiger_crmentity JOIN vtiger_tariffsections ON crmid=tariffsectionsid WHERE label LIKE '%$searchValue%' AND vtiger_crmentity.deleted = 0 AND related_tariff = '$parentId'";
    }
    
    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        if (!empty($searchValue) && empty($parentId) && empty($parentModule)) {
            $matchingRecords = Vtiger_Record_Model::getSearchResult($searchValue, $this->getName());
        } elseif ($parentId && $parentModule) {
            $db = PearDatabase::getInstance();
            $result = $db->pquery($this->getSearchRecordsQuery($searchValue, $parentId, $parentModule), array());
            $noOfRows = $db->num_rows($result);

            $moduleModels = array();
            $matchingRecords = array();
            for ($i=0; $i<$noOfRows; ++$i) {
                $row = $db->query_result_rowdata($result, $i);
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
                }
            }
        }

        return $matchingRecords;
    }
    
    public function isQuickCreateSupported()
    {
        return false;
    }
}
